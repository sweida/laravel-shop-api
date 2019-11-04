<?php

namespace App\Http\Controllers\Api;

use App\Models\Stock;
use App\Models\Goods;
use Illuminate\Http\Request;
use App\Http\Requests\StockRequest;


class StockController extends Controller
{
    // 减少商品库存 和 恢复商品库存
    public function decpStock($goods_id, $label_id, $count, $type) {
        $goods = Stock::where(['goods_id' => $goods_id, 'label_id' => $label_id])->first();
        $count = $count ? $count : 1;
        if ($type=='buy') {
            $goods->stock -=$count;
        } else if($type == 'cancel') {
            $goods->stock +=$count;
        }
        $goods->save();
        return $this->success($goods);
    }

    // 校验商品库存和获取最新价格，是否下架
    public function checkStock(Request $request) {
        $stocks = $request->get('stocks');
        $newStocks = [];

        foreach($stocks as $item){
            $stock = Stock::where(['goods_id' => $item['goods_id'], 'label_id' => $item['label_id']])->first();
            $item['stock'] = $stock->stock;
            $item['price'] = $stock->price;
            $item['vipprice'] = $stock->vipprice;
            // 商品是否失效
            $noEmpty = Goods::find($stock->goods_id);
            if (!$noEmpty)
                $item['isDelete'] = true;
            array_push($newStocks, $item);
        }
        return $this->success($newStocks);
    }

}
