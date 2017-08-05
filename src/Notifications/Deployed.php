<?php

namespace Tonning\Commits\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class Deployed extends Notification
{
    use Queueable;

    /**
     * @var
     */
    private $commits;

    /**
     * Create a new notification instance.
     *
     * @param $commits
     */
    public function __construct($commits)
    {
        $this->commits = $commits;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return config('commits.via');
    }

    public function toSlack()
    {
        $message = (new SlackMessage())
            ->to(config('commits.slack-channel'))
            ->from(config('commits.from'), config('commits.slack-icon'))
            ->content(config('commits.subject'));

        foreach ($this->commits as $commit) {
            $message->attachment(function ($attachment) use ($commit) {
                $attachment->title($commit->commitMessage(), $commit->url())
                    ->content($commit->details() ?: null)
                    ->timestamp(Carbon::parse($commit->date()))
                    ->footer($commit->author());
            });
        }

        return $message;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage())
            ->success()
            ->subject(config('commits.subject'))
            ->from($notifiable->routeNotificationForMail(), config('commits.from'))
            ->line(config('commits.subject'));

        foreach ($this->commits as $commit) {
            $mailMessage->line("- {$commit->commitMessage()}");
        }

        $mailMessage->action('View repository', url(config('commits.repository_url')));

        return $mailMessage;
    }
}
