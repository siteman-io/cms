---
outline: deep
---

# Introduction

Siteman is a CMS package for Laravel, built on Filament. It gives you pages, blocks, menus, blogging, and theming - the basics you need for most websites.

::: info
Siteman is still in beta. Things work, but expect some rough edges and API changes. Feedback welcome!
:::

## What You Get

- **Pages** with a block-based editor and tree structure
- **Blog** using parent-child page relationships
- **Menus** assignable to theme locations
- **Tags** for organizing content
- **Themes** to control frontend rendering
- **Settings** in the admin panel

## Core Concepts

**Pages** are the main content type. They have a title, slug, and can contain blocks. Pages can nest up to 3 levels deep.

**Themes** control your frontend. A theme registers menu locations, layouts, and provides Blade views.

**Blocks** are content pieces inside pages - Markdown text, images, or whatever you build.

**Page Types** determine behavior:
- `Page` - block-based content
- `BlogIndex` - lists child pages as posts
- `TagIndex` - shows pages by tag
- `RssFeed` - generates XML feed

## Next Steps

- [Quick Start](/getting-started/quick-start) - get running in 5 minutes
- [Installation](/getting-started/installation) - detailed setup
- [Configuration](/getting-started/configuration) - customize things
