<?php

namespace App\Http\Controllers;

use App\Models\Shop\Category;
use App\Models\Shop\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public function __construct()
    {
        view()->share([
            "__products" => Product::all(),
            "__categories" => Category::all(),
        ]);
    }
}
