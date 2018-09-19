@if (isset($category['children']) &&count($category['children']) > 0)
	<li class="dropdown-submenu">
		<a href="{{ route('products.index', ['category_id' => $category['id']]) }}" class="dropdown-toggle" data-toggle="down">{{ $category['name'] }}</a>
		<ul class="dropdown-menu">
			@each ('layouts._category_item', $category['children'], 'category')
		</ul>
	</li>
@else
	<li><a href="{{ route('products.index', ['category_id' => $category['id']]) }}">{{ $category['name'] }}</a></li>
@endif