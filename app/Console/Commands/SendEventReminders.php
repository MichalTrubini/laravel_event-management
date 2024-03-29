<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EmailReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends notifications to all event attendees that event is near';

    /**
     * Execute the console command.
     */
    public function handle()
    {   
        $events = Event::with('attendees.user')->whereBetween('start_date',[now(), now()->addDay()])->get();

        $eventCount = $events->count();
        $eventLabel = Str::plural('event', $eventCount);

        $this->info("Sending reminders for {$eventCount} {$eventLabel}...");

        $events->each(fn($event) => $event->attendees->each(
            fn($attendee) => $attendee->user->notify(new EmailReminderNotification($event))));

        $this->info('Reminders sent!');
    }
}
