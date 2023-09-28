<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Shop\Category;
use App\Models\Shop\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        //$products = Product::where('category_id',$id);
        $categories = Category::paginate(6);
        return view('frontend.category.index', compact('categories'));
    }

    public function show($id)
    {
        $category = Category::find($id);
        $products = $category->products()->paginate(3);
        //$products = Product::where('shop_category_id',$id)->paginate(3);
        $categories = Category::all();
        //dd($products);
        return view('frontend.category.show', compact('products','categories'));
    }
}
