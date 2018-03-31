<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Order;
use App\Mail\OrderConfirmationEmail;

class OrderConfirmationEmailTest extends TestCase
{
    /** @test */
    public function email_contains_a_link_to_the_order_confirmation_page()
    {
        $order = factory(Order::class)->make([
            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);

        $email = new OrderConfirmationEmail($order);
        $rendered = $this->render($email);
        
        $this->assertContains(url('/orders/ORDERCONFIRMATION1234'), $rendered);
    }

    /** @test */
    public function email_has_a_subject()
    {
        $order = factory(Order::class)->make();

        $email = new OrderConfirmationEmail($order);

        $this->assertEquals("Your TicketBeast Order", $email->build()->subject);
    }

    private function render($mailable)
    {
        $mailable->build();
        return view($mailable->view, $mailable->buildViewData())->render();
    }
}
