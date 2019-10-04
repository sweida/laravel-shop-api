<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use Illuminate\Http\Request;


class CartController extends Controller
{
    // 加入购物车
    public function addCart(Request $request) {
        $count = $request->get('count');
        $good_id = $request->get('good_id');
        $user_id = $request->get('user_id');
        $label_id = $request->get('label_id');
        $cart = Cart::where([
            'good_id' => $good_id,
            'user_id' => $user_id,
            'label_id' => $label_id,
        ])->first();

        return $this->success($cart);
    }

    public function list(Request $request) {
        $carts = Cart::where('user_id', $request->user_id)->get();

        return $this->success($carts);
    }

    public function decCart(Request $request) {
        $count = $request->get('count');
        $good_id = $request->get('good_id');
        $user_id = $request->get('user_id');
        $label_id = $request->get('label_id');
        $cart = Cart::where([
            'good_id' => $good_id,
            'user_id' => $user_id,
            'label_id' => $label_id,
        ])->first();

        if ($count> $cart['count']) {
            $card->delete();
        } else {
            $cart['count'] -= $count;
            $cart->save();
        }

        return $this->message('删除成功');

    }
}
