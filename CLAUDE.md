<laravel-boost-guidelines>
=== .ai/coding rules ===

New text needs to be translated in all languages available as json in lang/. Use the $t helper in Vue templates. Use `trans` in scripts by importing it: `import { trans } from 'laravel-vue-i18n';`
Use the english translation as the key. There is no need to add the translation to the en_US.json as the key and value will be the same.
When translating take the context of the app and the page in account. Never add new keys to lang/en_US.json
Always check if the key already exists to avoid duplicates.

## Vue

- Always place the <script> above the <template> section
- Always use defineModel() instead of creating a prop modelValue and defining an emit
- To import other components always use the absolute path starting with `@` instead of a relative path.

## PHP

## Laravel

- Don't use `$fillable` on the models. We are unguarded by default.
- If a migration was added, run `php artisan migrate`.
- To redirect on the php side, use actions instead of route name. E.g. `to_action` instead of `to_route`.

## Architecture

- On external requests where others can see the ids of models (i.e. urls, external services, etc) always use the `public_id` instead of the `id`. `id` is for internal use only.

### Controllers

- If not explicitly said otherwise, use invokable controllers.

### Validation

- Validate should use array for the rules. Not a pipe separated list.

### Localization

- Use `__()` in `.blade.php` files.
- Use `trans()` in normal `.php` files. Not `__()`.

### Vue Pages

- They should also be in the same subfolder. E.g. A controller is in app/Http/Controllers/Studio/IndexRedesignsController.php then the page must be in resources/js/Pages/Studio/IndexRedesigns.vue
- Always use invokable controllers from wayfinder.

### Migrations

- Don't add the `down` method.

### Facades

- Don't add the methods the facade can call to a docblock on the class.

### Jobs

- Always set a timeout. If the job calls an external endpoint, also set a timeout on the http client. This number should be smaller than the timeout on the job. Make sure the `timeout` in `config/horizon.php` is greater than the timeout on the job. Make sure the redis retry_after in `config/queue.php` is larger than the value in the horizon config.

### Routes

- Route model bindings should be declared in RouteServiceProvider.

## Billing

- If a user subscribes to a product, create a new subscription and redirect them to checkout. If a user already has a subscription, add the new product to the current subscription.

## Tests

- There is no need to write php tests
- There is no need to run php tests

## Misc

- When adding new env variables to config files, also add them to .env and .env.example with an empty value
- Dont run `php artisan pint`. It just takes time. 
- When a you add spatie media library to a model, make sure to add it to the morphmap in app/Providers/AppServiceProvider.php

## Playwright testing

- You can login with `yoeri@yoeri.me` and `password`

## CSS / Tailwind

- Always use `shrink-0` instead of `flex-shrink-0`. They do the same.

=== .ai/design-picker rules ===

# Design variant picker

A vanilla JS dev tool that lets us preview multiple design options side-by-side on the same page.

## When to use it

- The user asks for multiple variants/options of a UI element (e.g. "give me 5 designs for X", "show a few options for Y", "create variants").
- The user is in a design exploration phase and wants to compare options live in the browser before picking a winner.

Do NOT use it for:
- A single design — just build the thing.
- A/B tests in production — this is a local-only preview tool.

## How to use it

### Loading the script

Add the following script tag to the page where the variants will be previewed (e.g. in `resources/views/layouts/app.blade.php`, ideally guarded by `@env('local')` so it only loads in local development):

```html
<script src="https://gistcdn.githack.com/yoeriboven/60d47eb9f44ce13847aaa04873ba05d9/raw/8df832d71a1158d4507adb48928f0a92a84c5edb/variant-picker.js"></script>
```

### Rendering variants

For each variant, render the markup directly in the Vue/Blade template with a `data-variant="[number]-kebab-case-name"` attribute. Render all variants at once — no prop, no v-if, no JS registration. The picker scans the DOM, hides all but the selected variant, and renders a floating bar (top-right) with prev/next arrows that cycles through them.

```vue
<button data-variant="1-header-link" class="...">+ Add</button>
<button data-variant="2-header-button" class="...">+ Add showing</button>
<button data-variant="3-dashed-footer" class="...">+ Add showing</button>
```

### Naming

- Use descriptive kebab-case names (`dashed-footer`, `floating-fab`, `header-button`) — they show up verbatim in the picker bar.
- Use the same `data-variant` value on every element belonging to the same variant. If one variant needs multiple elements (e.g. an empty-state version plus an in-list version), give them all the same name.

### Behaviour to know

- The picker persists nothing — refresh resets to the first variant.
- All variants live in the DOM simultaneously, hidden via `style.display: none`. Don't rely on `v-show` or `v-if` to filter them.
- All variants should emit the same event / call the same handler — functionality stays identical, only the design changes.
- For variants that don't render in some states (e.g. a table row that's hidden when the list is empty), render an alternative element with the same `data-variant` name in the empty state.

## Cleanup

Once the user picks a winning variant:
1. Delete the losing markup.
2. Remove the `data-variant` attribute from the winner.

The picker auto-hides its bar when no `[data-variant]` elements are on the page, but the script tag itself should still be removed once exploration is done.

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v3
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v13
- laravel/horizon (HORIZON) - v5
- laravel/nightwatch (NIGHTWATCH) - v1
- laravel/prompts (PROMPTS) - v0
- laravel/wayfinder (WAYFINDER) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/vue3 (INERTIA_VUE) - v3
- tailwindcss (TAILWINDCSS) - v4
- vue (VUE) - v3
- @laravel/vite-plugin-wayfinder (WAYFINDER_VITE) - v0
- eslint (ESLINT) - v9
- prettier (PRETTIER) - v3

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`). Run `herd list` to discover all available commands.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

=== spatie/boost-spatie-guidelines rules ===

# Project Coding Guidelines

- This codebase follows Spatie's Laravel & PHP guidelines.
- Always activate the `spatie-laravel-php-standards` skill whenever writing, editing, reviewing, or formatting Laravel or PHP code.

</laravel-boost-guidelines>
