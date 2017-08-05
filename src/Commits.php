<?php

namespace Tonning\Commits;

use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class Commits
{
    public function getCommits($branch = null, $from = null, $to = 'HEAD')
    {
        $branch = $branch ?: self::getCurrentBranch();
        $from = $from ?: self::getLatestCommitHash($branch);

        $rawLogOutput = $this->getCommandOutput([
            'git',
            'log',
            $branch,
            "{$from}..{$to}"
        ]);

        $lines = collect(explode("\n", $rawLogOutput));

        $commits = collect();

        foreach ($lines as $lineNumber => $line) {
            if (Str::startsWith($line, 'commit')) {
                $commit = new Commit();
                $commit->setHash($line);
                $commits->push($commit);
            } elseif (Str::startsWith($line, 'Author: ')) {
                $commit->setAuthor($line);
            } elseif (Str::startsWith($line, 'Date: ')) {
                $commit->setDate($line);
            } elseif (Str::startsWith($line, 'Merge')) {
                $commit->setMerge($line);
            } elseif (! empty($line)) {
                $commit->setMessageLine($line);
            }
        }

        return $commits;
    }

    protected function getCommandOutput(array $arguments)
    {
        $process = $this->getCommand($arguments);

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    protected function getCommand(array $arguments)
    {
        return $this->buildCommand($arguments)->getProcess()->setWorkingDirectory(app_path());
    }

    protected function buildCommand(array $arguments)
    {
        return new ProcessBuilder($arguments);
    }

    public static function getLatestCommitHash($branch = null)
    {
        return trim((new static)->getCommandOutput(['git', 'rev-parse', $branch]));
    }

    public static function getBranches()
    {
        return collect(explode("\n", (new static)->getCommandOutput(['git', 'branch', '-a'])))
            ->map(function ($branch) {
                return trim($branch);
            })->reject(function ($branch) {
                return ! $branch;
            })->toArray();
    }

    public static function getCurrentBranch()
    {
        return trim((new static)->getCommandOutput(['git', 'rev-parse', '--abbrev-ref', 'HEAD']));
    }

    public static function getCurrentBranchIndex($branches)
    {
        return collect($branches)->search(function ($branch) {
            return starts_with($branch, '*');
        });
    }
}
