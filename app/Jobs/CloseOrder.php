<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Http\Controllers\Api\StockController;


class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order, $delay)
    {
        $this->order = $order;
        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    // 定义这个任务类具体的执行逻辑
    // 当队列处理器从队列中取出任务时，会调用 handle() 方法
    public function handle()
    {
        // 判断对应的订单是否已经被支付
        // 如果已经支付则不需要关闭订单，直接退出
        if ($this->order->status == 2) {
            return;
        }
        // 恢复库存
        $goodsList = OrderGoods::where('order_id', $this->order->order_id)->get();
        foreach($goodsList as $item){
            (new StockController())->decpStock($item['goods_id'], $item['label_id'], $item['count'], 'cancel');
        }

        $this->order->status = 6;
        $this->order->save();

        // 通过事务执行 sql
        // \DB::transaction(function() {

        // });
    }
}
