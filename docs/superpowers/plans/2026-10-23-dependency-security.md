# Dependency security implementation plan

**Goal:** Resolve all 53 Composer advisories, repair the three failing tests, and ship verified Docker configuration.

**Architecture:** Keep the existing Laravel application structure and persistent database schema. Upgrade to patched Laravel 12, Jetstream 5, Sanctum 4, Livewire 3, and a compatible PHPUnit release. Preserve the patched PHP 8.3.36 runtime.

**Constraints:** No suppressed advisories, ignored platform requirements, destructive migrations, or committed secrets. Work in the existing user-requested checkout and push through `albertassidiq`.

## Tasks

- [x] Reproduce the password-confirmation failures. Add regression coverage for new-user defaults and rejection of inactive/disabled users; make model defaults agree with database defaults. Correct the home-route test to assert the intended login redirect.
- [x] Upgrade Composer dependencies to secure compatible releases. Migrate Livewire events, model bindings, JavaScript hooks, component configuration, and Alpine integration. Test authenticated application pages and Livewire actions, not only the login page.
- [x] Update Dockerfile and add Compose with a persistent database and upload volume, required environment configuration, health checks, and explicit migration instructions. Extend CI to audit dependencies and run the complete test suite and container smoke test.
- [x] Verify Composer audit and validation, PHPUnit, frontend build, Docker/Compose CI, and changes against the original application behavior. Update deployment documentation, commit, and push.

## Review focus

- Fresh user objects must retain active status, while disabled users must still be rejected.
- Password confirmation must record the confirmation timestamp and reject incorrect passwords.
- Livewire must hydrate existing components, preserve live previews, and enforce ownership on later requests.
- Uploaded files and database data must survive container replacement.
- CI and Docker must use the real patched PHP runtime and fail on known vulnerable dependencies.
