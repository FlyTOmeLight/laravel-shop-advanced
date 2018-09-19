<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\Category;
use Encore\Admin\Form;
use Encore\Admin\Grid;
// use Encore\Admin\Facades\Admin;
// use Encore\Admin\Layout\Content;
// use App\Http\Controllers\Controller;

class ProductsController extends CommonProductsController
{
    public function getProductType()
    {
        return Product::TYPE_NORMAL;
    }

    /**
     * Index interface.
     *
     * @return Content
     
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('商品列表');
            $content->body($this->grid());
        });
    }
    */
    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('编辑商品');
            $content->body($this->form()->edit($id));
        });
    }
    */
    /**
     * Create interface.
     *
     * @return Content
     
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建商品');
            $content->body($this->form());
        });
    }
    */
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function customGrid(Grid $grid)
    {
        
            $grid->model()->with(['category']);

            $grid->id('ID')->sortable();
            $grid->title('商品名称');

            // Laravel-Admin 支持用符号 . 来展示关联关系的字段
            $grid->column('category.name', '类目');

            $grid->on_sale('已上架')->display(function ($value) {
                return $value ? '是' : '否';
            });
            $grid->price('价格');
            $grid->rating('评分');
            $grid->sold_count('销量');
            $grid->review_count('评论数');
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function customForm(Form $form)
    {
    }
}
