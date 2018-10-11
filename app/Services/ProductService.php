<?php

namespace App\Services;

use App\Models\Product;
use App\SearchBuilders\ProductSearchBuilder;

class ProductService
{

    /**
     * @param Product $product
     * @param $amount 推荐几个商品
     * @return array 返回相似商品id
     */
    public function getSimilarProductIds(Product $product, $amount)
    {
        if (!count($product->properties)) {
            return [];
        }

        $builder = (new ProductSearchBuilder())->onSale()->paginate($amount, 1);
        foreach ($product->properties as $property) {
            $builder->propertyFilter($property->name, $property->value, 'should');
        }
        $builder->minShouldMatch(count($product->properties)/2);
        $params = $builder->getParams();
        $params['body']['query']['bool']['must_not'] = [
            ['term' => ['_id' => $product->id]],
        ];

        $result = app('es')->search($params);

        return collect($result['hits']['hits'])->pluck('_id')->all();

    }
}