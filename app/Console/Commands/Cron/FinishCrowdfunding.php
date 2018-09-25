<?php

namespace App\Console\Commands\Cron;

use App\Jobs\RefundCriwdfundingOrders;
use App\Services\OrderService;
use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\CrowdfundingProduct;
use Carbon\Carbon;

class FinishCrowdfunding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:finish-crowdfunding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '结束众筹';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        CrowdfundingProduct::query()
            //预加载商品数据
            ->with(['products'])
            ->where('end_at', '<=', Carbon::now())
            ->where('status', CrowdfundingProduct::STATUS_FUNDING)
            ->get()
            ->each(function (CrowdfundingProduct $crowdfunding) {
                //如果众筹目标大于实际众筹金额
                if ($crowdfunding->target_amount > $crowdfunding->total_amount) {
//                    调用众筹失败逻辑
                    $this->crowdfundingFail($crowdfunding);
                } else {
                    // 否则调用众筹成功逻辑
                    $this->crowdfundingSucceed($crowdfunding);
                }
            });
    }

    protected function crowdfundingSucceed(CrowdfundingProduct $crowdfunding)
    {
        //众筹状况改为成功
        $crowdfunding->update([
            'status' => CrowdfundingProduct::STATUS_SUCCESS,
        ]);
    }

    protected function crowdfundingFail(CrowdfundingProduct $crowdfunding)
    {
        $crowdfunding->update([
            'status' => CrowdfundingProduct::STATUS_FAIL,
        ]);

        dispatch(new RefundCriwdfundingOrders($crowdfunding));
    }
}
