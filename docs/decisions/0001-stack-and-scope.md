# 0001 — Stack and MVP scope

Status: accepted (2026-07-19)

## Context

Goal is a developer job, fast. Software can help, but building it competes
with time spent applying. Heath's target roles are Laravel/PHP full-stack or
backend, remote or Madison WI. Three scope options were considered: full
pipeline, tailor+track only, ingest+score only. Tool stack options: Laravel,
Python scripts, bare CLI.

## Decision

1. **Full pipeline MVP**: ingest → score → tailor (Claude API) → track.
   Auto-apply excluded (board TOS risk, low signal).
2. **Laravel/PHP/MySQL** for the tool itself — same stack as target jobs, so
   the repo doubles as a portfolio piece.
3. **Resume rewrite comes first** (manual, before any code) so applying can
   start immediately; the rewritten master profile also seeds the tailoring
   engine.

## Consequences

- Repo is public-facing quality: README and tests matter (milestone 6).
- Laravel adds scaffold overhead vs scripts; accepted for portfolio value.
- Every day of building is a day not applying — milestones stay lean, and
  Track A (manual applying) runs in parallel from day one.
