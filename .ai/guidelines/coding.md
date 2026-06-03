New text needs to be translated in all languages available as json in lang/. Use the $t helper in Vue templates. Use `trans` in scripts by importing it: `import { trans } from 'laravel-vue-i18n';`
Use the english translation as the key. There is no need to add the translation to the en_US.json as the key and value will be the same.
When translating take the context of the app and the page in account. Never add new keys to lang/en_US.json
Always check if the key already exists to avoid duplicates.

## Vue
- Always place the <template> above the <script> section
- Always use defineModel() instead of creating a prop modelValue and defining an emit
- To import other components always use the absolute path starting with `@` instead of a relative path.

## PHP
- New controllers need to extend App\Http\Controller and use the `handle` method and `authorize` if necessary. Never use `__invoke()`.

## Laravel
- Don't use `$fillable` on the models. We are unguarded by default.
- If a migration was added, run `php artisan migrate`.
- To redirect on the php side, use actions instead of route name. E.g. `to_action` instead of `to_route`.

## Architecture
- On external requests where others can see the ids of models (i.e. urls, external services, etc) always use the `public_id` instead of the `id`. `id` is for internal use only.

### Controllers
- If not explicitly said otherwise, use invokable controllers.

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