# Contributing Guide

Thank you for contributing to the Ollama PHP library! This guide outlines quality expectations, coding style, and the contribution process.

## Quick Start
- Requirements: PHP 7.2+, ext-curl, ext-json
- Install dependencies: `composer install`
- Run all checks (style + static analysis + tests): `composer run check` or `make check`
- Auto-fix style: `composer run fix-style`
- Run tests: `composer run test`

## Commits — Iterative and Atomic
- Keep changes small and atomic: one commit = one logical change.
- Every commit must pass checks (`composer run check`).
- Prefer small, focused PRs.

## Mandatory Comments and Tests
- Changes to public interfaces and non-trivial logic must include concise PHPDoc/comments (explain the “why”, not the “how”).
- New features must include unit tests; bug fixes should include regression tests.
- Do not regress quality: run `phpunit`, `phpstan`, and linters before pushing.

## Commit Message Convention
The commit message must clearly convey the change and start with a type:

| Prefix   | Description |
|----------|-------------|
| FEAT     | add new functionality |
| FIX      | bug fixes |
| REFACTOR | refactoring without changing behavior |
| PERF     | performance improvements |
| TEST     | add or fix tests |
| CHORE    | technical changes not affecting code behavior (configs, deps) |
| DOCS     | documentation updates |
| STYLE    | code style (spaces, formatting) |
| BUILD    | build-related changes |
| CI       | CI/CD configuration |

Recommended format:

```
TYPE: imperative short summary

Details (why and what changed), notable nuances, and any BC impact.

Refs #<issue>, Related #<issue>
```

Examples:

```
FEAT: add temperature and top_p support in GenerationOptions

Introduce GenerationOptions DTO and serialize it into the request options.
Update the client to forward settings to /api/generate.
Refs #42
```

```
FIX: handle cURL timeouts and propagate error codes correctly

Add normalized TransportException message and timeout tests.
```

## Code Style and Checks
- Style: PSR-compatible (enforced by `php-cs-fixer` and `phpcs`).
- Static analysis: `phpstan analyse` (see config for level).
- Unified command: `composer run check` or `make check`.

## Branches and PRs
- Name branches by purpose: `feature/<short>`, `fix/<short>`, `chore/<short>`.
- PR description must explain what and why changed, how to test, and link issues.
- PRs with failing checks will not be merged.

## Regressions and Backward Compatibility
- Avoid breaking changes to public interfaces. If unavoidable, clearly document and flag in release notes.

## Security
- Never commit secrets/keys/passwords. Use environment variables and local configs outside the repo.

## How to Propose a Change
1. Fork/branch from `main`.
2. Make small, atomic commits with meaningful messages.
3. Locally ensure `composer run check` is green.
4. Open a PR describing motivation and changes.

Thank you for your contribution!
