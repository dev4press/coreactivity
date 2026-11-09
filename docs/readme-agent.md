# Documentation guide for agents

This file is for people and agents maintaining the documentation. It is not a documentation topic: keep it outside the configured topics directory and do not add it to the Writerside tree. The instructions are intended to be reused across plugins; derive project-specific details from the plugin and its documentation rather than copying values from another project.

## Identify the project

Before writing, inspect the plugin's main file and header, existing source and documentation, `writerside.cfg`, the configured tree, and `cfg/buildprofiles.xml`. Determine the plugin's display name, slug, edition names, version, relevant features, and documentation structure from the current project. Verify behavior and requirements against the current implementation; do not assume another plugin has the same UI or capabilities. If sources disagree, investigate and report unresolved discrepancies instead of guessing.

## Project layout

- `writerside.cfg` identifies the Writerside module, topic and image directories, and instance tree. Read it to locate those resources; directory names and tree filenames may vary between projects.
- The configured `.tree` file defines the documentation navigation and start page. Use it to understand the intended structure and preserve that structure unless the task calls for changing it. Add new topics to the appropriate branch when they should appear in navigation.
- The configured topics directory contains the Markdown documentation. The configured images directory holds documentation images, including screenshots supplied by the maintainer.
- Name all Markdown files and images with lowercase, URL-friendly names. Separate words with hyphens (`-`), not underscores.
- When a panel article is created, link it from the plugin panels organization article as well as the appropriate navigation. Find that article in the current tree or topics; it can include additional information and a short note about the About panel.

## Metadata comments

Another system processes topic metadata written as HTML comments after the H1 title and before the body. Preserve the spelling, punctuation, comment syntax, and placeholder tokens. Do not remove or rewrite existing comments casually. Check the current project's topics and confirm unresolved rules before creating metadata whose meaning or value is uncertain.

The two common patterns are:

```md
# Section title

<!-- import: skip -->
```

```md
# Article title

<!-- category: plugin-setup -->
<!-- prefix: setup -->
<!-- tag: install,update -->
<!-- seo-title: %%post_title%% - %%sitetitle%% -->
<!-- seo-description: A description of this article. -->
<!-- seo-keywords: install,update -->
```

The second example is illustrative: `setup` is an additional prefix, not a plugin name. Omit the `prefix` comment when the default plugin-name prefix is sufficient; otherwise use only the additional portion and choose the other values for the topic. Most metadata tags are required for imported topics, but the exact required fields by topic type remain to be confirmed. A topic marked `import: skip` needs no other metadata tags. For FAQ topics, use `type: faq` and choose a relevant category, such as `getting-started`, `common-issues`, or `troubleshooting`.

| Comment | Purpose and guidance | What still needs confirmation |
| --- | --- | --- |
| `import: skip` | Marks a topic to be skipped by the import system. No other metadata tags are needed for a skipped topic. | Whether other values are supported. |
| `type` | Document type: `article`, `user-guide`, or `faq`. If omitted, it defaults to `article`. | Any additional rules for choosing a type. |
| `category` | Categorizes the imported topic. Use a value appropriate to its place in the current documentation. | Allowed values, whether multiple categories are possible, and whether required. |
| `prefix` | The plugin name is the default prefix. Omit this comment when only the default is needed. For an extended prefix, include only the additional portion: for example, a plugin-name prefix extended with `setup` is written as `<!-- prefix: setup -->`. Do not repeat the plugin name or slug. | What the prefix affects. |
| `tag` | Comma-separated topic tags. | Allowed values, whether to reuse or create tags, and whether required. |
| `banner` | Banner identifier for selected topics. The maintainer assigns banners manually; do not invent or assign one. | How the identifier is resolved and which topics need one. |
| `seo-title` | SEO title, which may include supported placeholders. | Required status. |
| `seo-description` | Topic-specific SEO description. | Required status and any length or formatting rules. |
| `seo-keywords` | Comma-separated SEO keywords. | Required status and any keyword rules. |

For now, use only the literal metadata placeholders `%%post_title%%` and `%%sitetitle%%`. Preserve them exactly; do not replace them with the current title or site name unless instructed. Plugin content may separately use its own syntax, such as a shortcode or a setting's display token; verify and document those as plugin features, not metadata placeholders.

Ask the maintainer for the authoritative list of required and optional comments for each document type, valid values and defaults, what `prefix` affects, and the substitution rules for the two metadata placeholders. Update this guide when those answers are available. Until then, follow the closest existing example and report unresolved metadata rather than inventing values.

## Writing and links

- Put the main title on the first line as the only level-one heading (`#`). Organize the rest of the topic with headings from `##` through at most `####`, following the hierarchy without skipping levels. Use Markdown blockquotes (`>`) for notes when appropriate.
- Do not use tables in documentation articles. Use clear prose or lists when a comparison or grouping is needed.
- Write for plugin users with clear headings, accurate UI labels, and practical steps. Explain relevant options and how they affect the result, including edition or integration conditions where applicable.
- For a Pro-only feature, include a Markdown note stating that Pro is required and link to the current plugin pricing page inside the note.
- Use the existing tree and topics as a starting point, not proof that their content is complete or correct. Correct inaccuracies relevant to the assigned work and call out unrelated issues.
- Include useful internal links wherever they help a reader continue a task, understand a feature, or find related details. Use descriptive link text and topic-relative Markdown links; link to a specific heading when it is helpful. Prefer an existing topic over duplicating a lengthy explanation. Check that each topic and heading target exists; do not add links to unwritten topics.
- Read `cfg/buildprofiles.xml` for the current plugin's footer links labeled **Home Page** and **Pricing**. Where useful in context, link to the plugin home page for product information and to the pricing page when discussing plans or upgrading. Use the configured URLs rather than constructing them from a slug. Avoid unnecessary promotional links or repeating them in every topic.
- Check configured external URLs against other project sources, such as the plugin header and existing documentation. If they conflict, appear malformed, or cannot be verified, report the discrepancy and avoid asserting or propagating a questionable URL until it is resolved. Do not silently substitute a different destination.

## Screenshots and verification

- Identify places where a screenshot would help explain a screen, setting, or result. Ask the maintainer for the image, describing the screen and state to capture and suggesting a descriptive filename. Never fabricate a screenshot or present an unverified mockup as the plugin UI.
- Store supplied screenshots in the configured images directory and reference them from the relevant topic with descriptive alt text, following Writerside's image conventions. Verify that the image resolves in preview or build. Until a screenshot exists, report the request separately rather than adding a broken image link.
- After changes, check the tree, internal and external links, images, metadata comments, and the relevant Writerside preview or build if available. Report checks that could not be run.
