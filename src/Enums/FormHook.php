<?php declare(strict_types=1);

namespace Siteman\Cms\Enums;

enum FormHook: string
{
    case POST_TABS = 'post_tabs';
    case POST_SIDEBAR = 'post_sidebar';
    case POST_BLOCKS = 'post_blocks';
    case POST_MAIN = 'post_main';
}
