<?php

namespace Tests\Unit\Jobs\Salon\Client\Booking;

use Bus;
use Notification;
use Carbon\Carbon;
use Tests\TestCase;
use App\{Salon, User};
use Tests\Concerns\SalonClientBookingHelpers;
use App\Salon\Config\Booking as ConfigBooking;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Salon\{Client\Booking, Client\User as ClientUser, Client};
use App\Exceptions\Salon\Client\Booking\Exception as BookingException;
use App\Exceptions\Salon\Client\Booking\CreateToleranceForClientUserExceeded;
use App\Notifications\Salon\Client\Booking\CreationFailed as BookingCreationFailed;
use App\Jobs\Salon\Client\Booking\{
    Create as CreateBooking,
        CreateThroughUser as CreateBookingThroughUser
};

/**
 * Tests of 'User Books a Service' Use Case
 */
class CreateThroughUserTest extends TestCase
{
    use DatabaseTransactions,
        SalonClientBookingHelpers;

    /** @test */
    public function user_books_the_service()
    {
        Bus::fake();

        $data = $this->bookingData();
        $client = factory(Client::class)->create([
            'salon_id' => $this->salon->id,
        ]);

        $user = $this->createUserWithClients($client);

        tap(new CreateBookingThroughUser($user, $client->salon, $data))->handle();

        Bus::assertDispatched(CreateBooking::class, function ($job) use ($client, $data) {
            return $job->client->is($client);
            return $job->data === $data;
        });
    }

    /** @test */
    public function a_new_client_is_created_for_the_user_when_the_user_does_not_represent_a_client_on_the_salon()
    {
        Bus::fake();

        $user = $this->createUserWithClients();
        $data = $this->bookingData();

        $this->assertDatabaseMissing('salon_clients', array_merge(
            ['salon_id' => $this->salon->id, 'user_id' => $user->id]
        ));

        tap((new CreateBookingThroughUser($user, $this->salon, $data))->handle(), function ($client) use ($user, $data) {
            $this->assertEquals($this->salon->id, $client->salon_id);
            $this->assertEquals($user->id, $client->user_id);
            $this->assertEquals($user->name, $client->name);
            $this->assertEquals($user->email, $client->email);
            $this->assertEquals($user->cellphone, $client->cellphone);

            $this->assertDatabaseHas('salon_clients', $client->makeHidden(['user', 'salon'])->toArray());

            Bus::assertDispatched(CreateBooking::class, function ($job) use ($client, $data) {
                return $job->client->is($client);
                return $job->data === $data;
            });
        });
    }

    /** @test */
    public function user_can_create_a_booking_if_time_is_in_the_limit_of_create_tolerance_for_client_user()
    {
        Bus::fake();

        $client = factory(Client::class)->create([
            'salon_id' => $this->salon->id,
        ]);
        $user = $this->createUserWithClients($client);

        $start = Carbon::now()
            ->subMinutes($this->salon->configBooking->create_tolerance_for_client_user)
            ->toTimeString();

        // Tries to create a booking int limit of the salon create tolerance
        $data = $this->bookingData(['date' => Carbon::today(), 'start' => $start]);

        tap(new CreateBookingThroughUser($user, $this->salon, $data))->handle();

        Bus::assertDispatched(CreateBooking::class, function ($job) use ($client, $data) {
            return $job->client->is($client);
            return $job->data === $data;
        });
    }

    /** @test */
    public function user_can_not_create_a_booking_if_time_exceeds_the_create_tolerance_for_client_user()
    {
        Bus::fake();

        $client = factory(Client::class)->create([
            'salon_id' => $this->salon->id,
        ]);
        $user = $this->createUserWithClients($client);

        $start = Carbon::now()
            ->subMinutes($this->salon->configBooking->create_tolerance_for_client_user - 1)
            ->toTimeString();

        // Tries to create a booking that exceeds the salon create tolerance
        $data = $this->bookingData(['date' => Carbon::today(), 'start' => $start]);

        $this->catchException(CreateToleranceForClientUserExceeded::class, function () use ($user, $client, $data) {
            tap(new CreateBookingThroughUser($user, $this->salon, $data))->handle();
        });

        Bus::assertNotDispatched(CreateBooking::class, function ($job) use ($client, $data) {
            return $job->client->is($client);
            return $job->data === $data;
        });
    }

    /** @test */
    public function the_client_user_is_notified_when_the_booking_creation_fails()
    {
        Notification::fake();

        $user = $this->createUserWithClients();
        $data = $this->bookingData();

        $job = tap(new CreateBookingThroughUser($user, $this->salon, $data))->failed(
            new class($reason = 'Some message') extends \Exception implements BookingException {}
        );

        Notification::assertSentTo(
            $job->user,
            BookingCreationFailed::class,
            function ($notification, $channels) use ($job, $reason) {
                return
                    $notification->salon->is($job->salon) &&
                    $notification->reason === $reason &&
                    $notification->data === ($job->data) &&
                    in_array('mail', $channels);
            }
        );
    }

    /** @test */
    public function the_salon_is_not_notified_when_the_booking_creation_fails()
    {
        Notification::fake();

        $user = $this->createUserWithClients();
        $data = $this->bookingData();

        $job = tap(new CreateBookingThroughUser($user, $this->salon, $data))->failed(
            new class($reason = 'Some message') extends \Exception implements BookingException {}
        );

        Notification::assertSentTo(
            $job->user,
            BookingCreationFailed::class,
            function ($notification, $channels) use ($job, $reason) {
                return
                    $notification->salon->is($job->salon) &&
                    $notification->reason === $reason &&
                    $notification->data === ($job->data) &&
                    in_array('mail', $channels);
            }
        );
    }
}
