<?php

return [
    'notifiables' => [
        \App\Admin::class
    ],

    'notification' => \Tonning\Commits\Notifications\Deployed::class,

    'via' => ['slack'],

    'from' => 'Deployer',

    'subject' => 'Commits since last deployment.',

    'slack-channel' => '#sysops',

    'slack-icon' => ':satellite_antenna:',

    'repository_url' => 'http://github.com/_your_/_repo_/commit',

    'filename' => 'commits.json',
];
