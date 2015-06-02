<?php namespace App\Commands;

use App\Commands\Command;

use Httpful\Request;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Request the urls
 */
class RequestProjectCheckUrl extends Command implements SelfHandling, ShouldBeQueued
{
    use InteractsWithQueue, SerializesModels;

    private $link;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($link)
    {
        $this->link = $link;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $reponse = Request::get($this->link->url)->send();

        $this->link->last_status = $reponse->hasErrors();
        $this->link->save();

        if ($reponse->hasErrors()) {
            foreach ($this->link->project->notifications as $notification) {
                Queue::pushOn('notify', new Notify($notification, $this->link->notificationPayload()));
            }
        }
    }
}
