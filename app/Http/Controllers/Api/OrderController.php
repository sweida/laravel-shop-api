<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Stock;
use App\Models\Goods;
use App\Models\User;
use App\Models\OrderGoods;
use App\Jobs\CloseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;



class OrderController extends Controller
{
    // 生成订单
    public function createOrder(Request $request) {
        $orderId = $this->createOrderNo('order_id');

        $goodsList = $request->get('goodsList');
        $discount = $request->get('discount');
        // 计算付款金额
        $goodsPrice = 0;
        foreach($goodsList as $item){
            $goods = Stock::where(['goods_id' => $item['goods_id'], 'label_id' => $item['label_id']])->first();

            // 商品是否失效
            if (!$goods)
                return $this->message('商品不存在');
            // 检验库存
            if ($item['count'] > $goods->stock) {
                return $this->message('库存不足，请返回购物车确定后重新下单');
            }

            $item['price'] = $goods->price;
            $goodsPrice += $item['price'] * $item['count'];

        }
        $expressPrice = $goodsPrice >= 9000 ? 0 : 1000;
        $totalPay = $goodsPrice + $expressPrice - $discount;

        $data = $request->all();
        $data['order_id'] = $orderId;
        $data['goodsPrice'] = $goodsPrice;
        $data['totalPay'] = $totalPay;
        $data['expressPrice'] = $expressPrice;

        foreach($goodsList as $item){
            // 购买的商品表
            $item['order_id'] = $orderId;
            $item['goods_name'] = $item['title'];
            OrderGoods::create($item);

            // 扣减库存
            (new StockController())->decpStock($item['goods_id'], $item['label_id'], $item['count'], 'buy');
        }
        
        $order = Order::create($data);

        // $order = Order::where('order_id', $orderId)->first();
        // $order->update([$order->status = 6]);
        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

        CloseOrder::dispatch($order, config('app.order_ttl'));
        return $this->success($data);
    }

    public function setStatus($order_id, $num) {
        $order = Order::where('order_id', $order_id)->first();
        if (!$order) 
            return $this->failed('订单不存在', 200);

        $order->status = $num;
        $order->save();
    }

    // 取消订单 status = 6
    public function cancelOrder(Request $request) {
        $order = Order::where('order_id', $request->order_id)->first();
        if (!$order){
            return $this->failed('订单不存在', 200);
        } else if ($order['status'] == 6) {
            return $this->failed('订单已取消，无需重复提交', 200);
        } else if ($order['status'] == 1) {
            $order->status = 6;
            $goodsList = OrderGoods::where('order_id', $request->order_id)->get();
            // 恢复库存
            foreach($goodsList as $item){
                (new StockController())->decpStock($item['goods_id'], $item['label_id'], $item['count'], 'cancel');
            }
            $order->save();

            return $this->message('订单已取消');
        } else {
            return $this->failed('订单已支付，无法取消订单', 200);
        }

    }

    // 支付订单 status = 2
    public function payOrder(Request $request) {
        $order = Order::where('order_id', $request->order_id)->first();
        if (!$order){
            return $this->failed('订单不存在', 200);
        } else if ($order['status'] == 6) {
            return $this->failed('订单已取消，请重新下单后再付款', 200);
        } else if ($order['status'] != 1) {
            return $this->failed('订单已经支付，请勿重复支付订单', 200);
        }

        $order->status = 2;
        $order->save();
        return $this->message('订单支付成功');
    }

    // 发货 status = 3
    public function DeliverGoods(Request $request) {
        $order = Order::where('order_id', $request->order_id)->first();
        if (!$order){
            return $this->failed('订单不存在', 200);
        } else if ($order['status'] != 2) {
            return $this->failed('订单还未付款', 200);
        }

        $order->status = 3;
        $order->save();
        return $this->message('订单已经发货');
    }

    // 确认订单 status = 5
    public function submitOrder(Request $request) {
        $order = Order::where('order_id', $request->order_id)->first();
        if (!$order){
            return $this->failed('订单不存在', 200);
        } else if ($order['status'] != 4) {
            return $this->failed('订单还没签收', 200);
        }

        $order->status = 5;
        $order->save();
        return $this->message('订单已确认');
    }

    // 获取个人订单
    public function personalList(Request $request){
        // $orders = Order::where('user_id', $request->user_id)->paginate(20);

        $status = $request->get('status');
        if ($status == 0) {
            $orders = Order::where(['user_id'=> $request->user_id])->orderBy('created_at', 'desc')->paginate(20);
        } else {
            $orders = Order::where(['user_id'=> $request->user_id, 'status' => $status])->orderBy('created_at', 'desc')->paginate(20);
        }

        foreach($orders as $item) {
            $item['goodsList'] = OrderGoods::where('order_id', $item->order_id)->get();
        }

        return $this->success($orders);
    }

    // 获取所有人订单
    public function allList(Request $request){
        if ($request->status) {
            // 将,切割成数组
            $status = explode(',', $request->status);
            $orders = Order::whereIn('status', $status)->orderBy('created_at', 'desc')->paginate(10);
        } else if ($request->user_id) {
            $orders = Order::where('user_id', $request->user_id)->orderBy('created_at', 'desc')->paginate(10);
        } else if ($request->order_id) {
            $orders = Order::where('order_id', $request->order_id)->orderBy('created_at', 'desc')->paginate(10);
        } else if ($request->date){
            $orders = Order::whereDate('created_at', $request->date)->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $orders = Order::orderBy('created_at', 'desc')->paginate(10);
        }

        foreach($orders as $item) {
            $user = User::where('openid', $item->user_id)->first();
            $item['userName'] = $user->nickName;
            $item['goodsList'] = OrderGoods::where('order_id', $item->order_id)->get();
        }

        return $this->success($orders);
    }


    /**
     * 生成订单号
     *  -当天从1开始自增
     *  -订单号模样：20190604000001
     * @param Client $redis
     * @param $key
     * @param $back：序号回退，如果订单创建失败，事务回滚可用
     * @return string
     */
    public static function createOrderNo($key, $back=0)
    {
        $sn = Redis::get($key);
        $snDate = substr($sn,0,8);
        $snNo = intval(substr($sn,8));
        $curDate = date('Ymd');
        if($back==1){//序号回退
            if($curDate==$snDate){
                $snNo = ($snNo>1) ? ($snNo-1) : 1;
                $sn = $curDate.sprintf("%06d",$snNo);
            }
        }else{//序号增加
            if(empty($sn)){
                $sn = $curDate.'000001';
            }else{
                $snNo = ($curDate==$snDate) ? ($snNo+1) : 1;
                $sn = $curDate.sprintf("%06d",$snNo);
            }
        }
        Redis::set($key,$sn);
        return $sn;
    }
}
