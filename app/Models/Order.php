<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    protected $fillable = [
        'order_no',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'subtotal',
        'total',
        'status', // 'pending', 'processing', 'completed', 'delivered', 'cancelled'
    ];


    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // $lastOrder = self::orderBy('id', 'desc')->value('order_no') ?? 0;
            // $model->order_no = str_pad((int) $lastOrder + 1, 5, '0', STR_PAD_LEFT); // 00001
            $lastOrder = self::orderBy('id', 'desc')->value('order_no') ?? 5999;
            $nextOrder = max((int) $lastOrder + 1, 6000); // Ensure at least 6000
            $model->order_no = str_pad($nextOrder, 5, '0', STR_PAD_LEFT);
        });

        static::updating(function ($contract) {
            
        });
    }
    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }

    
}
