<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use Carbon\Carbon;
use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use Illuminate\Http\Request;

class InstallmentsController extends Controller
{
    public function index(Request $request)
    {
        $installments = Installment::query()
                        ->where('user_id', $request->user()->id)
                        ->paginate(10);

        return view('installments.index', ['installments' => $installments]);
    }

    public function show(Installment $installment)
    {
        $installmentItems = $installment->InstallmentItems()->orderBy('sequence')->get();
        return view('installments.show', [

            'installment' => $installment,
            'items' => $installmentItems,
            'nextItem' => $installmentItems->where('paid_at', null)->first(),

        ]);
    }

    //支付宝分期付款
    public function payByAlipay(Installment $installment)
    {
        if ($installment->order->closed) {
            throw new InvalidRequestException('对应商品的订单已被关闭');
        }

        if ($installment->status === Installment::STATUS_FINISHED) {
            throw new InvalidRequestException('该分期付款已经结清');
        }

        if (!$nextItem = $installment->InstallmentItems()->whereNull('paid_at')->orderBy('sequence')->first()) {
            throw new InvalidRequestException('该分期订单已经结清');
        }

        return app('alipay')->web([
            'out_trade_no' => $installment->no .'_'.$nextItem->sequence,
            'total_amount' => $nextItem->total,
            'subject' => '支付 Laravel Shop 的分期订单:'.$installment->no,
            'notify_url' =>ngrok_url('installments.alipay.notify'),
            'return_url' =>route('installments.alipay.return'),
        ]);
    }

    //支付宝前端回调
    public function alipayReturn()
    {
        try {

            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    //支付宝后端回调
    public function alipayNotify()
    {
        //校验支付宝回调参数是否正确
        $data = app('alipay')->verify();

        // 拉起支付时使用的支付订单号是由分期流水号 + 还款计划编号组成的
        // 因此可以通过支付订单号来还原出这笔还款是哪个分期付款的哪个还款计划
        list($no, $sequence) = explode('_', $data->out_trade_no);
        //根据分期流水号查询对应的分期记录
        if (!$installment = Installment::where('no', $no)->first()) {
            return 'fail';
        }
        if (!$item = $installment->InstallmentItems()->where('sequence', $sequence)->first()) {
            return 'fail';
        }

        if ($item->paid_at) {
            return app('alipay')->success();
        }

        $item->update([
            'paid_at' => Carbon::now(),
            'payment_method' => 'alipay',
            'payment_no' => $data->trade_no,

        ]);

        // 如果这是第一笔还款
        if ($item->sequence === 0) {
            $installment->update([
                'status' => Installment::STATUS_REPAYING,
            ]);
            // 将分期付款对应的商品订单状态改为已支付
            $installment->order->update([
                'paid_at' => Carbon::now(),
                'payment_method' => 'alipay',
                'payment_no' => $no,
            ]);
            //触发商品订单已支付事件
            event(new OrderPaid($installment->order));
        }

        //如果这是最后一笔还款
        if ($item->sequence === $installment->count - 1) {
            $installment->update([
                'status' => Installment::STATUS_FINISHED,
            ]);
        }

        return app('alipay')->success();
    }
}
