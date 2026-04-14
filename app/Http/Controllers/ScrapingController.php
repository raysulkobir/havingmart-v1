<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\FuncCall;

class ScrapingController extends Controller
{

    public function all()
    {
        // $this->brand();
        // $this->category();
        // $this->addProducts();
       return $this->updateImage();

        return "Success";
    }

    public function category()
    {
        $rows = DB::connection('mysql_second')
            ->table('products')
            ->select(
                'main_category',
            'subcategory',
                'sub_sub_category',
                'sub_sub_sub_category',
                DB::raw('COUNT(*) as total_products')
            )
            ->groupBy(
                'main_category',
            'subcategory',
                'sub_sub_category',
                'sub_sub_sub_category'
            )
            ->get();

        $mainCache      = [];
        $subCache       = [];
        $subSubCache    = [];

        foreach ($rows as $row) {

            /** ---------- MAIN CATEGORY (Level 0) ---------- */
            if (!isset($mainCache[$row->main_category])) {

                DB::table('categories')->updateOrInsert(
                    [
                        'name'  => $row->main_category,
                        'level' => 0
                    ],
                    [
                        'parent_id' => 0,
                        'slug' => Str::slug($row->main_category),
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $mainCache[$row->main_category] = DB::table('categories')
                    ->where('name', $row->main_category)
                    ->where('level', 0)
                    ->value('id');
            }

            /** ---------- SUB CATEGORY (Level 1) ---------- */
            $subKey = $row->main_category . '_' . $row->subcategory;

            if (!isset($subCache[$subKey])) {

                DB::table('categories')->updateOrInsert(
                    [
                        'name' => $row->subcategory,
                        'parent_id' => $mainCache[$row->main_category],
                        'level' => 1,
                    ],
                    [
                        'slug' => Str::slug($row->subcategory),
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $subCache[$subKey] = DB::table('categories')
                    ->where('name', $row->subcategory)
                    ->where('parent_id', $mainCache[$row->main_category])
                    ->where('level', 1)
                    ->value('id');
            }

            /** ---------- SUB-SUB CATEGORY (Level 2) ---------- */
            $subSubKey = $subKey . '_' . $row->sub_sub_category;

            if (!isset($subSubCache[$subSubKey])) {

                DB::table('categories')->updateOrInsert(
                    [
                        'name' => $row->sub_sub_category,
                        'parent_id' => $subCache[$subKey],
                        'level' => 2,
                    ],
                    [
                        'slug' => Str::slug($row->sub_sub_category),
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $subSubCache[$subSubKey] = DB::table('categories')
                    ->where('name', $row->sub_sub_category)
                    ->where('parent_id', $subCache[$subKey])
                    ->where('level', 2)
                    ->value('id');
            }

            /** ---------- SUB-SUB-SUB CATEGORY (Level 3) ---------- */
            if (!empty($row->sub_sub_sub_category)) {

                DB::table('categories')->updateOrInsert(
                    [
                        'name' => $row->sub_sub_sub_category,
                        'parent_id' => $subSubCache[$subSubKey],
                        'level' => 3,
                    ],
                    [
                        'slug' => Str::slug($row->sub_sub_sub_category),
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        return "ok";
    }

    public function brand()
    {
        $rows = DB::connection('mysql_second')
            ->table('products')
            ->select(
                'brand',
                DB::raw('COUNT(*) as total_products')
            )
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->get();

        foreach ($rows as $row) {
            DB::table('brands')->updateOrInsert(
                [
                    'name' => $row->brand,
                    'slug' => Str::slug($row->brand)
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return 'ok';
    }

    public function unit(){
        $rows = DB::connection('mysql_second')
            ->table('products')
            ->select(
            'size_pack',
                DB::raw('COUNT(*) as total_products')
            )
            ->whereNotNull('size_pack')
            ->groupBy('size_pack')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $words = preg_split('/\s+/', trim($row->size_pack));
            $lastWord = end($words);
            $result[] = $lastWord;
        }

        return $result;
    }


    public function addProducts()
    {
        $addProducts = DB::connection('mysql_second')
            ->table('products')
            ->where('type', 0)
            // ->limit(5000)
            ->get();

        foreach ($addProducts as $pro) {
            // return $pro;
            // set category 
            if($pro->sub_sub_sub_category){
                $category = Category::where('name', $pro->sub_sub_sub_category)->first();
            }else if($pro->sub_sub_category){
                $category = Category::where('name', $pro->sub_sub_category)->first();
            }else if($pro->sub_category){
                $category = Category::where('name', $pro->sub_category)->first();
            }else if($pro->main_category){
                $category = Category::where('name', $pro->main_category)->first();
            }

            if($pro->brand){
                $brand = Brand::where('name', $pro->brand)->first();
            }

            if($pro->size_pack){
                $words = preg_split('/\s+/', trim($pro->size_pack));
                $unit = end($words);
            }

            if(isset($pro->original_price) && isset($pro->current_price)){
                $discount = (int) $pro->original_price - (int) $pro->current_price;
            }

            $product = new Product();
            $product->name = $pro->title;
            $product->meta_title = $pro->title. "Price in BD";
            $product->meta_description = $pro->title." Price in Bangladesh";
            $product->added_by = 1;
            $product->category_id = $category->id;
            $product->brand_id = $brand->id;
            $product->unit_price = $pro->current_price;
            $product->discount = $discount;
            $product->attributes = "attributes";
            $product->stock_visibility_state = 0;
            $product->unit = $unit ? $unit : '';
            $product->published = 1;
            $product->approved = 1;
            // $product->colors = [];
            $product->slug = Str::slug($pro->title);
            $product->description = $pro->description;
            
            $product->save();

            $this->addImage($pro->id, $product->id, $pro->thumbnail_path);

            $updated = DB::connection('mysql_second')
                ->table('products')
                ->where('id', $pro->id)
                ->where('type', 0)
                ->update(['type' => 1]);
        }

        return "ok";
    }


    private function addImage($id, $productId, $thumbnail_path){

            $upload = new Upload();
            $upload->file_name = "uploads/all/$thumbnail_path";
            $upload->save();


        $product = Product::find($productId);
        $product->photos = $upload->id;
        $product->thumbnail_img = $upload->id;
        $product->save();
    }

    public function updateImage(){
        $addProducts = DB::connection('mysql_second')
            ->table('products')
            ->where('type', 0)
            // ->limit(5000)
            ->get();

            foreach($addProducts as $pro){
            $product =  Product::where('name', $pro->title)->first();
                if(isset($product->thumbnail_img)){
                    $this->addImaged($product->thumbnail_img, $pro->thumbnail_path);
                    
                }
            }
    }

    public function addImaged($id, $thumbnail_path)
    {
        $upload =  Upload::find($id);
        $upload->file_name = "uploads/all/$thumbnail_path";
        $upload->update();
        // return $upload;
    }
    public function addProductStock(){
        $products = Product::get();

        foreach ($products as $product) {

            $exists = ProductStock::where('product_id', $product->id)->exists();

            if (!$exists) {
                $product_stock = new ProductStock();
                $product_stock->product_id = $product->id;
                $product_stock->price      = $product->unit_price;
                $product_stock->qty        = $product->current_stock;
                $product_stock->save();
            }
        }
    }



    

    
}
