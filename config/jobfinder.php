<?php

// Single source of truth for stack keywords. Used by:
// - scoring (M3): weight per keyword
// - HN ingest filter: strong keywords only (thread has 1000s of comments)
// - tailoring (M4): bullet selection by tag overlap
return [

    // keyword => score weight (matched case-insensitively against title + description + tags)
    'keywords' => [
        'laravel' => 30,
        'php' => 20,
        'full stack' => 12,
        'full-stack' => 12,
        'fullstack' => 12,
        'mysql' => 10,
        'backend' => 8,
        'back-end' => 8,
        'javascript' => 5,
        'sql server' => 5,
        'vue' => 4,
        'rest' => 3,
    ],

    // postings mentioning remote work get this bonus
    'remote_bonus' => 15,

    // location => bonus (Madison WI area preferred)
    'location_bonus' => [
        'madison' => 20,
        'wisconsin' => 10,
        'milwaukee' => 5,
    ],

    // tailor:auto generates documents for unapplied postings scoring at least this.
    'auto_tailor_min' => 60,

    // Spelling variants → canonical tag used in config/profile.php bullet tags.
    'aliases' => [
        'full-stack' => 'full stack',
        'fullstack' => 'full stack',
        'back-end' => 'backend',
    ],

    // Regions Heath can't work from (title or location) — near-disqualifying.
    'region_penalty' => ['europe' => -50, 'emea' => -50, 'apac' => -50, 'latam' => -50, 'uk only' => -50, 'eu only' => -50, 'hybrid' => -25],

    // If the title names an off-stack tech and no PHP/Laravel, the job is
    // primarily that stack — flat penalty (word-boundary matched).
    'off_stack_title' => ['react', 'python', 'java', 'ruby', 'rails', 'django', 'golang', 'node', 'dotnet', '\.net', 'ios', 'android'],
    'off_stack_penalty' => -30,

    // HN Who's Hiring: only store comments containing at least one of these
    // (strong stack signals only — 'rest'/'javascript' would match everything)
    'hn_filter' => ['php', 'laravel', 'full stack', 'full-stack', 'fullstack', 'mysql'],
];
