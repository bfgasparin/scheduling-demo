<?php

namespace Tests\Unit\Jobs\Salon\Client\Booking;

use Notification;
use Carbon\Carbon;
use Tests\TestCase;
use App\Salon\{Client, Client\Booking};
use Tests\Concerns\SalonClientBookingHelpers;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\Salon\Client\Booking\Cancel as CancelBooking;
use App\Exceptions\Salon\Client\Booking\{
    Exception as BookingException,
    CancelToleranceForClientUserExceeded
};
use App\Notifications\Salon\Client\Booking\{
    Canceled as BookingCanceled,
    CancelFailed as BookingCancelFailed
};

/**
 * Tests of 'Canceling a Booking' Use Case
 */
class CancelTest extends TestCase
{
    use DatabaseTransactions,
        SalonClientBookingHelpers;

    /** @before */
    public function fakeNotifications() : void
    {
        Notification::fake();
    }

    /** @test */
    public function a_booking_is_canceled()
    {
        $booking = $this->createBooking();

        tap(new CancelBooking($booking))->handle();

        $this->assertDatabaseHas(
            'client_bookings',
            array_merge($booking->makeHidden('client')->toArray(), ['canceled_at' => Carbon::now()])
        );
    }

    /**
     * @test
     * @expectedException App\Exceptions\Salon\Client\Booking\AlreadyCanceled
     */
    public function a_canceled_booking_can_not_be_canceled_again()
    {
        $booking = $this->createBooking('canceled');

        tap(new CancelBooking($booking))->handle();
    }

    /** @test */
    public function a_past_booking_can_not_be_canceled()
    {
        $booking = $this->createBooking('past');

        tap(new CancelBooking($booking))->handle();

        // TODO
    }

    /** @test */
    public function the_client_is_notified_when_the_booking_is_cancelled()
    {
        $booking = $this->createBooking();

        tap(new CancelBooking($booking))->handle();

        Notification::assertSentTo(
            $booking->client,
            BookingCanceled::class,
            function ($notification, $channels) use ($booking) {
                return $notification->booking->is($booking) && in_array('mail', $channels);
            }
        );
    }

    /** @test */
    public function the_salon_is_notified_when_a_booking_is_cancelled()
    {
        $booking = $this->createBooking();

        tap(new CancelBooking($booking))->handle();

        Notification::assertSentTo(
            $booking->salon,
            BookingCanceled::class,
            function ($notification, $channels) use ($booking) {
                return $notification->booking->is($booking) && in_array('mail', $channels);
            }
        );
    }

    /** @test */
    public function the_client_is_notified_when_the_booking_cancel_fails()
    {
        $booking = factory(Booking::class)->create();

        $job = tap(new CancelBooking($booking))->failed(
            new class($reason = 'Some message') extends \Exception implements BookingException {}
        );

        Notification::assertSentTo(
            $job->booking->client,
            BookingCancelFailed::class,
            function ($notification, $channels) use ($job, $reason) {
                return
                    $notification->reason === $reason &&
                    $notification->booking->is($job->booking) &&
                    in_array('mail', $channels);
            }
        );
    }

    /** @test */
    public function the_salon_is_notified_when_the_booking_cancel_fails()
    {
        $booking = factory(Booking::class)->create();

        $job = tap(new CancelBooking($booking))->failed(
            new class($reason = 'Some message') extends \Exception implements BookingException {}
        );

        Notification::assertSentTo(
            $job->booking->salon,
            BookingCancelFailed::class,
            function ($notification, $channels) use ($job, $reason) {
                return
                    $notification->reason === $reason &&
                    $notification->booking->is($job->booking) &&
                    in_array('mail', $channels);
            }
        );
    }

    /** @test */
    public function the_devs_is_notified_when_booking_cancel_fails_with_an_unexpected_error()
    {
        // TODO
    }
}
