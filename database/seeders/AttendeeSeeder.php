<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Event, User, Attendee};

class AttendeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $events = Event::all();

        foreach ($users as $user) {
            $eventsToAttend = $events->random(random_int(1, 3));

            foreach ($eventsToAttend as $event) {
                Attendee::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                ]);
            }
        }

    }
}
