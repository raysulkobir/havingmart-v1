<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;

/**
 * HavingMart A-Z storefront suite.
 * Read-only: GET requests + DB assertions against existing data.
 * Does NOT use RefreshDatabase (must never wipe the live catalogue).
 */
class StorefrontTest extends TestCase
{
    // ---------- Public pages ----------
    public function test_homepage_loads()      { $this->get('/')->assertStatus(200)->assertSee('HavingMart', false); }
    public function test_login_page_loads()    { $this->get('/login')->assertStatus(200); }
    public function test_register_page_loads() { $this->get('/register')->assertStatus(200); }
    public function test_search_page_loads()   { $this->get('/search')->assertStatus(200); }
    public function test_brands_page_loads()   { $this->get('/brands')->assertStatus(200); }
    public function test_flash_deals_loads()   { $this->get('/flash-deals')->assertStatus(200); }
    public function test_cart_page_loads()     { $this->get('/cart')->assertStatus(200); }

    // ---------- Beauty category pages ----------
    public function test_category_pages_load()
    {
        foreach (['skincare', 'korean-beauty', 'makeup', 'haircare', 'personal-care', 'fragrance-perfume', 'beauty-tools-device'] as $slug) {
            $this->get("/category/{$slug}")->assertStatus(200);
        }
    }

    // ---------- Product details ----------
    public function test_product_details_loads()
    {
        $product = Product::where('published', 1)->first();
        $this->assertNotNull($product, 'There should be at least one published product');
        $this->get(route('product', $product->slug))->assertStatus(200);
    }

    // ---------- Design system ----------
    public function test_brand_colors_applied()
    {
        $this->assertEquals('#DF1F8F', get_setting('base_color'), 'Primary brand colour');
        $this->assertEquals('#C9177C', get_setting('base_hov_color'), 'Primary hover colour');
    }

    // ---------- Navigation is beauty-only & dynamic ----------
    public function test_navigation_is_beauty_only()
    {
        $visible = Category::where('level', 0)->where('status', 1)->pluck('name')->map(fn($n) => strtolower($n));
        $this->assertTrue($visible->contains('skincare'), 'Skincare should be in the nav');
        $this->assertTrue($visible->contains('makeup'), 'Makeup should be in the nav');
        foreach (['baby', 'sexual', 'herbal', 'digestive', 'hormone', 'teether'] as $bad) {
            $this->assertFalse($visible->contains(fn($n) => str_contains($n, $bad)), "Non-beauty '{$bad}' must not be in the nav");
        }
    }

    // ---------- Beauty-only catalogue ----------
    public function test_beauty_catalogue_is_curated()
    {
        $this->assertGreaterThan(5000, Product::where('published', 1)->count(), 'Beauty products should be visible');
        $this->assertNotNull(Category::where('slug', 'korean-beauty')->first(), 'Korean Beauty category exists');
    }

    // ---------- Homepage merchandising ----------
    public function test_homepage_merchandising_configured()
    {
        $homeCats = json_decode(get_setting('home_categories'), true);
        $this->assertIsArray($homeCats);
        $this->assertContains('2', $homeCats, 'Skincare in Shop-by-Category');

        $slides = json_decode(get_setting('home_slider_images'), true);
        $this->assertIsArray($slides);
        $this->assertGreaterThanOrEqual(3, count($slides), 'Slider should have multiple (dynamic) slides');

        $this->assertGreaterThanOrEqual(1, Brand::where('top', 1)->count(), 'Featured brands configured');
    }

    // ---------- Platform ----------
    public function test_laravel_13_on_php_83()
    {
        $this->assertStringStartsWith('13.', app()->version());
        $this->assertTrue(version_compare(PHP_VERSION, '8.3.0', '>='), 'PHP 8.3+');
    }

    // ---------- No hardcoded Bengali in cart buttons ----------
    public function test_cart_buttons_are_english()
    {
        $box = file_get_contents(resource_path('views/frontend/partials/product_box_1.blade.php'));
        $this->assertStringNotContainsString('ব্যাগ এ রাখুন', $box);
        $this->assertStringNotContainsString('এখনি কিনুন', $box);
    }
}
