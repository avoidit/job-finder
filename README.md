# job-finder

Laravel app that finds developer job postings, ranks them against my stack,
generates a tailored resume + cover letter per posting, and tracks the
application pipeline. Built to run my own job search — and to be read as a
work sample (Laravel 13, PHP 8.4, SQLite).

Everything is deterministic: no LLM calls, no scraping. Same input → same
output, verified by tests.

## How it works

```
[Sources] → ingest → postings → score → ranked queue
                                            │
   master profile (tagged bullets) → tailor (template assembly)
                                            │
                              resume.md + cover.md per posting
                                            │
                                  application pipeline (dashboard)
```

- **Ingest** — one artisan command per source, feeds/APIs only:
  [Larajobs](https://larajobs.com) (RSS), HN Who's Hiring (Algolia API),
  [We Work Remotely](https://weworkremotely.com) (RSS),
  [Remote OK](https://remoteok.com) (JSON API). Deduped on `(source, external_id)`.
- **Score** — keyword weights from `config/jobfinder.php`. Title matches count
  double (descriptions are keyword-stuffed), off-stack titles and
  non-workable regions are penalized. Pure config, no magic.
- **Tailor** — `config/profile.php` holds resume bullets tagged by skill.
  Bullets are reordered by tag overlap with the posting's keywords and
  rendered through Blade to markdown. Nothing invented, nothing dropped.
- **Track** — dashboard with ranked queue, one-click status moves, and an
  applied-per-week counter.

## Usage

```sh
composer install
php artisan migrate

php artisan ingest:larajobs && php artisan ingest:hn && \
php artisan ingest:wwr && php artisan ingest:remoteok

php artisan score:run --top=15        # ranked table in the terminal
php artisan tailor:generate {id}      # writes storage/app/applications/{id}-{company}/
php artisan serve                     # dashboard at localhost:8000
```

## Automation

The whole pipeline runs itself daily at 07:00 via the Laravel scheduler
(`routes/console.php`): ingest all sources → re-score → auto-generate
tailored documents for every unapplied posting scoring ≥ 60
(`jobfinder.auto_tailor_min`). One crontab entry drives it:

```cron
* * * * * /usr/bin/php /path/to/app/artisan schedule:run >> storage/logs/cron.log 2>&1
```

Daily routine is then just: open the dashboard, review new high scores,
hand-edit the generated cover letter, apply, click the status button.
Note: cron only fires while the machine is on — a missed 07:00 run simply
happens the next day.

## Tests

```sh
php artisan test
```

Covers keyword extraction, bullet ranking (stable, deterministic), rendered
output identity, queue/pipeline behavior, and status transitions.

## Design notes

- Decision records live in [`docs/decisions/`](docs/decisions/), the
  source-viability spike in [`docs/spikes/`](docs/spikes/).
- Auto-apply is deliberately out of scope: job-board TOS, and low-quality
  signal to employers.
