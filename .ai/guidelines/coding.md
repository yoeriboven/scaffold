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

So don't do:

```vue
import {
Table,
TableBody,
TableCell,
TableHead,
TableHeader,
TableRow,
} from '@/components/ui/table';
```

but do

```vue
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
```

- Turn the data you pass from the controller to the page into a ts definition. This can be done inline for
  `defineProps`. No need for separate declarations.

## Inertia

- Always use the <Form> component instead of the form helper.

## PHP

## Laravel

- Don't use `$fillable` on the models. We are unguarded by default.
- If a migration was added, run `php artisan migrate`.

### Eloquent

- Use relationships on the model rather than doing `->where('model_id', $someId)`
- When a datetime column is added to the database it needs to be cast to `datetime`

### Spatie Data

- Don't use methods if a an attribute can also be used. Set defaults in the constructor definition (not the body if
  possible)

### Jobs

- Always set a timeout. If the job calls an external endpoint, also set a timeout on the http client. This number should
  be smaller than the timeout on the job. Make sure the `timeout` in `config/horizon.php` is greater than the timeout on
  the job. Make sure the redis retry_after in `config/queue.php` is larger than the value in the horizon config.

### Routing

- There is no need to explicitly specify the `public_id` column for route model binding. {customer:public_id} is
  redundant
  because we always want to use the `HasPublicId` trait on models that can be used in routes.

## Architecture

- If a domain gets big and has files spread across the app folder, move it to the Domain namespace. See
  app/Domains/Teams as an example.

## Misc

- When adding new env variables to config files, also add them to .env and .env.example with an empty value
- Never do `$team = currentTeam();`. Just use `currentTeam()` directly.

## CSS

- Always use `shrink-0` instead of `flex-shrink-0`. They do the same.

## CLI

Run `npx tsc --noEmit` to make sure there are no ts errors.

## Libraries

- Reka UI (powers Shadcn Vue): https://reka-ui.com/llms.txt
    - The Reka UI Checkbox uses v-model (which binds to modelValue), not :checked and @update:checked.

## Domain knowledge

- Most models need to be attached to the Team instead of the User

## Playwright testing

- When it asks to login use:
    - Email: yoeri@yoeri.me
    - Password: password