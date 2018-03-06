<?php

use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Reservation;

class RevervationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function calculating_the_total_cost()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(3);
        $tickets = $concert->findTickets(3);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }
}
