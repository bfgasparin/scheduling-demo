<?php

namespace Tests\Unit\Notifications\Salon\Client\Booking;

use Tests\TestCase;
use App\Salon\Service;
use Tests\Concerns\SalonClientBookingHelpers;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Notifications\Salon\Client\Booking\CreationFailed as BookingCreationFailed;

/**
 * Test Booking CreationFailed Notificaiion
 */
class CreationFailedTest extends TestCase
{
    use DatabaseTransactions,
        SalonClientBookingHelpers;

    /** @test */
    public function the_client_is_notified_by_email() : void
    {
        $data = $this->bookingData();
        $reason = str_random(30);

        tap(new BookingCreationFailed($this->salon, $data, $reason), function ($notification) use ($data, $reason) {
            $client = $this->createClient();

            tap($notification->via($client), function (array $channels) {
                $this->assertContains('mail', $channels);
            });

            tap($notification->toMail($client), function (MailMessage $message) use ($data, $reason) {
                $this->assertEquals('mail.salon.client.booking.creation-failed', $message->markdown);
                $this->assertEquals("Your booking could not be created", $message->subject);
                $this->assertArraySubset(
                    [
                        'salon' => $this->salon,
                        'data' => $data,
                        'reason' => $reason,
                        'service' => Service::find($data['service_id']),
                    ],
                    $message->viewData
                );
            });
        });
    }

    /** @test */
    public function the_client_user_is_notified_by_email() : void
    {
        $data = $this->bookingData();
        $reason = str_random(30);

        tap(new BookingCreationFailed($this->salon, $data, $reason), function ($notification) use ($data, $reason) {
            $user = $this->createClient()->user;

            tap($notification->via($user), function (array $channels) {
                $this->assertContains('mail', $channels);
            });

            tap($notification->toMail($user), function (MailMessage $message) use ($data, $reason) {
                $this->assertEquals('mail.salon.client.booking.creation-failed', $message->markdown);
                $this->assertEquals("Your booking could not be created", $message->subject);
                $this->assertArraySubset(
                    [
                        'salon' => $this->salon,
                        'data' => $data,
                        'reason' => $reason,
                        'service' => Service::find($data['service_id']),
                    ],
                    $message->viewData
                );
            });
        });
    }

    /** @test */
    public function the_salon_is_notified_by_email() : void
    {
        $data = $this->bookingData();
        $reason = str_random(30);

        tap(new BookingCreationFailed($this->salon, $data, $reason), function ($notification) use ($data, $reason) {
            tap($notification->via($this->salon), function (array $channels) {
                $this->assertContains('mail', $channels);
            });

            tap($notification->toMail($this->salon), function (MailMessage $message) use ($data, $reason) {
                $this->assertEquals('mail.salon.booking.creation-failed', $message->markdown);
                $this->assertEquals("A booking creation failed", $message->subject);
                $this->assertArraySubset(
                    [
                        'salon' => $this->salon,
                        'data' => $data,
                        'reason' => $reason,
                        'service' => Service::find($data['service_id']),
                    ],
                    $message->viewData
                );
            });
        });

    }
}
