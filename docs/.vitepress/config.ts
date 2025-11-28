import { defineConfig } from "vitepress";

// https://vitepress.dev/reference/site-config
export default defineConfig({
    title: "Siteman CMS",
    description: "A modern, flexible content management system built on Laravel and Filament",
    head: [
        ['link', { rel: 'icon', href: '/favicon.ico' }],
        ['meta', { name: 'theme-color', content: '#3eaf7c' }],
        ['meta', { name: 'og:type', content: 'website' }],
        ['meta', { name: 'og:site_name', content: 'Siteman CMS' }],
    ],
    themeConfig: {
        // https://vitepress.dev/reference/default-theme-config
        logo: '/logo.svg',

        nav: [
            { text: "Home", link: "/" },
            { text: "Documentation", link: "/getting-started/introduction" },
            {
                text: "Resources",
                items: [
                    { text: "GitHub", link: "https://github.com/siteman-io/cms" },
                    { text: "Changelog", link: "https://github.com/siteman-io/cms/blob/main/CHANGELOG.md" },
                    { text: "Contributing", link: "/contributing/" },
                ]
            },
        ],

        sidebar: [
            {
                text: "Getting Started",
                collapsed: false,
                items: [
                    { text: "Introduction", link: "/getting-started/introduction" },
                    { text: "Quick Start", link: "/getting-started/quick-start" },
                    { text: "Installation", link: "/getting-started/installation" },
                    { text: "Configuration", link: "/getting-started/configuration" },
                ],
            },
            {
                text: "Features",
                collapsed: false,
                items: [
                    { text: "Page Types", link: "/features/page-types" },
                    { text: "Blog", link: "/features/blog" },
                    { text: "Tags", link: "/features/tags" },
                    { text: "RSS Feeds", link: "/features/rss" },
                    { text: "Themes", link: "/features/themes" },
                    { text: "Blocks", link: "/features/blocks" },
                    { text: "Layouts", link: "/features/layouts" },
                    { text: "Menus", link: "/features/menus" },
                    { text: "Settings", link: "/features/settings" },
                ],
            },
            {
                text: "Advanced",
                collapsed: false,
                items: [
                    { text: "Architecture", link: "/advanced/architecture" },
                    { text: "Facades & Helpers", link: "/advanced/facades-helpers" },
                ],
            },
            {
                text: "Contributing",
                collapsed: false,
                items: [
                    { text: "Contributing Guide", link: "/contributing/" },
                    { text: "Testing", link: "/contributing/testing" },
                ],
            },
        ],

        editLink: {
            pattern: 'https://github.com/siteman-io/cms/edit/main/docs/:path',
            text: 'Edit this page on GitHub'
        },

        lastUpdated: {
            text: 'Last updated',
            formatOptions: {
                dateStyle: 'medium',
                timeStyle: 'short'
            }
        },

        footer: {
            message: 'Released under the MIT License.',
            copyright: 'Copyright © 2024-present Siteman.io'
        },

        search: {
            provider: 'local',
            options: {
                detailedView: true,
                placeholder: 'Search documentation...',
            }
        },

        socialLinks: [
            { icon: "github", link: "https://github.com/siteman-io/cms" },
        ],

        outline: {
            level: [2, 3],
            label: 'On this page'
        },

        docFooter: {
            prev: 'Previous',
            next: 'Next'
        },

        returnToTopLabel: 'Return to top',
        sidebarMenuLabel: 'Menu',
        darkModeSwitchLabel: 'Appearance',
    },
});
