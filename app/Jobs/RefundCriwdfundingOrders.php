<?php

namespace App\Jobs;

use App\Models\CrowdfundingProduct;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RefundCriwdfundingOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $crowdfunding;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CrowdfundingProduct $crowdfunding)
    {
        $this->crowdfunding = $crowdfunding;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->crowdfunding->status !== CrowdfundingProduct::STATUS_FAIL) {
            return;
        }

        $orderService = app(OrderService::class);
        //查询所有参与此众筹的订单
        Order::query()
            ->where('type', Order::TYPE_CROWDFUNDING)
            ->whereNotNull('paid_at')
            ->whereHas('items', function ($query) use ($crowdfunding) {
                $query->where('product_id', $crowdfunding->product_id);
            })->get()
            ->each(function(Order $order) use ($orderService) {
                //退款
                $orderService->refundOrder($order);
            });
    }
}
