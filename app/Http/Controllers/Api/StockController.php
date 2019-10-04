<?php

namespace App\Http\Controllers\Api;

use App\Models\Stock;
use App\Models\Good;
use Illuminate\Http\Request;
use App\Http\Requests\StockRequest;


class StockController extends Controller
{
    // 减少商品库存 和 恢复商品库存
    public function decpStock($good_id, $label_id, $count, $type) {
        $good = Stock::where(['good_id' => $good_id, 'label_id' => $label_id])->first();
        $count = $count ? $count : 1;
        if ($type=='buy') {
            $good->stock -=$count;
        } else if($type == 'cancel') {
            $good->stock +=$count;
        }
        $good->save();
        return $this->success($good);
    }

    // 校验商品库存和获取最新价格，是否下架
    public function checkStock(Request $request) {
        $stocks = $request->get('stocks');
        $newStocks = [];

        foreach($stocks as $item){
            $stock = Stock::where(['good_id' => $item['good_id'], 'label_id' => $item['label_id']])->first();
            $item['stock'] = $stock->stock;
            $item['price'] = $stock->price;
            $item['vipprice'] = $stock->vipprice;
            // 商品是否失效
            $noEmpty = Good::find($stock->good_id);
            if (!$noEmpty)
                $item['isDelete'] = true;
            array_push($newStocks, $item);
        }
        return $this->success($newStocks);
    }

}
