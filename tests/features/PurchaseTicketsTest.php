<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Concert;
use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    /** @test */
    public function customer_can_purchase_concert_tickets()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 3250]);

        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertResponseStatus(201);

        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);

        $this->assertEquals(3, $order->tickets()->count());
    }

    /** @test */
    public function email_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->create();

        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertResponseStatus(422);

        $this->assertArrayHasKey('email', $this->decodeResponseJson());
    }

    /** @test */
    public function email_must_be_valid_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->create();
        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'not-an-email-address',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey('email', $this->decodeResponseJson());
    }

    /** @test */
    public function ticket_quantity_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->create();
        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey('ticket_quantity', $this->decodeResponseJson());
    }

    /** @test */
    public function ticket_quantity_must_be_at_least_1_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->create();
        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey('ticket_quantity', $this->decodeResponseJson());
    }
    
    /** @test */
    public function payment_token_is_required()
    {
        $concert = factory(Concert::class)->create();
        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
        ]);
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey('payment_token', $this->decodeResponseJson());
    }
}
