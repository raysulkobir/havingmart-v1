<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Product;
use App\Models\Cart;

/**
 * Cart + checkout flow.
 * DatabaseTransactions rolls back every test (cart rows / orders never persist,
 * and the live catalogue is never wiped). No real payment gateway is triggered.
 */
class CartCheckoutTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Cart/checkout are AJAX POSTs; skip only CSRF (keep session middleware for the guest cart).
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    private function simpleProduct(): ?Product
    {
        return Product::where('published', 1)->where('digital', 0)->where('auction_product', 0)->first();
    }

    private function qtyFor(Product $p): int
    {
        return max((int) ($p->min_qty ?: 1), 1);
    }

    private function addOne(Product $p)
    {
        return $this->post(route('cart.addToCart'), ['id' => $p->id, 'quantity' => $this->qtyFor($p)]);
    }

    public function test_add_to_cart_creates_a_cart_row()
    {
        $p = $this->simpleProduct();
        $this->assertNotNull($p, 'Need a published simple product');
        $before = Cart::count();
        $this->addOne($p)->assertStatus(200);
        $this->assertGreaterThan($before, Cart::count(), 'Add to cart should create a cart row');
    }

    public function test_cart_page_loads_with_item()
    {
        $p = $this->simpleProduct();
        $this->addOne($p);
        $this->get(route('cart'))->assertStatus(200);
    }

    public function test_update_cart_quantity()
    {
        $p = $this->simpleProduct();
        $this->addOne($p);
        $cart = Cart::latest('id')->first();
        $this->assertNotNull($cart);
        $this->post(route('cart.updateQuantity'), ['id' => $cart->id, 'quantity' => $this->qtyFor($p) + 1])
             ->assertStatus(200);
        $this->assertEquals($this->qtyFor($p) + 1, Cart::find($cart->id)->quantity);
    }

    public function test_remove_from_cart()
    {
        $p = $this->simpleProduct();
        $this->addOne($p);
        $cart = Cart::latest('id')->first();
        $before = Cart::count();
        $this->post(route('cart.removeFromCart'), ['id' => $cart->id])->assertStatus(200);
        $this->assertLessThan($before, Cart::count(), 'Remove should delete the cart row');
    }

    public function test_show_cart_modal()
    {
        $p = $this->simpleProduct();
        $this->addOne($p);
        $this->post(route('cart.showCartModal'), ['id' => $p->id])->assertStatus(200);
    }

    public function test_checkout_shipping_reachable_with_cart()
    {
        $p = $this->simpleProduct();
        $this->addOne($p);
        $res = $this->get(route('checkout.shipping_info'));
        // Guest checkout either shows the shipping page (200) or redirects to login (302) — never a 500.
        $this->assertContains($res->status(), [200, 302], 'Checkout entry must not error');
    }
}
