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
        //    return $this->updateImage();
           return $this->addProductStock();

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
            ->groupBy('main_category', 'subcategory', 'sub_sub_category', 'sub_sub_sub_category')
            ->get();

        $mainCache   = [];
        $subCache    = [];
        $subSubCache = [];
        $now         = now();

        $buildRecord = fn(string $name, int $parentId, int $level) => [
            'slug'       => Str::slug($name),
            'parent_id'  => $parentId,
            'level'      => $level,
            'status'     => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $upsertAndCache = function (string $name, int $parentId, int $level) use ($buildRecord) {
            DB::table('categories')->updateOrInsert(
                ['name' => $name, 'parent_id' => $parentId, 'level' => $level],
                $buildRecord($name, $parentId, $level)
            );

            return DB::table('categories')
                ->where(['name' => $name, 'parent_id' => $parentId, 'level' => $level])
                ->value('id');
        };

        foreach ($rows as $row) {

            // Level 0 — Main Category
            if (!array_key_exists($row->main_category, $mainCache)) {
                $mainCache[$row->main_category] = $upsertAndCache($row->main_category, 0, 0);
            }
            $mainId = $mainCache[$row->main_category];

            // Level 1 — Sub Category
            $subKey = "{$row->main_category}|{$row->subcategory}";
            if (!array_key_exists($subKey, $subCache)) {
                $subCache[$subKey] = $upsertAndCache($row->subcategory, $mainId, 1);
            }
            $subId = $subCache[$subKey];

            // Level 2 — Sub-Sub Category
            $subSubKey = "{$subKey}|{$row->sub_sub_category}";
            if (!array_key_exists($subSubKey, $subSubCache)) {
                $subSubCache[$subSubKey] = $upsertAndCache($row->sub_sub_category, $subId, 2);
            }
            $subSubId = $subSubCache[$subSubKey];

            // Level 3 — Sub-Sub-Sub Category (no cache needed, always leaf)
            if (!empty($row->sub_sub_sub_category)) {
                DB::table('categories')->updateOrInsert(
                    ['name' => $row->sub_sub_sub_category, 'parent_id' => $subSubId, 'level' => 3],
                    $buildRecord($row->sub_sub_sub_category, $subSubId, 3)
                );
            }
        }

        return response()->json(['status' => 'ok', 'processed' => $rows->count()]);
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
            ->limit(500)
            ->get();

        foreach ($addProducts as $pro) {
            // return $pro;
            // set category 
            if($pro->sub_sub_category){
                $category = Category::where('name', $pro->sub_sub_category)->first();
            }else if($pro->subcategory){
                $category = Category::where('name', $pro->subcategory)->first();
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

            // return __LINE__;

            // return $category;

            if(!isset($category->id)){
                continue;
            }
            // if(isset($pro->original_price) && isset($pro->current_price)){
            //     $discount = (int) $pro->original_price - (int) $pro->current_price;
            // }

            $product = new Product();
            $product->name = $pro->title;
            $product->meta_title = $pro->title. "Price in BD";
            $product->meta_description = $pro->title." Price in Bangladesh";
            $product->added_by = 1;
            $product->category_id = $category->id;
            $product->brand_id = $brand->id;
            $product->purchase_price = (int) filter_var($pro->current_price, FILTER_SANITIZE_NUMBER_INT);
            $product->unit_price = (int) filter_var($pro->original_price, FILTER_SANITIZE_NUMBER_INT);
            $product->discount = 0;
            $product->attributes = "attributes";
            $product->stock_visibility_state = 0;
            $product->unit = $unit ? $unit : '';
            $product->published = 1;
            $product->approved = 1;
            // $product->colors = [];
            $product->slug = Str::slug($pro->title);
            $product->description = $pro->description;
            
            // return $product;
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

        return "Product stock added successfully.";
    }



    

    
}
