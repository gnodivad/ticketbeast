<?php

namespace Tests\Unit\Jobs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\AttendeeMessage;
use App\Jobs\SendAttendeeMessage;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttendeeMessageEmail;

class SendAttendeeMessageTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_sends_the_message_to_all_concert_attendees()
    {
        Mail::fake();

        $concert = \ConcertFactory::createPublished();
        $otherConcert = \ConcertFactory::createPublished();

        $message = AttendeeMessage::create([
            'concert_id' => $concert->id,
            'subject' => 'My subject',
            'message' => 'My message'
        ]);
        $orderA = \OrderFactory::createForConcert($concert, ['email' => 'alex@example.com']);
        $orderOrder = \OrderFactory::createForConcert($otherConcert, ['email' => 'jane@example.com']);
        $orderB = \OrderFactory::createForConcert($concert, ['email' => 'sam@example.com']);
        $orderC = \OrderFactory::createForConcert($concert, ['email' => 'taylor@example.com']);

        SendAttendeeMessage::dispatch($message);

        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('alex@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('sam@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('taylor@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertNotQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('jabe@example.com');
        });
    }
}
