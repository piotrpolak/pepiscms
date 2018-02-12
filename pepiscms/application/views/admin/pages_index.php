<?php

// FIXME Remove links from table for menu elements if menu disabled for instance

function formatCells($menu, $level = 1, &$lang, &$url_suffix, $site_language, $view)
{
    static $tabs = "\t\t";

    if (!count($menu) > 0)
    {
        return;
    }

    foreach ($menu as $menu_element)
    {

        $has_submenu = ( isset($menu_element['submenu']) && count($menu_element['submenu']) > 0);

        if ($menu_element['page_id'] != null)
        {

            // Pages

            echo $tabs . '<tr">' . "\n";
            echo $tabs . "\t" . '<td class="first" style="padding-left:' . (($level - 1) * 40 + 10) . 'px;">' . "\n";
            echo $tabs . "\t\t" . '<img src="pepiscms/theme/img/pages/' . ($menu_element['page_is_default'] > 0 ? 'page_home_22.png' : 'page_white_22.png') . '" alt="preview">';
            echo '<span class="menu_element_name"><a href="' . admin_url() . 'pages/edit/page_id-' . $menu_element['page_id'] . '/language_code-' . $site_language->code . '/view-' . $view . '">' . $menu_element['item_name'] . ' <b class="binvisible">[' . $lang->line('pages_link_edit') . ']</b></a></span>';
            echo ($menu_element['page_is_default'] > 0 ? ' <strong>' . $lang->line('pages_dialog_default_document') . '</strong>' : '') . "\n";
            echo '<span class="menu_element_uri"><a href="' . ($site_language->is_default == 1 ? '' : $site_language->code . '/') . $menu_element['page_uri'] . $url_suffix . '" class="pages_uri" title="' . $menu_element['page_uri'] . $url_suffix . '">' . shortname($menu_element['page_uri'] . $url_suffix) . ' <b class="binvisible">[' . $lang->line('pages_link_preview') . ']</b></a></span>';
            echo $tabs . "\t" . '</td>' . "\n";

            if (get_instance()->config->item('feature_is_enabled_menu'))
            {
                echo $tabs . "\t" . '<td>';
                echo '<a href="' . admin_url() . 'pages/move/direction-up/item_id-' . $menu_element['item_id'] . '/language_code-' . $site_language->code . '/view-' . $view . '" class="moveUp"><img src="pepiscms/theme/img/dialog/datagrid/up_16.png" alt="up"></a> <a href="' . admin_url() . 'pages/move/direction-down/item_id-' . $menu_element['item_id'] . '/language_code-' . $site_language->code . '" class="moveDown"><img src="pepiscms/theme/img/dialog/datagrid/down_16.png" alt="down"></a>';
                echo '</td>' . "\n";
            }



            if (get_instance()->config->item('feature_is_enabled_menu'))
            {
                echo $tabs . "\t" . '<td class="link">';
                echo '<a href="' . admin_url() . 'pages/edit/parent_item_id-' . $menu_element['item_id'] . '/language_code-' . $site_language->code . '/view-' . $view . '" title="' . $lang->line('pages_link_add_child') . '"><img src="pepiscms/theme/img/dialog/actions/add_16.png" alt="add">';
                echo '</a></td>' . "\n";
            }

            echo $tabs . "\t" . '<td class="link"><a href="' . admin_url() . 'pages/edit/page_id-' . $menu_element['page_id'] . '/language_code-' . $site_language->code . '/view-' . $view . '" title="' . $lang->line('pages_link_edit') . '"><img src="pepiscms/theme/img/pages/page_edit_22.png" alt="edit"></a></td>' . "\n";

            if (get_instance()->config->item('feature_is_enabled_menu'))
            {
                echo $tabs . "\t" . '<td class="link">';
                if (!$has_submenu)
                {
                    echo $tabs . "\t" . '<a href="' . admin_url() . 'pages/deletemenuelement/item_id-' . $menu_element['item_id'] . '/language_code-' . $site_language->code . '/view-' . $view . '/delete_contents-true" title="' . $lang->line('pages_link_delete') . '" class="delete json"><img src="pepiscms/theme/img/pages/page_delete_22.png" alt=""></a>';
                }
                echo '</td>' . "\n";
            }
            echo $tabs . "\t" . '<td class="link">' .
            ($menu_element['page_is_default'] > 0 ? '<span class="table_text">' . $lang->line('pages_dialog_default_document') . '</span>' : '<a href="' . admin_url() . 'pages/setdefault/page_id-' . $menu_element['page_id'] . '/language_code-' . $site_language->code . '/view-' . $view . '" title="' . $lang->line('pages_link_set_default') . '"><img src="pepiscms/theme/img/pages/page_home_22.png" alt="set default"></a>') . '</td>' . "\n" .
            $tabs . '</tr>' . "\n\r";
        }
        else
        {
            // Mapped elements
            echo $tabs . '<tr>' . "\n";
            echo $tabs . "\t" . '<td class="first" style="padding-left:' . (($level - 1) * 40 + 10) . 'px;"><img src="pepiscms/theme/img/pages/page_white_link_22.png" alt="menu element">';
            echo '<span class="menu_element_name">';
            if (get_instance()->config->item('feature_is_enabled_menu'))
            {
                echo '<a href="' . admin_url() . 'menumanager/edit/item_id-' . $menu_element['item_id'] . '/language_code-' . $site_language->code . '/view-' . $view . '">' . $menu_element['item_name'] . '  <b class="binvisible">[' . $lang->line('pages_link_edit') . ']</b></a>';
            }
            else
            {
                echo $menu_element['item_name'];
            }
            echo '</span>';
            echo '<span class="menu_element_uri"><a href="' . $menu_element['item_uri'] . '" class="pages_uri" title="' . $menu_element['item_uri'] . '">' . shortname($menu_element['item_uri']) . ' <b class="binvisible">[' . $lang->line('pages_link_preview') . ']</b></a></span>';
            echo '</td>' . "\n";

            if (get_instance()->config->item('feature_is_enabled_menu'))
            {
                echo $tabs . "\t" . '<td>';
                echo '<a href="' . admin_url() . 'pages/move/direction-up/item_id-' . $menu_element['item_id'] . '/language_code-' . $site_language->code . '/view-' . $view . '" class="moveUp"><img src="pepiscms/theme/img/dialog/datagrid/up_16.png" alt="up"></a> <a href="' . admin_url() . 'pages/move/direction-down/item_id-' . $menu_element['item_id'] . '/language_code-' . $site_language->code . '/view-' . $view . '" class="moveDown"><img src="pepiscms/theme/img/dialog/datagrid/down_16.png" alt="down"></a>';
                echo '</td>' . "\n";

                echo $tabs . "\t" . '<td class="link"></td>' . "\n";
            }


            echo $tabs . "\t" . '<td class="link">';
            if (get_instance()->config->item('feature_is_enabled_menu'))
            {
                echo '<a href="' . admin_url() . 'menumanager/edit/item_id-' . $menu_element['item_id'] . '/language_code-' . $site_language->code . '/view-' . $view . '" title="' . $lang->line('pages_link_edit') . '"><img src="pepiscms/theme/img/pages/page_edit_22.png" alt="edit"></a>';
            }
            echo '</td>' . "\n";

            if (get_instance()->config->item('feature_is_enabled_menu'))
            {
                echo $tabs . "\t" . '<td class="link">' . "\n";
                if (!$has_submenu)
                {
                    echo $tabs . "\t" . '<a href="' . admin_url() . 'pages/deletemenuelement/item_id-' . $menu_element['item_id'] . '/language_code-' . $site_language->code . '/view-' . $view . '" title="' . $lang->line('pages_link_delete') . '" class="delete json"><img src="pepiscms/theme/img/pages/page_delete_22.png" alt=""></a>';
                }
                echo '</td>' . "\n";
            }

            echo $tabs . "\t" . '<td class="link"><span class="table_text">' . $lang->line('pages_dialog_element_mapped') . '</span></td>' . "\n";
            echo $tabs . '</tr>' . "\n";
        }

        if ($has_submenu)
        {
            formatCells($menu_element['submenu'], $level + 1, $lang, $url_suffix, $site_language, $view);
        }
    }
}
?>
<?php if($site_language): ?>
    <script language="javascript" type="text/javascript" src="pepiscms/js/pages_search.js?v=<?= PEPISCMS_VERSION ?>"></script>

    <div class="lFloated">
        <?= display_breadcrumb(array(admin_url() . 'pages/index/language_code-' . $site_language->code . '/view-' . $view => $this->lang->line('pages_module_name')), 'pepiscms/theme/img/pages/page_white_world_32.png') ?>
    </div>


    <div class="rFloated filter_form">
        <div class="datagrid_filter_box">
            <div class="view_selector">
                <label for="searchbox"><?= $lang->line('pages_search') ?></label>
                <input type="text" size="8" maxlength="8" class="text date hasDatepicker" value="" id="searchbox" name="filters[timestamp_ge]"><a id="searchbox_reset_button" href="#"><img alt="remove" src="pepiscms/theme/img/dialog/actions/delete_16.png"></a>
            </div>
        </div>

        <?php if (count($site_languages) > 1): ?>
            <div class="datagrid_filter_box">
                <div class="view_selector"><?= $lang->line('pages_label_select_site_s_language') ?>
                    <?php foreach ($site_languages as $sl): ?>
                        <a href="<?= admin_url() ?>pages/index/language_code-<?= $sl->code ?>/view-<?= $view ?>"<?= ($site_language->code == $sl->code ? ' class="active"' : '') ?>><?= $sl->label ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="datagrid_filter_box">
            <div id="pagesview" class="view_selector"><?= $lang->line('pages_view') ?>
                <a href="<?= admin_url() ?>pages/setviewtype/language_code-<?= $site_language->code ?>/view-simple"<?= ($view == 'simple' ? ' class="active"' : '') ?>><?= $lang->line('pages_view_simple') ?></a>
                <a href="<?= admin_url() ?>pages/setviewtype/language_code-<?= $site_language->code ?>/view-tree"<?= ($view != 'simple' ? ' class="active"' : '') ?>><?= $lang->line('pages_view_tree') ?></a>
            </div>
        </div>
    </div>

    <?php
    $actions = array();
    $actions[] = array(
        'name' => $this->lang->line('pages_write_new_page'),
        'title' => $this->lang->line('pages_write_new_page_desc'),
        'link' => admin_url() . 'pages/edit/language_code-' . $site_language->code . '/view-' . $view,
        'icon' => 'pepiscms/theme/img/dialog/actions/add_16.png',
    );
    if ($this->config->item('feature_is_enabled_menu'))
    {
        $actions[] = array(
            'name' => $this->lang->line('pages_menuelement_add'),
            'title' => $this->lang->line('pages_menuelement_add_desc'),
            'link' => admin_url() . 'menumanager/edit/language_code-' . $site_language->code . '/view-' . $view,
            'icon' => 'pepiscms/theme/img/dialog/actions/add_16.png',
        );
    }
    ?>
    <?= display_action_bar($actions) ?>

    <?php if ($simple_session_message != null): ?>
        <?= $simple_session_message ?>
    <?php endif; ?>

    <?php if ($menu == null): ?>
        <?= display_tip(sprintf($lang->line('pages_dialog_no_menu_elements_so_far'), '<a href="' . admin_url() . 'pages/edit/language_code-' . $site_language->code . '">', '</a>')) ?>
    <?php else: ?>
        <div class="table_wrapper">
            <h4><?= $lang->line('pages_header_pages_pinned_to_menu') ?></h4>
            <table class="datagrid">

                <tr>
                    <th><?= $lang->line('pages_cl_name_of_menu_element') ?></th>
                    <?php if (get_instance()->config->item('feature_is_enabled_menu')): // Remove 2 of them ?>
                        <th style="width: 10px;"></th>
                        <th style="width: 40px;"></th>

                        <th style="width: 40px;"></th>
                    <?php endif; ?>
                    <th style="width: 40px;"></th>
                    <th style="width: 40px;"></th>

                </tr>


                <?= formatCells($menu, 1, $lang, $url_suffix, $site_language, $view); ?>
            </table>
        </div>
    <?php endif; ?>

    <?php if (count($pages) > 0): ?>


        <div class="table_wrapper">
            <h4><?= $lang->line('pages_header_pages_not_pinned_to_menu') ?></h4>
            <table class="datagrid">

                <tr>
                    <th><?= $lang->line('pages_cl_document_uri') ?></th>
                    <th style="width: 40px;"></th>
                    <?php if (get_instance()->config->item('feature_is_enabled_menu')): ?>
                        <th style="width: 40px;"></th>
                    <?php endif; ?>
                    <th style="width: 40px;"></th>
                </tr>

                <?php foreach ($pages as $page): ?>

                    <tr>
                        <td class="first"><img src="pepiscms/theme/img/pages/<?= ($page->page_is_default > 0 ? 'page_home_22.png' : 'page_white_22.png') ?>" alt="preview"> <span class="menu_element_name"><a href="<?= admin_url() ?>pages/edit/page_id-<?= $page->page_id ?>/language_code-<?= $site_language->code ?>"><?= $page->page_title ?> <b class="binvisible">[<?= $lang->line('pages_link_edit') ?>]</b></a></span> <?= ($page->page_is_default > 0 ? ' <strong>' . $lang->line('pages_dialog_default_document') . '</strong>' : '') ?>
                            <span class="menu_element_uri"><a href="<?= ($site_language->is_default == 1 ? '' : $site_language->code . '/') . $page->page_uri . $url_suffix ?>" class="pages_uri"><?= shortname($page->page_uri . $url_suffix) ?> <b class="binvisible">[<?= $lang->line('pages_link_preview') ?>]</b></a></span></td>
                        <td class="link"><a href="<?= admin_url() ?>pages/edit/page_id-<?= $page->page_id ?>/language_code-<?= $site_language->code ?>" title="<?= $lang->line('pages_link_edit') ?>"><img src="pepiscms/theme/img/pages/page_edit_22.png" alt="edit"></a></td>
                        <?php if (get_instance()->config->item('feature_is_enabled_menu')): ?>
                            <td class="link"><a href="<?= admin_url() ?>pages/delete/page_id-<?= $page->page_id ?>/language_code-<?= $site_language->code ?>" title="<?= $lang->line('pages_link_delete') ?>" class="delete json"><img src="pepiscms/theme/img/pages/page_delete_22.png" alt=""></a></td>
                        <?php endif; ?>
                        <td class="link"><?= ($page->page_is_default > 0 ? '<span class="table_text">' . $lang->line('pages_dialog_default_document') . '</span>' : '<a href="' . admin_url() . 'pages/setdefault/page_id-' . $page->page_id . '/language_code-' . $site_language->code . '" title="' . $lang->line('pages_link_set_default') . '"><img src="pepiscms/theme/img/pages/page_home_22.png" alt="set default"></a>'); ?></td>
                    </tr>

                <?php endforeach; ?>

            </table>
        </div>

    <?php endif; ?>
<?php else: ?>
    <?= display_error($lang->line('pages_site_languages_are_not_defined')) ?>
<?php endif; ?>