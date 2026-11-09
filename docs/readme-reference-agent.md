# Reference post guide for agents

This guide is for people and agents creating source-code reference posts for a plugin. It is intended to be reusable across projects. Keep it outside the Writerside topics directory and do not add it to the documentation tree. Read the project's existing agent guide as well, and follow the maintainer's instructions when they differ from this guide.

## Purpose and files

Create one HTML file per documented item in the plugin's `docs/reference` directory, unless the project specifies another location. These files contain WordPress block-editor markup for posts that use the GD Knowledge Base Source Code block. They are separate from Writerside Markdown topics; do not add them to the Writerside tree or assume Markdown import metadata applies to them.

Name the file with the item type first, followed by its name, using lowercase, URL-friendly words separated by hyphens. For example, a shortcode named `example_current` would use `shortcode-example-current.html`. Preserve the actual identifier, including underscores, in the post itself. Use distinct, clear names for methods if the same method name occurs in more than one class.

Put the intended WordPress post title in an HTML comment on the first line, followed by a blank line. Use only the actual identifier as the title; do not append labels such as `filter`, `action`, `shortcode`, `function`, or `method`. Preserve any necessary class qualification and dynamic name components:

```html
<!-- post-title: example_current -->
```

This comment is file metadata, not a WordPress title field and not part of the pasteable post body. Tell the maintainer to enter the title separately, then paste everything after the comment into the block editor's **Code editor**. Do not assume pasting the comment creates the post title. Avoid adding an H1 to the body just to hold the title.

## Research the item

- Inspect the target plugin's current source, existing documentation, main plugin header, version and edition conditions. Verify the implementation rather than treating existing documentation or PHPDoc as conclusive. Investigate discrepancies and report any that remain unresolved.
- For functions and methods, check the signature, visibility, types, defaults, return values, exceptions and actual behavior. For hooks, inspect where they are called, the filtered value or action arguments, their order, and the conditions under which the hook runs. For shortcodes, check registration, attribute defaults and conversion, lookup and access checks, output, and any wrapper or empty-result behavior.
- Follow behavior into helpers, settings and callers where needed. Distinguish fixed defaults from values taken from settings or request context. State edition or integration requirements only when verified. Do not assume a name or an implementation is a supported public API simply because it exists.
- Choose examples that reflect the real API and explain any required context. Never invent parameter values, a hook's return behavior, source locations or version claims. If the intended set of references is unclear, inspect the available API and report any scope ambiguity.

## Build the post body

The first pasteable block must be one `gdkob/source` block for the documented item. Follow it with normal WordPress blocks, such as paragraphs, headings, lists and code blocks, describing how the item works and showing useful examples. Avoid another top-level Source Code block in the same post. Nested `gdkob/argument` blocks represent individual arguments or shortcode attributes.

Before constructing markup, inspect the installed GD Knowledge Base plugin's current `src/blocks/source` and `src/blocks/argument` definitions, save functions and source templates, as well as any editor-saved example supplied by the maintainer. Its Reference Generator can also help explain expected structure, but verify generated content against the block's current save implementation and the documented plugin's actual behavior. Block markup and defaults may change between versions.

The current block distinguishes `function`, `method`, `static`, `shortcode`, `action`, `action_ref`, `filter` and `filter_ref` types. Select the actual type and use its prescribed prefix, suffix, wrapper and separator from the block's templates. Do not substitute a display format from another type. Explain qualifiers or signatures that the block cannot represent accurately in the surrounding text.

The serialized block contains both HTML and `<!-- wp:... -->` delimiters. Settings without an HTML source belong in the JSON attributes on the opening block comment. Text attributes with an HTML source must appear in the elements and selectors defined by `block.json`. The saved HTML must match the selected attributes and the block's save function: include the correct wrapper classes, elements, and nested block comments. For example, when a column is hidden, keep the parent and each child block's corresponding `hide_type` or `hide_default` setting and saved elements consistent. Do not rely on a block parser alone to detect invalid saved markup.

Use the argument rows to identify names, types, descriptions and defaults where meaningful. For shortcodes, label the section **Attributes** and the return section **Output** when appropriate. Omit sections that do not apply, with the matching block settings; do not imply that an action returns a filtered value or that a shortcode returns a PHP API value. If a definition shows example attribute values instead of defaults, make that clear in the prose. Do not include a placeholder source line; show a verified location or disable it.

When the documented item is available only in a plugin's Pro edition, enable the Source Code block ribbon, set its text to `Pro` and its background to red (for example, `#d63638`). State the edition requirement in the post. Check the actual edition gate before applying the ribbon; an optional integration alone does not make a hook Pro-only.

Escape HTML and JSON correctly for their respective contexts. Preserve code syntax: use straight quotes, and use `&quot;` in the saved definition text where needed to prevent WordPress typography from converting shortcode attribute quotes into curly quotes. Verify the result after an editor save. In code blocks showing a registered shortcode, use WordPress's double-bracket escaping, for example `[[example_current]]`, so the published example displays the shortcode instead of executing it; confirm the rendered output. Do not double brackets in the Source Code block's definition, whose markup supplies the brackets.

## Verify and hand off

1. Check every identifier, attribute, type, default, condition and example against the target plugin. Check that setting-dependent defaults and no-result behavior are described accurately.
2. Parse the file as WordPress block markup when tooling is available. Check that the first pasteable block is `gdkob/source`, its argument blocks are correctly nested and the following blocks are recognized. Parsing alone does not establish editor validity.
3. When a suitable WordPress environment is available, paste into the Code editor, switch to the visual editor, save, reopen and inspect the published post. Check for validation warnings, editability, straight quotes, example rendering and the expected layout. Do not publish or alter a site without authorization. If this round trip cannot be performed, say so rather than claiming full validation.
4. If the layout differs despite consistent saved rows, inspect the rendered DOM, loaded styles and surrounding elements. A right-floated, automatically inserted GD Knowledge Base table of contents has been observed overlapping the Source Code block and changing the available width partway through its rows. Check its position or disable it for references where appropriate before changing block markup; other layout problems may have different causes.
5. Report the created files, how to enter the title and paste the body, the checks performed and any unresolved issues. Preserve existing unrelated documentation and user changes.
