<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Artisan;
use Cache;
use CoreComponentRepository;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_dashboard(Request $request)
    {
        // CoreComponentRepository::initializeCache();
        $root_categories = Category::where('level', 0)->get();

        $cached_graph_data = Cache::remember('cached_graph_data', 86400, function () use ($root_categories) {
            $num_of_sale_data = null;
            $qty_data = null;
            foreach ($root_categories as $category) {
                $category_ids = \App\Utility\CategoryUtility::children_ids($category->id);
                $category_ids[] = $category->id;

                $products = Product::with('stocks')->whereIn('category_id', $category_ids)->get();

                $qty = $products->sum(fn($p) => $p->stocks->sum('qty'));
                $sale = $products->sum('num_of_sale');

                $qty_data .= $qty . ',';
                $num_of_sale_data .= $sale . ',';
            }

            return [
                'num_of_sale_data' => $num_of_sale_data,
                'qty_data' => $qty_data
            ];
        });

        return view('backend.dashboard', compact('root_categories', 'cached_graph_data'));
    }


    function clearCache(Request $request)
    {
        Artisan::call('cache:clear');
        flash(translate('Cache cleared successfully'))->success();
        return back();
    }
}
