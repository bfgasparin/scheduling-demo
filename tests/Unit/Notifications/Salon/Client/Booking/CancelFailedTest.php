<?php

namespace Tests\Unit\Notifications\Salon\Client\Booking;

use Tests\TestCase;
use App\Salon\Client\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Notifications\Salon\Client\Booking\CancelFailed as BookingCancelFailed;

/**
 * Test Booking CancelFailed Notificaiion
 */
class CancelFailedTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function the_client_is_notified_by_email() : void
    {
        $booking = factory(Booking::class)->create();
        $reason = str_random(30);

        tap(new BookingCancelFailed($booking, $reason), function ($notification) use ($booking, $reason) {
            tap($notification->via($booking->client), function (array $channels) {
                $this->assertContains('mail', $channels);
            });

            tap($notification->toMail($booking->client), function (MailMessage $message) use ($booking, $reason) {
                $this->assertEquals('mail.salon.client.booking.cancel-failed', $message->markdown);
                $this->assertEquals("Your booking nº {$booking->id} could not be canceled", $message->subject);
                $this->assertArraySubset(['booking' => $booking, 'reason' => $reason], $message->viewData);
            });
        });
    }

    /** @test */
    public function the_client_user_is_notified_by_email() : void
    {
        $booking = factory(Booking::class)->create();
        $reason = str_random(30);

        tap(new BookingCancelFailed($booking, $reason), function ($notification) use ($booking, $reason) {
            tap($notification->via($booking->client->user), function (array $channels) {
                $this->assertContains('mail', $channels);
            });

            tap($notification->toMail($booking->client->user), function (MailMessage $message) use ($booking, $reason) {
                $this->assertEquals('mail.salon.client.booking.cancel-failed', $message->markdown);
                $this->assertEquals("Your booking nº {$booking->id} could not be canceled", $message->subject);
                $this->assertArraySubset(['booking' => $booking, 'reason' => $reason], $message->viewData);
            });
        });
    }

    /** @test */
    public function the_salon_is_notified_by_email() : void
    {
        $booking = factory(Booking::class)->create();
        $reason = str_random(30);

        tap(new BookingCancelFailed($booking, $reason), function ($notification) use ($booking, $reason) {
            tap($notification->via($booking->salon), function (array $channels) {
                $this->assertContains('mail', $channels);
            });

            tap($notification->toMail($booking->salon), function (MailMessage $message) use ($booking, $reason) {
                $this->assertEquals('mail.salon.booking.cancel-failed', $message->markdown);
                $this->assertEquals('A booking cancel failed', $message->subject);
                $this->assertArraySubset(['booking' => $booking, 'reason' => $reason], $message->viewData);
            });
        });
    }
}
