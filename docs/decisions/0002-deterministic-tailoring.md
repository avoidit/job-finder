# 0002 — Deterministic tailoring, no AI calls

Status: accepted (2026-07-19). Supersedes the Claude-API tailoring portion of
[0001](0001-stack-and-scope.md); stack and scope decisions there still stand.

## Context

0001 planned resume/cover-letter tailoring via the Claude API. Heath wants the
code deterministic and AI calls avoided as much as possible: testable output,
no API key or cost, same input → same output.

## Decision

Tailoring is pure template assembly, no LLM calls:

1. **Master profile as structured data** — experience bullets tagged with
   skill keys (`laravel`, `mysql`, `api`, `automation`, `frontend`, …), plus
   metrics. Sourced from the rewritten resume + `summary`.
2. **Keyword extraction** from posting text reuses the scoring keyword config
   (one keyword source of truth).
3. **Resume tailor** — select and order bullets by tag overlap with the
   posting; render via Blade to markdown.
4. **Cover letter** — template with slots: company, role, top-3 matching
   achievements chosen by overlap. Heath hand-edits before sending.

## Consequences

- Fully testable: fixture posting in → exact expected markdown out.
- No API key, no cost, no nondeterminism.
- Cover letters read templated — acceptable; human polish pass is fast and
  was advisable even with LLM output.
- Master profile data entry (tagging bullets) is new manual work at M4 setup.
