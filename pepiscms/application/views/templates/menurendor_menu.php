<?php
if (!function_exists('menurendor_render_submenu')) {
    function menurendor_render_submenu($menu_items, $level = 1)
    {
        $prefix = "\t";
        for ($i = 0; $i < $level; $i++) {
            $prefix .= "\t";
        }

        $ul_class = '';
        if ($level > 1) {
            $ul_class = ' class="sub"';
        }
        $out = "\n" . $prefix . '<ul' . $ul_class . '>';
        foreach ($menu_items as $menu_item) {
            $has_submenu = false;
            if (count($menu_item[MenuRendor::PROP_SUBMENU])) {
                $has_submenu = true;
                $menu_item[MenuRendor::PROP_EXTRA_CSS_CLASSES][] = MenuRendor::HAS_SUBMENU_CSS_CLASS;
            }

            if ($menu_item[MenuRendor::PROP_IS_ACTIVE]) {
                $menu_item[MenuRendor::PROP_EXTRA_CSS_CLASSES][] = MenuRendor::ACTIVE_CSS_CLASS;
            } elseif ($has_submenu) {
                foreach ($menu_item[MenuRendor::PROP_SUBMENU] as $submenu_item) {
                    if ($submenu_item[MenuRendor::PROP_IS_ACTIVE]) {
                        $menu_item[MenuRendor::PROP_EXTRA_CSS_CLASSES][] = MenuRendor::ACTIVE_CSS_CLASS;
                        break;
                    }
                }
            }

            $classes = implode(' ', $menu_item[MenuRendor::PROP_EXTRA_CSS_CLASSES]);
            $out .= "\n" . $prefix . "\t" . '<li class="' . $classes . '">';

            $out .= '<a href="' . $menu_item[MenuRendor::PROP_URL] . '" title="' . $menu_item[MenuRendor::PROP_TITLE] . '"><img src="' . $menu_item[MenuRendor::PROP_ICON_URL] . '" alt=""><span>' . $menu_item[MenuRendor::PROP_LABEL] . '</span></a>';
            if ($has_submenu) {
                $out .= menurendor_render_submenu($menu_item[MenuRendor::PROP_SUBMENU], $level + 1);
            }
            $out .= '</li>';
        }
        $out .= "\n" . $prefix . '</ul>';

        return $out;
    }
}

?>
<nav id="primary_navigation"><?= menurendor_render_submenu($menu_items) ?></nav>
