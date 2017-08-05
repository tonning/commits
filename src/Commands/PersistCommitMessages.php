<?php

namespace Tonning\Commits\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;
use Tonning\Commits\Commit;
use Tonning\Commits\Commits;

class PersistCommitMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commits:persist 
                            {--B|branch=HEAD} 
                            {--F|from=} 
                            {--T|to=HEAD}
                            {--non-interactive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Persist git commit messages to json file.';

    /**
     * Create a new command instance.
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
        if ($this->option('non-interactive')) {
            $branch = $this->option('branch');
            $from = $this->option('from') ?: Commits::getLatestCommitHash($branch);
            $to = $this->option('to') ?: 'HEAD';
        } else {
            $branch = $this->choice('Which branch?', $branches = Commits::getBranches(), Commits::getCurrentBranchIndex($branches));
            $from = $this->ask("Commit hash to start from? Latest commit pushed to <comment>{$branch}</comment> is:", Commits::getLatestCommitHash($branch));
            $to = $this->ask("Commit hash to end with?", 'HEAD');
        }

        $commits = (new Commits)->getCommits($branch, $from, $to);

        File::put(base_path(config('commits.filename')), json_encode($commits));

        $commitsForTable = $commits->map(function ($commit) {
            return [
                $commit->commitMessage(),
                $commit->author(),
                $commit->date(),
                Str::limit($commit->hash(), 8, '')
            ];
        });

        $this->table(['Message', 'Author', 'Date', 'Hash'], $commitsForTable);

        $this->info("{$commits->count()} commits persisted.");
    }
}
