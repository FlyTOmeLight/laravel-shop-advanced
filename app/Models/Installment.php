<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_REPAYING = 'repaying';
    const STATUS_FINISHED = 'finished';

    public static $statusMap = [
        self::STATUS_PENDING => '未执行',
        self::STATUS_REPAYING => '还款中',
        self::STATUS_FINISHED => '已完成',
    ];

    protected $fillable = [
        'no', 'total_amount', 'count', 'fee_rate', 'fine_rate', 'status',
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            if (!$model->no) {
                $model->no = static::findAvailableNo();
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    protected static function findAvailableNo()
    {
        $prefix = date('YmdHis');
        for ($i = 0;$i < 10;$i++) {
            $no = $prefix.str_pad(random_int(0,999999), 6, '0', STR_PAD_LEFT);
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }

        \Log::warning(sprintf('find installment no failed'));
        return false;
    }

    public function InstallmentItems()
    {
        return $this->hasMany(InstallmentItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function refreshRefundStatus()
    {
        $allSuccess = true;
        //重新加载InstallmentItems保持与数据库一致
        $this->load('InstallmentItems');
        // 再次遍历所有还款计划
        foreach ($this->InstallmentItems  as $item) {
            // 如果该还款计划已经还款，但退款状态不是成功
            if ($item->paid_at && $item->refund_status !== InstallmentItem::REFUND_STATUS_SUCCESS) {
                $allSuccess = false;
                break;
            }
        }

        if ($allSuccess) {
            $this->order->update([
                'refund_status' => InstallmentItem::REFUND_STATUS_SUCCESS,
            ]);
        }
    }
}
