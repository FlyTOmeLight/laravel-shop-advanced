@extends('layouts.app')
@section('title', '商品列表')

@section('content')
<div class="row">
<div class="col-lg-10 col-lg-offset-1">
<div class="panel panel-default">
  <div class="panel-body">
    <div class="row">
      <form action="{{ route('products.index') }}" class="form-inline search-form">
          <!-- 创建一个隐藏字段,修复分面搜索后出现的排序问题 -->
          <input type="hidden" name="filters">
         {{--面包屑开始--}}
        <a href="{{ route('products.index') }}" class="all-products">全部</a> &gt;
        @if ($category)
          @if($category->path !== '-')
            @foreach ($category->ancestors as $ancestor)
              <span class="category">
                <a href="{{ route('products.index', ['category_id' => $ancestor->id]) }}">{{ $ancestor->name }}</a>
              </span>
              <span>></span>
            @endforeach
          @endif
          <span class="category">{{ $category->name }}</span>
          <input type="hidden" name="category_id" value="{{ $category->id }}">>
        @endif
      <!-- 商品属性面包屑开始 -->
          @foreach($propertyFilters as $name => $value)
              <span class="filter">{{ $name }}：
                <span class="filter-value">{{ $value }}</span>
                  {{--调用去除筛选面包屑页面发生页面--}}
                  <a href="javascript:removeFilterFromQuery('{{ $name }}');" class="remove-filter">x</a>
              </span>
          @endforeach
      <!-- 商品属性面包屑结束 -->
         {{--面包屑结束--}}
        <input type="text" class="form-control input-sm" name="search" placeholder="搜索">
        <button class="btn btn-primary btn-sm">搜索</button>
        <select name="order" class="form-control input-sm pull-right">
          <option value="">排序方式</option>
          <option value="price_asc">价格从低到高</option>
          <option value="price_desc">价格从高到低</option>
          <option value="sold_count_desc">销量从高到低</option>
          <option value="sold_count_asc">销量从低到高</option>
          <option value="rating_desc">评价从高到低</option>
          <option value="rating_asc">评价从低到高</option>
        </select>
      </form>
    </div>
     <div class="filters">
      <!-- 如果当前是通过类目筛选，并且此类目是一个父类目 -->
      @if ($category && $category->is_directory)
        <div class="row">
          <div class="col-xs-3 filter-key">子类目：</div>
          <div class="col-xs-9 filter-values">
          <!-- 遍历直接子类目 -->
          @foreach($category->children as $child)
            <a href="{{ route('products.index', ['category_id' => $child->id]) }}">{{ $child->name }}</a>
          @endforeach
          </div>
        </div>
      @endif
       {{--分面搜索开始--}}
       @if(count($properties) > 0)
        @foreach($properties as $property)
          <div class="row">
            <div class="col-xs-3 filter-key">{{ $property['key'] }}：</div>
            <div class="col-xs-9 filter-values">
              @foreach($property['values'] as $value)
                {{--实现点击筛选--}}
                <a href="javascript:appendFilterToQuery('{{ $property['key'] }}', '{{ $value }}');">{{ $value }}</a>
              @endforeach
            </div>
          </div>
         @endforeach
       @endif
       {{--分面搜索结束--}}
    </div>
    <div class="row products-list">
      @foreach($products as $product)
      <div class="col-xs-3 product-item">
        <div class="product-content">
          <div class="top">
            <div class="img">
              <a href="{{ route('products.show', ['product' => $product->id]) }}">
                <img src="{{ $product->image_url }}" alt="">
              </a>
            </div>
            <div class="price"><b>￥</b>{{ $product->price }}</div>
            <div class="title">
              <a href="{{ route('products.show', ['product' => $product->id]) }}">{{ $product->title }}</a>
            </div>
          </div>
          <div class="bottom">
            <div class="sold_count">销量 <span>{{ $product->sold_count }}笔</span></div>
            <div class="review_count">评价 <span>{{ $product->review_count }}</span></div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    <div class="pull-right">{{ $products->appends($filters)->render() }}</div>
  </div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
  <script>
    //实现点击筛选
    // 定义一个函数，解析url中的参数
    function parseSearch() {
        var searches = {}; //定义一个空对象
        // location.search 会返回 Url 中 ? 以及后面的查询参数
        // substr(1) 将 ? 去除，然后以符号 & 分割成数组，然后遍历这个数组
        location.search.substr(1).split('&').forEach(function (str) {
            var result = str.split('=');
            // 将数组的第一个值解码之后作为 Key，第二个值解码后作为 Value 放到之前初始化的对象中
            searches[decodeURIComponent(result[0])] = decodeURIComponent(result[1]);
        });
        console.log(searches);
        return searches;
    }

    // 根据 Key-Value 对象构建查询参数
    function buildSearch(searches) {
        //初始化字符串
        var query = '?';
        _.forEach(searches, function (value, key) {
          query += encodeURIComponent(key) + '=' + encodeURIComponent(value) + '&';
        });
        console.log(query);
        // 去除最末尾的 & 符号
        return query.substr(0, query.length - 1);
    }

    //将新的filter加入url中
    function appendFilterToQuery(name, value) {
        var searches = parseSearch();
        if (searches['filters']) {
            searches['filters'] += '|' + name + ':' + value;
        } else {
            searches['filters'] = name + ':' + value;
        }

        //重新构建参数触发浏览器跳转
        location.search = buildSearch(searches);
    }

    //移除filter从url
    function removeFilterFromQuery(name) {
        var searches = parseSearch();
        if (!searches['filters']) {
            return;
        }
        var filters = [];
        searches['filters'].split('|').forEach(function (filter) {
            var result = filter.split(':');
            if (result[0] === name) {
                return;
            }
            filters.push(filter);
        });

        searches['filters'] = filters.join('|');
        location.search = buildSearch(searches);
    }

    var filters={!! json_encode($filters) !!};
    $(document).ready(function () {
      $('.search-form input[name=search]').val(filters.search);
      $('.search-form select[name=order]').val(filters.order);

      $('.search-form select[name=order]').on('change', function() {
          var searches = parseSearch();
          if (searches['filters']) {
              $('.search-form input[name=filters]').val(searches['filters']);
          }
          $('.search-form').submit();
      });
    })
  </script>
@endsection
