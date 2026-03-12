# Repository Guidelines

## Project Structure & Module Organization

This repository currently stores planning and architecture material for the `aukcije.ba` platform rather than a full application scaffold. Start with [README.md](/Users/zeljkodzafic/Documents/aukcije.ba/README.md) for the product overview and intended stack. Core documentation lives in [docs/arhitektura](/Users/zeljkodzafic/Documents/aukcije.ba/docs/arhitektura), organized by topic and numbered for reading order (`01-system-overview.md` through `20-payment-integration.md`). Task tracking is centralized in [docs/TASKS.md](/Users/zeljkodzafic/Documents/aukcije.ba/docs/TASKS.md). Keep new design or implementation notes in `docs/` and follow the existing numbered, kebab-case naming pattern.

## Build, Test, and Development Commands

There is no runnable app in this snapshot, so no repository-local build scripts exist yet. The target application workflow is documented in [README.md](/Users/zeljkodzafic/Documents/aukcije.ba/README.md):

- `docker compose up -d` starts the planned local services.
- `composer install` and `npm install` install PHP and frontend dependencies once the app scaffold exists.
- `php artisan serve` and `npm run dev` run the Laravel and Vite dev servers.
- `php artisan test --parallel`, `php artisan dusk`, and `npx vitest run` are the intended test entry points.

Only document commands here that are verified in the repository; if you add executable code, update this section in the same change.

## Coding Style & Naming Conventions

Documentation should be concise, task-oriented, and written in consistent Markdown. Use `#` headings in a clear hierarchy, keep paragraphs short, and prefer kebab-case filenames such as `17-testing-strategy.md`. Preserve the numeric prefixes for ordered architecture docs. When describing code paths, mirror the Laravel structure defined in the README (`app/Services`, `app/Http/Controllers`, `tests/Feature`).

## Testing Guidelines

For documentation-only changes, verify links, filenames, and cross-references before submitting. For future implementation work, follow the quality bar documented in the README: every feature or bug fix requires a test, with emphasis on unit and feature coverage around bidding, escrow, and wallet flows.

## Commit & Pull Request Guidelines

Git history is minimal (`Initial commit`), so use imperative, scoped commit messages going forward, for example `docs: refine payment integration notes` or `feat: add bidding service skeleton`. Pull requests should include a short summary, affected documents or modules, linked task IDs when available (for example `T-301`), and screenshots only when UI assets are introduced.

## Security & Configuration Tips

Do not commit secrets, real credentials, or environment dumps. Keep payment, auth, and infrastructure assumptions aligned with the security guidance in `docs/arhitektura/13-security-architecture.md` and the deployment notes in `docs/arhitektura/08-deployment.md`.
