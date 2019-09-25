<?php

namespace App\Http\Controllers\Api;


use App\Models\Good;
use Illuminate\Http\Request;
use App\Http\Requests\GoodRequest;


class GoodController extends Controller
{
    // 添加商品
    public function addGood(Request $request){
        Good::create($request->all());
        return $this->message('商品新增成功');
    }

    // 修改商品信息
    public function edit(Request $request){
        $good = Good::findOrFail($request->id);
        $good->update($request->all());

        return $this->message('商品修改成功！');
    }

    // 商品详情
    public function detail(Request $request) {
        $good = Good::findOrFail($request->id);
        $good->likeCount = (new CollectionController())->likeCount($request->id);
        // $good->banners = (new GoodbannerController())->

        return $this->success($good);
    }

    // 下架商品
    public function delete(Request $request){
        Good::findOrFail($request->id)->delete();
        return $this->message('商品下架成功');
    }

    // 恢复下架商品
    public function restored(Request $request){
        Good::withTrashed()->findOrFail($request->id)->restore();
        return $this->message('商品恢复成功');
    }


    //返回商品列表 10篇为一页
    public function goodList(Request $request){
        // 需要显示的字段
        $data = ['id', 'title', 'price', 'vipprice', 'desc', 'classify', 'clicks', 'likes', 'buys', 'stock','created_at', 'deleted_at'];

        // 获取所有，包括软删除
        if($request->all)
            $goods = Good::withTrashed()->orderBy('created_at', 'desc')->paginate(10, $data);
        else if ($request->classify)
            $goods = Good::whereClassify($request->classify)->orderBy('created_at', 'desc')->paginate(10, $data);
        else
            $goods = Good::orderBy('created_at', 'desc')->paginate(10, $data);

        // 拿到商品封面图

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




}
