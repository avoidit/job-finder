<?php

// Master profile — my resume as structured data.
// Bullets tagged with keys from config/jobfinder.php 'keywords' (plus a few
// extras used only for overlap ranking). Tailoring reorders by tag overlap
// with the posting; nothing is invented, nothing is dropped.
return [

    'name' => 'Heath Landers',
    'title' => 'Full Stack Developer',
    'contact' => 'Madison, WI · landers.heath@gmail.com · (608) 207-6473',

    'summary' => 'Full stack developer with 10+ years building production web applications. '
        .'Laravel / PHP / MySQL / JavaScript. End-to-end ownership: schema design → business logic → frontend. '
        .'Tools I\'ve built are used by 180+ staff, saved an estimated 70,000+ labor hours annually, and scaled through 175% client growth.',

    'skills' => [
        'Backend' => 'PHP, Laravel, RESTful APIs, third-party integrations, batch processing',
        'Database' => 'MySQL, SQL Server — schema design, query optimization, stored procedures, migrations, indexing',
        'Frontend' => 'JavaScript, HTML, CSS, responsive UI',
        'Tooling' => 'Git, Python, Tableau, workflow automation',
    ],

    'jobs' => [
        [
            'company' => 'RailRCS',
            'role' => 'Full Stack Developer (Contract)',
            'location' => 'Madison, WI',
            'dates' => 'Aug 2022 – Present',
            'note' => null,
            'bullets' => [
                ['text' => 'Sole developer on a production Laravel platform for railroad dispatching operations — own architecture, feature development, database design, bug fixes, and deployments end-to-end.', 'tags' => ['laravel', 'php', 'backend', 'full stack', 'mysql']],
                ['text' => 'Built DOB (Daily Operating Bulletin) automation eliminating a 3-hour nightly manual process prone to human error — critical in a rail-safety environment. Scaled from 20 to 55 railroad clients with no added labor.', 'tags' => ['laravel', 'php', 'automation', 'backend']],
                ['text' => 'Built automated Trainsheet reporting replacing manual Excel tracking, freeing dispatchers for safety-critical work (track monitoring, compliance).', 'tags' => ['laravel', 'automation', 'reporting']],
                ['text' => 'Restructured database architecture and optimized queries, cutting response times from 8–10 seconds to 1–2 seconds.', 'tags' => ['mysql', 'database', 'backend']],
                ['text' => 'Maintained 99%+ uptime through 175% client growth.', 'tags' => ['backend', 'devops']],
            ],
        ],
        [
            'company' => 'Exact Sciences Laboratories',
            'role' => 'Systems Analyst / Internal Tools Developer',
            'location' => 'Madison, WI',
            'dates' => 'May 2015 – Aug 2022',
            'note' => 'Promoted from Data Entry; the Systems Analyst role was created around tools I built.',
            'bullets' => [
                ['text' => 'Built an Order Entry Suite (internal web app) adopted by 180+ staff — 90% error reduction, 20% faster processing, est. 70,000+ labor hours saved annually. Integrated BCBS insurance plan validation across 1,200+ daily orders.', 'tags' => ['full stack', 'frontend', 'javascript', 'sql server', 'database']],
                ['text' => 'Built an API-driven insurance plan lookup tool for the Revenue Cycle team — 30% faster queue processing across 1,000+ daily lookups.', 'tags' => ['api', 'rest', 'backend', 'integration']],
                ['text' => 'Automated shipping verification via NPI, address, and phone validation integrations — daily queue reduced from 1,000+ items to under 100.', 'tags' => ['automation', 'api', 'integration']],
                ['text' => 'Designed a custom SQL database and stored procedures for data validation, batch processing, and automated reporting.', 'tags' => ['database', 'sql server', 'backend']],
                ['text' => 'Built Tableau dashboards for department leadership: processing volume, error rates, queue metrics.', 'tags' => ['reporting', 'tableau']],
                ['text' => 'Served on the EPIC Systems migration team: data mapping, system integration, user training.', 'tags' => ['integration']],
            ],
        ],
    ],

    'education' => '**UW-Milwaukee** — Information Science & Technology (coursework completed)',

    // Cover letter pulls the top 3 of these by tag overlap with the posting.
    'achievements' => [
        ['text' => 'served as sole developer on a production Laravel platform for 55 railroad clients, maintaining 99%+ uptime', 'tags' => ['laravel', 'php', 'backend', 'full stack']],
        ['text' => 'eliminated a 3-hour nightly manual process with an automated Laravel system that scaled 20 → 55 clients', 'tags' => ['laravel', 'automation', 'php']],
        ['text' => 'cut database query response times from 8–10 seconds to 1–2 seconds by restructuring schema and indexes', 'tags' => ['mysql', 'database', 'backend']],
        ['text' => 'built an internal web app adopted by 180+ staff, cutting error rates 90% and saving 70,000+ labor hours a year', 'tags' => ['full stack', 'frontend', 'javascript']],
        ['text' => 'built an API-driven verification tool handling 1,000+ daily lookups, 30% faster queue processing', 'tags' => ['api', 'rest', 'integration']],
    ],
];
