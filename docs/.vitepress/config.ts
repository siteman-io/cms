import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Siteman.io",
  description: "Your website manager",
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Docs', link: '/getting-started/introduction' }
    ],

    sidebar: [
        {
            text: 'Getting Started',
            items: [
                { text: 'Introduction', link: '/getting-started/introduction' },
                { text: 'Installation', link: '/getting-started/installation' },
            ]
        },
        {
            text: 'Features',
            items: [
                { text: 'Page Types', link: '/features/page-types' },
                { text: 'Themes', link: '/features/themes' },
                { text: 'Blocks', link: '/features/blocks' },
                { text: 'Layouts', link: '/features/layouts' },
                { text: 'Menus', link: '/features/menus' },
                { text: 'Settings', link: '/features/settings' },
            ]
        }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/siteman-io/cms' }
    ]
  }
})
