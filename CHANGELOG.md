
# Changelog

All notable changes to this project will be documented in this file.

---

## [2.0.0] - 2026-03-27

### Major Release: Laravel Domain Toolkit

This release transforms the package from a simple repository generator into a full domain-oriented architecture toolkit.

---

## Added

- `make:service` command for generating service layer classes
- `make:dto` command for generating Data Transfer Objects
- `make:domain` command to scaffold a full domain (Model, Controller, Repository, Service)
- Support for DTO folder structuring (e.g. `App/DTO/Auth/...`)
- Publishable stub system for customization of generated code
- `ServiceReturn` helper for standardized service responses
- Smart resolution for `ServiceReturn` (uses app implementation if available)
- Support for Laravel 12 and 13 (experimental)

---

## Changed

- Package renamed from `laravel-repository` to `laravel-domain-toolkit`
- Shifted from repository-only pattern to layered architecture (Repository, Service, DTO)
- Commands are now:
  - Registered via the package
  - Available immediately after installation
- Publishing behavior updated:
  - Only publishes base repository, interface, provider, and stubs
- Repository generation now automatically registers bindings in `RepositoryServiceProvider`
- Improved stub structure for better extensibility and customization

---

## Removed

- Publishing of Artisan commands to the application
- Manual command registration requirement
- `--s` and `--s-only` flags from `make:repository` (replaced by `make:service`)

---

## Breaking Changes

- Package name has changed:
  - `blaisebueno/laravel-repository` → `reizucodes/laravel-domain-toolkit`
- Commands are no longer published to `app/Console/Commands`
- You must run:
  ```sh
  php artisan vendor:publish --tag=laravel-domain-toolkit
  ```
  before using generators
- Architecture has expanded beyond repositories to include services and DTOs

---

## Migration Guide

1. Remove old package:
   ```sh
   composer remove blaisebueno/laravel-repository
   ```

2. Install new package:
   ```sh
   composer require reizucodes/laravel-domain-toolkit
   ```

3. Publish toolkit resources:
   ```sh
   php artisan vendor:publish --tag=laravel-domain-toolkit
   ```

4. Update existing repositories and services as needed to align with the new structure

---

## Notes

This version introduces a more scalable and maintainable architecture approach while remaining aligned with Laravel conventions.

---

## [1.0.0] - Initial Release

- Repository pattern scaffolding
- `make:repository` command
- Base repository and interface generation
- Manual command publishing and registration