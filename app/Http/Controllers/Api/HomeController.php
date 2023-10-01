<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Shop\Category;
use App\Models\Shop\Product;
use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_visible', true)->with('products')->get();
        $products = Product::where('is_visible', true)
            ->with('categories')
            ->with('reasons')
            ->with('compositions')
            ->inRandomOrder()
            ->take(8)
            ->get();

        $bestSelling = Product::with('categories')->inRandomOrder()->take(8)->get();
        $newProducts = Product::orderBy('created_at' , 'desc')->paginate(8);
        $slides      = Slide::where('is_visible', true)->get();

        return view('welcome', compact('products', 'categories', 'newProducts', 'bestSelling' , 'slides'));
    }
}
