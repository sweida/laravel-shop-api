<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Stock;
use App\Models\Good;
use App\Models\User;
use App\Models\OrderGood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;



class OrderController extends Controller
{
    public function createOrder(Request $request) {
        $orderId = $this->createOrderNo('order_id');

        $goodList = $request->get('goodList');
        $discount = $request->get('discount');
        // 计算付款金额
        $goodPrice = 0;
        foreach($goodList as $item){
            $good = Stock::where(['good_id' => $item['good_id'], 'label_id' => $item['label_id']])->first();

            // 商品是否失效
            if (!$good)
                return $this->message('商品不存在');
            // 检验库存
            if ($item['count'] > $good->stock) {
                return $this->message('库存不足，请返回购物车确定后重新下单');
            }

            $item['price'] = $good->price;
            $goodPrice += $item['price'] * $item['count'];

        }
        $expressPrice = $goodPrice >= 9000 ? 0 : 1000;
        $totalPay = $goodPrice + $expressPrice - $discount;

        $data = $request->all();
        $data['order_id'] = $orderId;
        $data['goodPrice'] = $goodPrice;
        $data['totalPay'] = $totalPay;

        Order::create($data);

        foreach($goodList as $item){
            // 购买的商品表
            $item['order_id'] = $orderId;
            OrderGood::Create($item);

            // 扣减库存
            (new StockController())->decpStock($item['good_id'], $item['label_id'], $item['count'], 'buy');
        }

        return $this->success($data);
    }

    public function cancelOrder(Request $request) {
        $order = Order::where('order_id', $request->order_id)->first();
        if (!$order) 
            return $this->failed('订单不存在', 200);

        $order->status = 6;
        $order->save();
        return $this->message('订单已取消');
    }

    // 获取个人订单
    public function personalList(Request $request){
        // $orders = Order::where('user_id', $request->user_id)->paginate(20);

        $status = $request->get('status');
        $orders = Order::where(['user_id'=> $request->user_id, 'status' => $status])->paginate(20);

        foreach($orders as $item) {
            $item['goodList'] = OrderGood::where('order_id', $item->order_id)->get();
        }

        return $this->success($orders);
    }

    // 获取所有人订单
    public function allList(Request $request){
        $orders = Order::where('status', $request->status)->paginate(20);

        foreach($orders as $item) {
            $user = User::where('openid', $item->user_id)->first();
            $item['userName'] = $user->nickName;
            $item['goodList'] = OrderGood::where('order_id', $item->order_id)->get();
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
