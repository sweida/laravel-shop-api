<?php

namespace App\Http\Controllers\Api;

use App\Models\Collection;
use App\Models\Goods;
use Illuminate\Http\Request;
use App\Http\Requests\CollectionRequest;

// 商品收藏夹

class CollectionController extends Controller
{
    public function likeGood(CollectionRequest $request) {
        if ($request->get('active')==true) {
            $collection = Collection::where(
                    ['user_id' => $request->user_id, 'goods_id' => $request->goods_id]
                )->first();
            if ($collection) {
                $collection->delete();
            }
            return $this->message('已移出收藏夹');
        } else {
            Collection::updateOrCreate(
                ['user_id' => $request->user_id, 'goods_id' => $request->goods_id], 
                $request->all()
            );
            return $this->message('已添加到收藏夹');
        }
    }

    // 取消收藏
    public function unlikeGood(CollectionRequest $request) {

    }

    // 获取商品被收藏数量
    public function likeCount($goodsId) {
        $count = Collection::where('goods_id', $goodsId)->count();
        return $count;
    }

    // 获取用户收藏数量
    public function likesGoodCount($userId) {
        $count = Collection::where('user_id', $userId)->count();
        return $count;
    }

    // 获取用户收藏夹列表
    public function likesGoodList(CollectionRequest $request) {
        $collections = Collection::where('user_id', $request->user_id)->paginate(20);

        foreach($collections as $item){
            $item->good = Goods::find($item->goods_id);
        }  
        
        // 这里是没拿到商品被收藏的数量

        return $this->success($collections);
    }
}
