# AI Agent Instructions for Nyumban API

## Purpose
This file guides AI coding agents on the repository structure, common conventions, and how to handle UI style work such as title bar styling in this Laravel + Filament project.

## Project overview
- Laravel 13 backend project targeting PHP 8.3.
- Uses `filament/filament` v5 for the admin panel.
- Uses Tailwind CSS v4 + Vite for frontend asset compilation.
- Main app code lives in `app/` and `routes/`.
- Filament admin resources live under `app/Filament/Resources`, `app/Filament/Widgets`, and `app/Filament/Pages`.

## Build and test commands
- Install dependencies: `composer install` and `npm install`
- Compile assets: `npm run build`
- Local frontend dev: `npm run dev`
- Run PHP tests: `composer test` or `php artisan test`
- App setup: `composer setup`

## Key file locations
- `app/Providers/Filament/AdminPanelProvider.php` — Filament admin panel configuration, colors, pages, widgets, auth middleware.
- `resources/css/app.css` — Tailwind entrypoint and custom CSS for the app.
- `resources/views/welcome.blade.php` — default blade landing page.
- `routes/api.php` — API endpoints.
- `app/Http/Controllers/` — standard controller logic.

## Filament / UI style guidance
- For frontend styling tasks, prefer modifying `resources/css/app.css` and Filament resource/pages rather than vendor assets.
- For admin title bar / header style work, first inspect Filament page layout and header configuration in `app/Providers/Filament/AdminPanelProvider.php` and `app/Filament/Resources`.
- Tailwind is the source of truth for CSS utilities, so use Tailwind classes and `resources/css/app.css` for custom styles.
- Avoid changing `public/` or `vendor/` files directly unless the change is clearly a build artifact fix.

## What to do when asked about title bar style
- Treat "title bar style" as a UI layout/style change.
- Locate the relevant Filament resource or Blade view first.
- Confirm whether the page is a Filament admin page (`app/Filament/*`) or a regular view (`resources/views/*`).
- Prefer using Filament configuration, page components, or Tailwind classes over hardcoded markup changes.

## Agent behavior
- Keep changes minimal and aligned with Laravel conventions.
- Do not assume a separate SPA framework; frontend assets are built with Vite and Tailwind.
- Do not change unrelated backend code when making style or UI adjustments.
- When in doubt, ask for clarification on whether the requested change belongs to the admin UI or the public-facing views.
