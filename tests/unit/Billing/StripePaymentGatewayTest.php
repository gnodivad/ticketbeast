<?php

use App\Billing\StripePaymentGateway;

/**
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    private function lastCharge()
    {
        return array_first(\Stripe\Charge::all(
            [
                'limit' => 1
            ],
            ['api_key' => config('services.stripe.secret')]
        )['data']);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->lastCharge = $this->lastCharge();
    }

    protected function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }

    /** @test */
    public function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = $this->getPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        });

        $this->assertCount(1, $newCharges);
        $this->assertEquals(2500, $newCharges->sum());
    }

    /** @test */
    public function charges_with_an_invalid_payment_token_fail()
    {
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));
        $result = $paymentGateway->charge(2500, 'invalid-payment-token');
        $this->assertFalse($result);
    }
}
