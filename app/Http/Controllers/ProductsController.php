<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\SearchBuilders\ProductSearchBuilder;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 16;
        //构建查询
//        $params = [
//            'index' => 'products',
//            'type' => '_doc',
//            'body' => [
//                'from' => ($page - 1) * $perPage,
//                'size' => $perPage,
//                'query' => [
//                    'bool' => [
//                      'filter' => [
//                          ['term' => ['on_sale' => true]],
//                      ],
//                    ],
//                ],
//            ],
//        ];
        $searchBuilder = (new ProductSearchBuilder())->onSale()->paginate($perPage, $page);

        //是否提交order参数
        if ($order = $request->input('order', '')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
//                    $params['body']['sort'] = [[$m[1] => $m[2]]];
                    $searchBuilder->orderBy($m[1], $m[2]);
                }

            }
        }

        //按照类目来筛选
        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
//            //如果这是一个父栏目
//            if ($category->is_directory) {
//                //使用category_path来筛选
//                $params['body']['query']['bool']['filter'][] = [
//                    'prefix' => ['category_path' => $category->path.$category->id.'-'],
//                ];
//            } else {
//                $params['body']['query']['bool']['filter'][] = [
//                    'term' => [
//                        'category_id' => $category->id,
//                    ],
//                ];
//            }
            $searchBuilder->category($category);
        }

        //关键字查询
        if ($search = $request->input('search', '')) {
            //多关键字查询
            $keywords = array_filter(explode(' ', $search));
//            foreach ($keywords as $keyword) {
//                $params['body']['query']['bool']['must'][] = [
//                    'multi_match' => [
//                        'query' => $keyword,
//                        'fields' => [
//                            'title^3',
//                            'long_title^2',
//                            'category^2',
//                            'description',
//                            'skus_title',
//                            'skus_description',
//                            'properties_value',
//                        ],
//                    ],
//                ];
//            }
            $searchBuilder->keywords($keywords);
        }

        //聚合搜索，仅仅在存在关键词搜索或者使用了类目筛选时才存在聚合搜索(分面搜索)
        if ($search || isset($category)) {
//            $params['body']['aggs'] = [
//                'properties' => [
//                    'nested' => [
//                        'path' => 'properties',
//                    ],
//                    //二层聚合
//                    'aggs' => [
//                        'properties' => [
//                            'terms' => [
//                                'field' => 'properties.name',
//                            ],
//                            //三层聚合
//                            'aggs' => [
//                                'value' => [
//                                    'terms' => [
//                                        'field' => 'properties.value',
//                                    ],
//                                ],
//                            ],
//                        ],
//                    ],
//                ],
//            ];
            $searchBuilder->aggregateProperties();
        }
        //避免按属性筛选后显示的属性条目重复
        $propertyFilters = [];
        //按属性值筛选
        if ($filterString = $request->input('filters')) {
            $filterArray = explode('|', $filterString);
            foreach ($filterArray as $filter) {
                list($name, $value) = explode(':', $filter);
                $propertyFilters[$name] = $value;
                //添加到filter中
//                $params['body']['query']['bool']['filter'][] = [
//                    //因为是筛选的nested下的属性值所以要使用nested
//                    'nested' => [
//                        'path' => 'properties',
//                        'query' => [
//                            ['term' => ['properties.name' => $name]],
//                            ['term' => ['properties.value' => $value]],
//                        ],
//                    ],
//                ];
                $searchBuilder->propertyFilter($name, $value);
            }
        }

//        dd($searchBuilder->getParams());

        $result = app('es')->search($searchBuilder->getParams());
        $productsId = collect($result['hits']['hits'])->pluck('_id')->all();

        $products = Product::query()
            ->whereIn('id', $productsId)
            ->orderByRaw(\DB::raw("FIND_IN_SET(id, '".join(',', $productsId)."'".')'))
            ->get();

        $pager = new LengthAwarePaginator($products, $result['hits']['total'], $perPage, $page, [
            'path' => route('products.index', false),// 手动构建分页的 url
        ]);

        $properties = [];

        //如果返回的字段里有aggregations字段说明做了分面搜索
        if (isset($result['aggregations'])) {
            //使用collect将返回值转为集合
            $properties = collect($result['aggregations']['properties']['properties']['buckets'])
                ->map(function ($bucket) {
                    return [
                        'key' => $bucket['key'],
                        'values' => collect($bucket['value']['buckets'])->pluck('key')->all(),
                    ];
                })->filter(function ($property) use ($propertyFilters) {
                    return  count($property['values']) > 1 && !isset($propertyFilters[$property['key']]);
                });
        }

        return view('products.index', [
            'products' => $pager,
            'filters' => [
                'search' => $search,
                'order' => $order,
            ],
            'category' => $category ?? null,
            'properties' => $properties,
            'propertyFilters' => $propertyFilters,
        ]);

//        // 创建一个查询构造器
//        $builder = Product::query()->where('on_sale', true);
//        // 判断是否有提交 search 参数，如果有就赋值给 $search 变量
//        // search 参数用来模糊搜索商品
//        if ($search = $request->input('search', '')) {
//            $like = '%'.$search.'%';
//            // 模糊搜索商品标题、商品详情、SKU 标题、SKU描述
//            $builder->where(function ($query) use ($like) {
//                $query->where('title', 'like', $like)
//                    ->orWhere('description', 'like', $like)
//                    ->orWhereHas('skus', function ($query) use ($like) {
//                        $query->where('title', 'like', $like)
//                            ->orWhere('description', 'like', $like);
//                    });
//            });
//        }
//        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
//            //如果这是一个父栏目
//            if ($category->is_directory) {
//                //筛选出该父类下的所有子类目商品
//                $builder->whereHas('category', function($query) use ($category) {
//                    $query->where('path', 'like', $category->path.$category->id.'-%');
//                });
//            } else {
//                $builder->where('category_id', $category->id);
//            }
//        }
//
//        // 是否有提交 order 参数，如果有就赋值给 $order 变量
//        // order 参数用来控制商品的排序规则
//        if ($order = $request->input('order', '')) {
//            // 是否是以 _asc 或者 _desc 结尾
//            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
//                // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
//                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
//                    // 根据传入的排序值来构造排序参数
//                    $builder->orderBy($m[1], $m[2]);
//                }
//            }
//        }
//
//        $products = $builder->paginate(16);
//
//        return view('products.index', [
//            'products' => $products,
//            'filters'  => [
//                'search' => $search,
//                'order'  => $order,
//            ],
//            'category' => $category ?? null, // 等价于 isset($category) ? $category : null
//            'categoryTree' => $categoryService->getCategoryTree(),
//
//        ]);
    }

    public function show(Product $product, Request $request)
    {
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }

        $favored = false;
        // 用户未登录时返回的是 null，已登录时返回的是对应的用户对象
        if($user = $request->user()) {
            // 从当前用户已收藏的商品中搜索 id 为当前商品 id 的商品
            // boolval() 函数用于把值转为布尔值
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }
        
        $reviews = OrderItem::query()
            ->with(['order.user', 'productSku']) // 预先加载关联关系
            ->where('product_id', $product->id)
            ->whereNotNull('reviewed_at') // 筛选出已评价的
            ->orderBy('reviewed_at', 'desc') // 按评价时间倒序
            ->limit(10) // 取出 10 条
            ->get();
        
        // 最后别忘了注入到模板中
        return view('products.show', [
            'product' => $product,
            'favored' => $favored,
            'reviews' => $reviews
        ]);
    }

    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }

    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        return [];
    }

    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', ['products' => $products]);
    }
}
