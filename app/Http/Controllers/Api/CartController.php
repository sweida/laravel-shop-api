<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Stock;
use App\Models\Goods;
use App\Models\GoodsBanner;
use Illuminate\Http\Request;
use App\Http\Requests\CartRequest;

class CartController extends Controller
{
    // 加入购物车
    public function addGoods(CartRequest $request) {
        $count = $request->get('count');
        $goods_id = $request->get('goods_id');
        $user_id = $request->get('user_id');
        $label_id = $request->get('label_id');

        $requestGoods = [
            'goods_id' => $goods_id,
            'user_id' => $user_id,
            'label_id' => $label_id,
        ];

        $goods = Cart::where($requestGoods)->first();
        if ($goods) {
            $goods->count += $count;
            $goods->save();
        } else {
            $requestGoods['count'] = $count;
            $goods = Cart::create($requestGoods);
        }

        return $this->success($goods);
    }

    // 减少购物车
    public function decGoods(CartRequest $request) {

        $count = $request->get('count');
        $goods_id = $request->get('goods_id');
        $user_id = $request->get('user_id');
        $label_id = $request->get('label_id');

        $goods = Cart::where([
            'goods_id' => $goods_id,
            'user_id' => $user_id,
            'label_id' => $label_id,
        ])->first();

        $goods->count -=$count;
        if ($goods->count <= 0) {
            $goods->delete();
            return $this->message('商品删除成功！');
        }
        $goods->save();

        return $this->success($goods);
    }

    // 获取个人列表
    public function list(CartRequest $request) {
        $carts = Cart::where('user_id', $request->user_id)->get();
        foreach($carts as $item) {
            $item->stock = Stock::where([
                'goods_id' => $item->goods_id, 
                'label_id' => $item->label_id
                ])->first();
            $item->goods = Goods::withTrashed()->find($item->goods_id);

            // 封面图
            $banner = GoodsBanner::where([ 'goods_id'=>$item->goods_id, ])->orderBy('active', 'desc')->first();

            $item->defaultBanner = $banner->url;
        }
        return $this->success($carts);
    }
}
