<?php

namespace App\Http\Controllers\Api;

use App\Models\Collection;
use App\Models\Good;
use Illuminate\Http\Request;
use App\Http\Requests\CollectionRequest;

// 商品收藏夹

class CollectionController extends Controller
{
    public function likeGood(CollectionRequest $request) {
        Collection::updateOrCreate(
            ['user_id' => $request->user_id, 'good_id' => $request->good_id], 
            $request->all()
        );
        return $this->message('已添加到收藏夹');
    }

    // 取消收藏
    public function unlikeGood(CollectionRequest $request) {
        $collection = Collection::where(
                ['user_id' => $request->user_id, 'good_id' => $request->good_id]
            )->first();
        if ($collection) {
            $collection->delete();
        }
        return $this->message('已移出收藏夹');
    }

    // 获取商品被收藏数量
    public function likeCount($goodId) {
        $count = Collection::where('good_id', $goodId)->count();
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

        // 这里是没拿到商品被收藏的数量
        foreach($collections as $item){
            $item->good = Good::find($item->good_id);
        }  

        return $this->success($collections);
    }
}
