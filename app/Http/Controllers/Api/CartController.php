<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use Illuminate\Http\Request;


class CartController extends Controller
{
    // 加入购物车
    public function addGoods(Request $request) {
        $count = $request->get('count');
        $goods_id = $request->get('goods_id');
        $user_id = $request->get('user_id');
        $label_id = $request->get('label_id');

        $goods = Cart::firstOrCreate([
            'goods_id' => $goods_id,
            'user_id' => $user_id,
            'label_id' => $label_id,
        ], ['count' => $count]);

        $goods->count += $count;
        $goods->save();

        return $this->success($goods);
    }

    public function list(Request $request) {
        $carts = Cart::where('user_id', $request->user_id)->get();

        return $this->success($carts);
    }

    public function decGoods(Request $request) {

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
}
