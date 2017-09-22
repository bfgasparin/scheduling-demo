<?php

namespace Tests\Unit\Notifications\Salon\Client\Booking;

use Tests\TestCase;
use App\Salon\Client\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Notifications\Salon\Client\Booking\Canceled as BookingCanceled;

/**
 * Test Booking Canceled Notificaiion
 */
class CanceledTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function the_client_is_notified_by_email() : void
    {
        $booking = factory(Booking::class)->create();

        tap(new BookingCanceled($booking), function ($notification) use ($booking) {
            tap($notification->via($booking->client), function (array $channels) {
                $this->assertContains('mail', $channels);
            });

            tap($notification->toMail($booking->client), function (MailMessage $message) use ($booking) {
                $this->assertEquals('mail.salon.client.booking.canceled', $message->markdown);
                $this->assertEquals("Your booking nº {$booking->id} was canceled", $message->subject);
                $this->assertArraySubset(['booking' => $booking], $message->viewData);
            });
        });
    }

    /** @test */
    public function the_salon_is_notified_by_email() : void
    {
        $booking = factory(Booking::class)->create();

        tap(new BookingCanceled($booking), function ($notification) use ($booking) {
            tap($notification->via($booking->salon), function (array $channels) {
                $this->assertContains('mail', $channels);
            });

            tap($notification->toMail($booking->salon), function (MailMessage $message) use ($booking) {
                $this->assertEquals('mail.salon.booking.canceled', $message->markdown);
                $this->assertEquals('A booking was canceled', $message->subject);
                $this->assertArraySubset(['booking' => $booking], $message->viewData);
            });
        });
    }
}
