New text needs to be translated in all languages available as json in lang/. Use the $t helper in Vue templates. Use
`trans` in scripts by importing it: `import { trans } from 'laravel-vue-i18n';`
Use the english translation as the key. There is no need to add the translation to the en_US.json as the key and value
will be the same.
When translating take the context of the app and the page in account. Never add new keys to lang/en_US.json

## Vue

- Always place the <template> above the <script> section
- Always use defineModel() instead of creating a prop modelValue and defining an emit
- To import other components always use the absolute path starting with `@` instead of a relative path.
- Use Shadcn components when necessary. The shadcn mcp server is available.
- When importing multiple components from the same file, write it all on one line.

## PHP

## Laravel

- Don't use `$fillable` on the models. We are unguarded by default.
- If a migration was added, run `php artisan migrate`.

### Jobs

- Always set a timeout. If the job calls an external endpoint, also set a timeout on the http client. This number should
  be smaller than the timeout on the job. Make sure the `timeout` in `config/horizon.php` is greater than the timeout on
  the job. Make sure the redis retry_after in `config/queue.php` is larger than the value in the horizon config.

## Misc

- When adding new env variables to config files, also add them to .env and .env.example with an empty value

## CSS

- Always use `shrink-0` instead of `flex-shrink-0`. They do the same.

## CLI

When running `php`, `npm` or `npx` first source the ~/.zshrc (`source ~/.zshrc`).

Run `npx tsc --noEmit` to make sure there are no ts errors.