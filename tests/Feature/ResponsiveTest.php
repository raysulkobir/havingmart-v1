<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Responsive fundamentals (what can be asserted server-side).
 * Visual breakpoint behaviour must still be eyeballed in a browser / device.
 */
class ResponsiveTest extends TestCase
{
    public function test_homepage_has_responsive_viewport()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('width=device-width', false)
            ->assertSee('initial-scale=1', false);
    }

    public function test_homepage_has_mobile_navigation()
    {
        // mobile-only hamburger + mobile toggles must be present for small screens
        $this->get('/')->assertSee('d-lg-none', false);
    }

    public function test_category_page_is_responsive()
    {
        $this->get('/category/skincare')
            ->assertStatus(200)
            ->assertSee('width=device-width', false);
    }

    public function test_product_page_is_responsive()
    {
        $product = \App\Models\Product::where('published', 1)->first();
        $this->get(route('product', $product->slug))
            ->assertStatus(200)
            ->assertSee('width=device-width', false);
    }
}
