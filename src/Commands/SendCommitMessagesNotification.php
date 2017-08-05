<?php

namespace Tonning\Commits\Commands;

use File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Tonning\Commits\Commits;

class SendCommitMessagesNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commits:notify
                            {--B|branch=HEAD} 
                            {--F|from=} 
                            {--T|to=}
                            {--J|json=}
                            {--non-interactive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification of commit messages.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('json')) {
            return $this->notify(json_decode(File::get(base_path($this->option('json')))));
        }

        if ($this->option('non-interactive')) {
            $branch = $this->option('branch');
            $from = $this->option('from') ?: Commits::getLatestCommitHash($branch);
            $to = $this->option('to') ?: 'HEAD';
        } else {
            $branch = $this->choice('Which branch?', $branches = Commits::getBranches(), Commits::getCurrentBranchIndex($branches));
            $from = $this->ask("Commit hash to start from? Latest commit pushed to <comment>{$branch}</comment> is:", Commits::getLatestCommitHash($branch));
            $to = $this->ask("Commit hash to end with?", 'HEAD');
        }

        return $this->notify((new Commits)->getCommits($branch, $from, $to));
    }

    private function notify($commits)
    {
        $notifiables = array_wrap(config('commits.notifiables'));

        $notification = config('commits.notification');

        foreach ($notifiables as $notifiable) {
            (new $notifiable)->notify(new $notification($commits));
        }

        $this->info('Notification sent!');
    }
}
