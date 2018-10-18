<?php

namespace App\Models;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{   
    const TYPE_NORMAL = 'normal';
    const TYPE_CROWDFUNDING = 'crowdfunding';
    const TYPE_SECKILL = 'seckill';
    public static $typeMap = [
        self::TYPE_NORMAL => '普通商品',
        self::TYPE_CROWDFUNDING => '众筹商品',
        self::TYPE_SECKILL => '秒杀商品',
    ];

    protected $fillable = ['title', 'long_title', 'description', 'image', 'on_sale', 'rating', 'sold_count', 'review_count', 'price', 'type'];
    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段
    ];

    //与秒杀商品关联
    public function seckill()
    {
        return $this->hasOne(SeckillProduct::class);
    }
    // 与商品SKU关联
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        if (env('FILE_STORE') == 'oss') {
            return "https://".env('OSS_BUCKET').env('OSS_ENDPOINT').'/'.$this->attributes['image'];
        }

        return \Storage::disk('public')->url($this->attributes['image']);
    }

    public function crowdfunding()
    {
        return $this->hasOne(CrowdfundingProduct::class);
    }

    public function properties()
    {
        return $this->hasMany(ProductProperty::class);
    }

    public function getGroupPropertiesAttribute()
    {
        return $this->properties
                    ->groupBy('name')
                    ->map(function ($properties){
                        return $properties->pluck('value')->all();
                    });
    }

    //转化为esarray
    public function toESArray()
    {
        $arr = array_only($this->toArray(), [
            'id',
            'type',
            'title',
            'long_title',
            'category_id',
            'on_sale',
            'rating',
            'sold_count',
            'review_count',
            'price',
        ]);

        $arr['category'] = $this->category ? explode(' - ', $this->category->full_name) : '';
        $arr['category_path'] = $this->category ? $this->category->path : '';
        $arr['description'] = strip_tags($this->description);
        $arr['skus'] = $this->skus->map(function (ProductSku $sku) {
            return array_only($sku->toArray(), ['title', 'description', 'price']);
        });
        $arr['properties'] = $this->properties->map(function (ProductProperty $property) {
//            return array_only($property->toArray(), ['name', 'value']);
            return array_merge(array_only($property->toArray(), ['name', 'value']), [
                'search_value' => $property->name.':'.$property->value,
            ]);
        });
        return $arr;
    }


    /**
     * es查询后使商品排序
     * @param $query
     * @param $ids
     * @return query
     */
    public function scopeByIds($query, $ids)
    {
        return $query->whereIn('id', $ids)
                    ->orderByRaw(\DB::raw("FIND_IN_SET('id','".join(',', $ids)."')"));

    }
}
