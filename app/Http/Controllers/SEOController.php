<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Category;
use App\Models\FlashDeal;
use App\Models\Cart;
use App\Models\Brand;
use App\Models\Blog;
use App\Models\Product;
use App\Models\PickupPoint;
use App\Models\CustomerPackage;
use App\Models\User;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\City;
use Cookie;
use Illuminate\Support\Str;
use App\Mail\SecondEmailVerifyMailManager;
use App\Models\AffiliateConfig;
use App\Models\Page;
use App\Models\ProductQuery;
use Mail;
use Illuminate\Auth\Events\PasswordReset;
use Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SEOController extends Controller
{
   public function sitemap()
    {
        $products = Product::select('name', 'slug', 'updated_at')->get();
        $blogs = Blog::where('status', 1)->select('title', 'slug', 'updated_at')->get();

        return response()->view('frontend.SEO.sitemap', [
            'products' => $products,
            'blogs' => $blogs,
        ])->header('Content-Type', 'text/xml');
    }

    // XML Feed Generate
    public function generateXMLFeed()
    {
        // return Brand::find(1)->name;
        // return "ok";
        $products = Product::get();

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"></rss>');
        $channel = $xml->addChild('channel');
        $channel->addChild('title', 'Product Feed');
        $channel->addChild('link', url('/'));
        $channel->addChild('description', 'Facebook Product Catalog Feed');

        foreach ($products as $product) {
            $item = $channel->addChild('item');
            $item->addChild('g:id', $product->id, 'http://base.google.com/ns/1.0');
            $item->addChild('g:title', htmlspecialchars($product->name), 'http://base.google.com/ns/1.0');
            $item->addChild('g:description', htmlspecialchars($product->description), 'http://base.google.com/ns/1.0');
            $item->addChild('g:link', "https://havingmart.com/product/" . $product->slug, 'http://base.google.com/ns/1.0');
            $item->addChild('g:image_link', "https://havingmart.com/public/" . $product->thumbnail_img, 'http://base.google.com/ns/1.0');
            $item->addChild('g:availability', "in stock", 'http://base.google.com/ns/1.0');
            $item->addChild('g:price', ((int)$product->unit_price - (int)$product->discount) . ' BDT', 'http://base.google.com/ns/1.0');
            $item->addChild('g:condition', "new", 'http://base.google.com/ns/1.0');

            if ($product->unit_price) {
                $item->addChild('g:sale_price', $product->unit_price . ' BDT', 'http://base.google.com/ns/1.0');
            }
        }

        return response($xml->asXML(), 200)
            ->header('Content-Type', 'application/xml');
    }
}
