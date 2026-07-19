# Spike: job source viability (M1)

Date: 2026-07-19. Question: which job boards can we ingest deterministically
(feed/API, no scraping)? Answer: **all four candidates work.** Ingest order of
value: Larajobs > HN Who's Hiring > WWR > RemoteOK.

## Findings

### 1. Larajobs — RSS ✅ (best fit)

```sh
curl -s 'https://larajobs.com/feed'
```

- Laravel-specific board — every posting is on-stack.
- Custom `job:` namespace: `job:location`, `job:job_type`, `job:salary`,
  `job:company`. Clean structured fields, hourly update frequency.

### 2. HN Who's Hiring — Algolia API ✅

```sh
# find current month's thread
curl -s 'https://hn.algolia.com/api/v1/search_by_date?tags=story,author_whoishiring&hitsPerPage=3'
# fetch comments for a thread (July 2026 = story_48747976)
curl -s 'https://hn.algolia.com/api/v1/search_by_date?tags=comment,story_48747976&hitsPerPage=1000&page=0'
```

- Thread found by `tags=story,author_whoishiring`; postings are top-level
  comments; convention `Company | REMOTE/ONSITE | type | location`.
- **Gotcha:** Algolia typo-tolerance matched "largely" for query `laravel`.
  Do NOT rely on Algolia `query=` for filtering — fetch all comments
  (paginate `hitsPerPage=1000`) and keyword-filter ourselves. Deterministic
  and matches our keyword-config design.
- New thread first weekday of each month; ingest must re-resolve thread ID.

### 3. WeWorkRemotely — RSS ✅

```sh
curl -s -A 'Mozilla/5.0' 'https://weworkremotely.com/categories/remote-back-end-programming-jobs.rss'
# also: remote-full-stack-programming-jobs.rss
```

- Structured fields: `region`, `skills`, `category`, `type`. `ttl` 60 min.
- Needs browser User-Agent header.

### 4. RemoteOK — JSON API ✅

```sh
curl -s -A 'Mozilla/5.0' 'https://remoteok.com/api'
```

- First array element is a legal/TOS notice object — must skip it.
- **TOS: requires visible link-back attribution to RemoteOK.** Fine for
  personal tool; honor it if UI ever shows these postings publicly.
- Has `tags` array (e.g. `php`, `laravel`) — usable for scoring directly.

## Not viable

- **LinkedIn / Indeed:** no public API, active anti-scraping. Handle via
  manual paste-in form (already in PLAN.md).

## Conclusion for M2

Build 4 ingest commands (`ingest:larajobs`, `ingest:hn`, `ingest:wwr`,
`ingest:remoteok`) + manual paste-in. All deterministic fetch + parse, no
scraping. Dedupe on company+title (same job cross-posts across boards).
