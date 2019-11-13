<?php

namespace App\Http\Controllers\Api;

use App\Models\Goods;
use App\Models\GoodsBanner;
use App\Models\OrderGoods;
use App\Models\Collection;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Http\Requests\GoodsRequest;
use Illuminate\Support\Facades\DB;


class GoodsController extends Controller
{
    // 添加商品
    public function addGood(Request $request){
        $goods = Goods::create($request->all());
        $banners = $request->get('banners');
        $stocks = $request->get('stocks');

        // 规格库存
        foreach ($stocks as $key => $item) {
            $item['goods_id'] = $goods['id'];

            Stock::query()->updateOrCreate([
                'label_id' => $item['label_id'],
                'goods_id' => $goods['id'],
            ],$item);
        }

        // 商品图片
        if (!empty($banners)){
            // 1、Eloquent ORM 方法插入多条
            // number和goods_id 这2个字段一致则更新，否则新增
            foreach ($banners as $key => $item) {
                $item['goods_id'] = $goods['id'];

                GoodsBanner::query()->updateOrCreate([
                    'number' => $item['number'],
                    'goods_id' => $goods['id'],
                ],$item);
            }
            // 2、查询构造器方法插入多条（会自动添加创建和更新时间）
            // DB::table('goodbanners')->insert($banners);
        }

        return $this->message('商品新增成功');
    }

    // 修改商品信息
    public function editGood(Request $request){
        $id = $request->get('id');

        $goods = Goods::findOrFail($id);
        $banners = $request->get('banners');
        $stocks = $request->get('stocks');

        if (!empty($stocks)){
            $oldStocks = Stock::where('goods_id', $id)->get();
            $oldStocksNum = array_column($oldStocks->toArray(), 'label_id');

            $newStocksNum = array_column($stocks, 'label_id');
            // 如果新接收的list没有旧的list的num，则要删除
            foreach ($oldStocksNum as $item) {
                if (!in_array($item, $newStocksNum)) {
                    Stock::where(['goods_id'=> $id, 'label_id' => $item])->delete();
                }
            }
            foreach ($stocks as $item) {
                Stock::query()->updateOrCreate([
                    'label_id' => $item['label_id'],
                    'goods_id' => $goods['id'],
                ],$item);
            }
        }

        if (!empty($banners)){
            $oldBanners = GoodsBanner::where('goods_id', $id)->get();
            $oldBannersNum = array_column($oldBanners->toArray(), 'number');

            $newBannersNum = array_column($banners, 'number');

            // 如果新接收的list没有旧的list的num，则要删除
            foreach ($oldBannersNum as $item) {
                if (!in_array($item, $newBannersNum)) {
                    GoodsBanner::where(['goods_id'=> $id, 'number' => $item])->delete();
                }
            }
            foreach ($banners as $key => $item) {
                GoodsBanner::query()->updateOrCreate([
                    'number' => $item['number'],
                    'goods_id' => $goods['id'],
                ],$item);
            }
        }
        $goods->update($request->all());

        return $this->message('商品修改成功！');
    }

    // 修改标签
    public function editBanners($id, $newBanners){
        // 旧的标签值
        $oldBanners = GoodsBanner::where('goods_id', $id)->get();
        // 排序
        sort($oldBanners);
        sort($newBanners);

        if ($oldBanners != $newBanners) {
            // 先删除数据
            $oldBanners->delete();
            // 再添加新的数据
            foreach($newBanners as $tag){
                $tag = DB::table('tags')->insert([
                    'tag' => $tag,
                    'article_id' => $id,
                    'classify' => $classify
                ]);
            }
        }
    }

    // 商品详情
    public function detail(Request $request) {
        $goods = Goods::findOrFail($request->id);
        // 收藏数量
        $goods->likeCount = (new CollectionController())->likeCount($request->id);
        $goods->banners = GoodsBanner::where('goods_id', $goods['id'])->get();
        $goods->stocks = Stock::where('goods_id', $goods['id'])->get();

        foreach($goods['banners'] as $item) {
            $item['image'] = $item['url'];
            $item['uid'] = -$item['number'];
        }
        // 销量
        $goods->sales = OrderGoods::where('goods_id', $request->id)->sum('count');
        // 购买次数
        $goods->buys = OrderGoods::where('goods_id', $request->id)->count();

        // 如果有传用户id则查询是否收藏
        
        $collect = Collection::where(['goods_id' => $goods['id'], 'user_id'=> $request->user_id])->first();
        $goods->collect = $collect ? true : false; 

        return $this->success($goods);
    }

    // 下架商品
    public function delete(Request $request){
        Goods::findOrFail($request->id)->delete();
        return $this->message('商品下架成功');
    }

    // 恢复下架商品
    public function restored(Request $request){
        Goods::withTrashed()->findOrFail($request->id)->restore();
        return $this->message('商品恢复成功');
    }


    //返回商品列表 10篇为一页
    public function goodsList(Request $request){
        // 需要显示的字段
        $data = ['id', 'title', 'desc', 'classify', 'clicks', 'created_at', 'deleted_at'];

        // 获取所有，包括软删除
        if($request->all)
            $goods = Goods::withTrashed()->orderBy('created_at', 'desc')->paginate(10, $data);
        else if ($request->classify)
            $goods = Goods::whereClassify($request->classify)->orderBy('created_at', 'desc')->get();
        else
            $goods = Goods::orderBy('created_at', 'desc')->paginate(10, $data);

        // 拿到商品封面图
        foreach($goods as $item){
            $banner = GoodsBanner::where('goods_id', $item['id'])->orderBy('active', 'desc')->first();

            $item->likeCount = (new CollectionController())->likeCount($item->id);
            $item->stocks = Stock::where('goods_id', $item['id'])->get();
            $item->defaultBanner = $banner['url'];
            $item->buys = 0;
        }

        // // 拿回文章的标签和评论总数
        // foreach($articles as $item){
        //     $tag = Tag::where('article_id', $item->id)->get(['tag']);
        //     $item->view_count = visits($item)->count();
        //     // 去除重复标签
        //     $item->tag = array_values(array_unique(array_column($tag->toArray(), 'tag')));
        //     $item->commentCount = Comment::where('article_id', $item->id)->count();
        // }  
        return $this->success($goods);
    }

    // 根据商品分类排序获取所有商品
    public function classify(Request $request) {
        $classifys = Goods::groupBy('classify')->pluck('classify');
        // $classifys = array_values(array_filter($classifys->toArray()));
        // $newData = [];
        // foreach($classifys as $item) {
        //     $newData[$item] = Goods::where('classify', $item)->orderBy('created_at', 'desc')->get();
        // }
        return $this->success($classifys);
    }
    

    // // 根据分类获取商品
    // public function getListOrderByClassify(){
    //     $calssify = $request->get('classify');

    //     if($request->all)
    //         $goods = Goods::withTrashed()->where('classify', $calssify)->orderBy('created_at', 'desc')->paginate(10, $data);
    //     else if ($request->classify)
    //         $goods = Goods::whereClassify($request->classify)->orderBy('created_at', 'desc')->paginate(10, $data);
    //     else
    //         $goods = Goods::orderBy('created_at', 'desc')->paginate(10, $data);

    // }

    public function buy(Request $request) {
        (new StockController())->decpStock($request->goods_id, $request->label_id, $request->count, 'buy');
    }
}
