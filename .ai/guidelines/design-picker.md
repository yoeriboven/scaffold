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