<?php

namespace Tests\Unit\Jobs\Salon\Client\Booking;

use Notification;
use Tests\TestCase;
use Tests\Concerns\SalonClientBookingHelpers;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\Salon\Client\Booking\Create as CreateBooking;
use App\Exceptions\Salon\Client\Booking\Exception as BookingException;
use App\Notifications\Salon\Client\Booking\CreationFailed as BookingCreationFailed;

/**
 * Tests of 'Booking a Service' Use Case
 */
class CreateTest extends TestCase
{
    use DatabaseTransactions,
        SalonClientBookingHelpers;

    /** @test */
    public function a_booking_is_created()
    {
        $data = $this->bookingData();
        $client = $this->createClient();

        tap(new CreateBooking($client, $data))->handle();

        $this->assertDatabaseHas('client_bookings', array_merge(
            $data, ['client_id' => $client->id, 'canceled_at' => null]
        ));
    }

    /** @test */
    public function the_client_is_notified_when_the_booking_creation_fails()
    {
        Notification::fake();

        $data = $this->bookingData();
        $client = $this->createClient();

        $job = tap(new CreateBooking($client, $data))->failed(
            new class($reason = 'Some message') extends \Exception implements BookingException {}
        );

        Notification::assertSentTo(
            $client,
            BookingCreationFailed::class,
            function ($notification, $channels) use ($job, $reason) {
                return
                    $notification->salon->is($job->client->salon) &&
                    $notification->reason === $reason &&
                    $notification->data === ($job->data) &&
                    in_array('mail', $channels);
            }
        );
    }

    /** @test */
    public function the_salon_is_notified_when_the_booking_cancel_fails()
    {
        Notification::fake();

        $data = $this->bookingData();
        $client = $this->createClient();

        $job = tap(new CreateBooking($client, $data))->failed(
            new class($reason = 'Some message') extends \Exception implements BookingException {}
        );

        Notification::assertSentTo(
            $client->salon,
            BookingCreationFailed::class,
            function ($notification, $channels) use ($job, $reason) {
                return
                    $notification->salon->is($job->client->salon) &&
                    $notification->reason === $reason &&
                    $notification->data === ($job->data) &&
                    in_array('mail', $channels);
            }
        );
    }
}
