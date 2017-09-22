<?php

namespace Tests\Unit\Jobs\Salon\Client\Booking;

use Bus;
use App\User;
use Notification;
use Carbon\Carbon;
use Tests\TestCase;
use App\Salon\{Client, Client\Booking};
use Tests\Concerns\SalonClientBookingHelpers;
use App\Salon\Config\Booking as ConfigBooking;
use App\Jobs\Salon\Client\Booking\Cancel as CancelBooking;
use App\Notifications\Salon\Client\Booking\CancelFailed as BookingCancelFailed;
use App\Jobs\Salon\Client\Booking\CancelThroughUser as CancelBookingThroughUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Exceptions\Salon\Client\Booking\{
    Exception as BookingException,
    BookingDoesNotBelongsToClientUser,
    CancelToleranceForClientUserExceeded
};

/**
 * Tests of 'Client USer Canceling a Booking' Use Case
 */
class CancelThroughUserTest extends TestCase
{
    use DatabaseTransactions,
        SalonClientBookingHelpers;

    /** @before */
    public function fakeNotifications() : void
    {
        Bus::fake();
        Notification::fake();
    }

    /** @test */
    public function a_booking_is_canceled()
    {
        $booking = $this->createBooking();

        tap(new CancelBookingThroughUser($booking->client->user, $booking))->handle();

        Bus::assertDispatched(CancelBooking::class, function ($job) use ($booking) {
            return $job->booking->is($booking);
        });
    }

    /** @test */
    public function a_client_user_can_cancel_a_booking_at_the_limit_of_the_cancel_tolerance_for_client_user()
    {
        $tolerance = $this->salon->configBooking->cancel_tolerance_for_client_user;

        // Given we have a booking dated at the limit of cancel tolerance time of the salon
        $booking = factory(Booking::class)->create([
            'client_id' => factory(Client::class)->lazy(['salon_id' => $this->salon->id]),
            'start' => Carbon::now()->subMinutes($tolerance)->toTimeString(),
            'date' => Carbon::today(),
        ]);

        tap(new CancelBookingThroughUser($booking->client->user, $booking))->handle();

        Bus::assertDispatched(CancelBooking::class, function ($job) use ($booking) {
            return $job->booking->is($booking);
        });
    }

    /** @test */
    public function a_client_user_can_not_cancel_a_bookings_if_the_client_user_does_not_own_it()
    {
        $booking = $this->createBooking();
        $user = factory(User::class)->create();

        $this->catchException(BookingDoesNotBelongsToClientUser::class, function () use ($booking, $user) {
            tap(new CancelBookingThroughUser($user, $booking))->handle();
        });

        Bus::assertNotDispatched(CancelBooking::class, function ($job) use ($booking) {
            return $job->booking->is($booking);
        });
    }

    /** @test */
    public function a_client_user_can_not_canncel_a_booking_that_exced_the_cancel_tolerance_for_client_user()
    {
        $tolerance = $this->salon->configBooking->cancel_tolerance_for_client_user;

        // Given we have a booking dated 1 minute after the cancel tolerance time
        $booking = factory(Booking::class)->create([
            'client_id' => factory(Client::class)->lazy(['salon_id' => $this->salon->id]),
            'start' => Carbon::now()->subMinutes($tolerance - 1)->toTimeString(),
            'date' => Carbon::today(),
        ]);

        $this->catchException(CancelToleranceForClientUserExceeded::class, function () use ($booking) {
            tap(new CancelBookingThroughUser($booking->client->user, $booking))->handle();
        });

        Bus::assertNotDispatched(CancelBooking::class, function ($job) use ($booking) {
            return $job->booking->is($booking);
        });
    }

    /** @test */
    public function the_client_user_is_notified_when_the_booking_cancel_fails()
    {
        $booking = factory(Booking::class)->create();

        $job = tap(new CancelBookingThroughUser($booking->client->user, $booking))->failed(
            new class($reason = 'Some message') extends \Exception implements BookingException {}
        );

        Notification::assertSentTo(
            $job->user,
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

    /**
     * Gets an existing valid booking
     *
     * @param ...$states
     * @return App\Salon\Client\Booking
     */
    protected function createBooking(...$states) : Booking
    {
        $minutesToday =  Carbon::today()->diffInMinutes();
        $tolerance = $this->salon->configBooking->cancel_tolerance_for_client_user;

        // Return a booking that not exceeds the cancel tolerance of client user time of the salon
        return factory(Booking::class)->states($states)->create([
            'client_id' => factory(Client::class)->lazy(['salon_id' => $this->salon->id]),
            'start' => Carbon::now()->subMinutes($tolerance + rand(1, $minutesToday - $tolerance))->toTimeString(),
            'date' => Carbon::today(),
        ]);
    }
}
