# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-01-13

### Changed

- **Dependency Management:** Migrated from Dependabot to Renovate for automated dependency updates
- **Frontend:** Updated @typescript-eslint packages to 8.53.0, aligning parser and plugin versions
- **Frontend:** Bumped Vite to 7.3.1, Vitest to 4.0.17, TypeScript to 5.9.3
- **Frontend:** Updated Vue ecosystem (vue, vuetify, vue-router, vue-tsc)
- **Backend:** Updated Symfony packages to 8.0.3, Doctrine ORM to 3.6.1, phpunit to 12.5.4
- **Docker:** Upgraded base images (Node 25.2.1, PHP 8.5-fpm, nginx 1.29.4, PostgreSQL 18)
- **CI/CD:** Updated GitHub Actions and security scanning tools

### Added

- Renovate configuration with autodiscovery for npm, Composer, Docker, Docker Compose, and GitHub Actions
- Docker Compose file versioning (bumped to 5.0.0 compatibility)
- Enhanced GitHub Actions permissions and CodeQL security scanning

### Fixed

- Resolved TypeScript ESLint peer dependency conflict
- Fixed Dependabot-related workflow issues before Renovate migration

## [1.0.0] - 2025-12-08

### Changed

- Upgraded CodeQL workflow version from v3 to v4[attached_file:1].
- Vue frontend: Multi-stage Node → nginx (~25MB production image).

[1.0.0]: https://github.com/Esysc/wep-app-fullstack/releases/tag/v1.0.0

## [1.0.0-rc.1] - 2025-12-04

### Added

- Full-stack train routing application with PHP 8.4 backend and Vue 3 frontend.
- RESTful API with OpenAPI specification for route calculation and analytics.
- Dijkstra's algorithm implementation for shortest path calculation between stations.
- JWT-based authentication system.
- PostgreSQL database with Doctrine ORM.
- Vue 3 + Vuetify 3 + TypeScript frontend.
- Docker Compose orchestration for local development.
- Nginx reverse proxy with HTTPS support.
- Comprehensive test suite (PHPUnit for backend, Vitest + Playwright for frontend).
- CI/CD pipeline with GitHub Actions.
- Security scans (PHPStan SAST, npm audit).
- E2E tests with Playwright across multiple browsers.
- Code coverage reporting with Codecov integration.
- Automated release workflow with Docker image publishing.
- Pre-commit hooks for code quality.
- Complete documentation (README, DEPLOYMENT, RELEASE guides).

### Security

- JWT keys generated automatically.
- Self-signed certificates for local HTTPS development.
- PHPStan static analysis at level 5.
- npm audit for dependency vulnerability scanning.
- Secure password hashing with Symfony security component.

[1.0.0-rc.1]: https://github.com/Esysc/wep-app-fullstack/releases/tag/v1.0.0-rc.1

## [Unreleased]

[Unreleased]: https://github.com/Esysc/wep-app-fullstack/compare/v1.0.0...HEAD[web:3].
