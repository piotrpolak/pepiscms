<?php

function formatCells($menu, $level = 1, &$lang, &$url_suffix, $site_language)
{
    static $tabs = "\t\t";

    if (!count($menu) > 0)
        return;

    foreach ($menu as $menu_element)
    {
        if ($menu_element['page_id'] != null)
        {
            // FOR MENU ELEMENTS

            echo $tabs . '<tr>' . "\n" .
            $tabs . "\t" . '<td class="first">' . "\n" .
            $tabs . "\t\t" . '<img src="pepiscms/theme/img/pages/' . ($menu_element['page_is_default'] > 0 ? 'page_home_22.png' : 'page_white_22.png') . '" alt="preview">' .
            '<span class="menu_element_name"><a href="' . admin_url() . 'pages/edit/page_id-' . $menu_element['page_id'] . '/language_code-' . $site_language->code . '/view-simple">' . $menu_element['item_name'] . ' <b class="binvisible">[' . $lang->line('pages_link_edit') . ']</b></a></span>' .
            ($menu_element['page_is_default'] > 0 ? ' <strong>' . $lang->line('pages_dialog_default_document') . '</strong>' : '') . "\n" .
            '<span class="menu_element_uri"><a href="' . ($site_language->is_default == 1 ? '' : $site_language->code . '/') . $menu_element['page_uri'] . $url_suffix . '" class="pages_uri" title="' . $menu_element['page_uri'] . $url_suffix . '">' . shortname($menu_element['page_uri'] . $url_suffix) . ' <b class="binvisible">[' . $lang->line('pages_link_preview') . ']</b></a></span>' .
            $tabs . "\t" . '</td>' . "\n" .
            $tabs . "\t" . '<td class="link"><a href="' . admin_url() . 'pages/edit/page_id-' . $menu_element['page_id'] . '/language_code-' . $site_language->code . '/view-simple" title="' . $lang->line('pages_link_edit') . '"><img src="pepiscms/theme/img/pages/page_edit_22.png" alt="edit"></a></td>' . "\n";
            if (get_instance()->config->item('feature_is_enabled_menu'))
            {
                echo $tabs . "\t" . '<td class="link"><a class="delete json" href="' . admin_url() . 'pages/deletemenuelement/item_id-' . $menu_element['item_id'] . '/language_code-' . $site_language->code . '/delete_contents-true/view-simple" title="' . $lang->line('pages_link_delete') . '"><img src="pepiscms/theme/img/pages/page_delete_22.png" alt=""></a></td>' . "\n";
            }
        }

        formatCells($menu_element['submenu'], $level + 1, $lang, $url_suffix, $site_language);
    }
}
?>
<script language="javascript" type="text/javascript" src="pepiscms/js/pages_search.js?v=<?= PEPISCMS_VERSION ?>"></script>

<?php if($site_language): ?>

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
                        <a href="<?= admin_url() ?>pages/index/language_code-<?= $sl->code ?>"<?= ($site_language->code == $sl->code ? ' class="active"' : '') ?>><?= $sl->label ?></a>
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

    <?php if ($menu == null && count($pages) == 0): ?>

        <?= display_tip(sprintf($lang->line('pages_dialog_no_menu_elements_so_far'), '<a href="' . admin_url() . 'pages/edit/language_code-' . $site_language->code . '">', '</a>')) ?>

    <?php else: ?>
        <div class="table_wrapper">
            <h4><?= $lang->line('pages_header_pages_pinned_to_menu') ?></h4>
            <table class="datagrid">

                <tr>
                    <th><?= $lang->line('pages_cl_document_uri') ?></th>
                    <th style="width: 40px;"></th>

                    <?php if (get_instance()->config->item('feature_is_enabled_menu')): ?>
                        <th style="width: 40px;"></th>
    <?php endif; ?>
                </tr>
                <?= formatCells($menu, 1, $lang, $url_suffix, $site_language); ?>
                <?php if (count($pages) > 0): ?>
                        <?php foreach ($pages as $page): ?>

                        <tr>
                            <td class="first"><img src="pepiscms/theme/img/pages/<?= ($page->page_is_default > 0 ? 'page_home_22.png' : 'page_white_22.png') ?>" alt="preview"> <span class="menu_element_name"><a href="<?= admin_url() ?>pages/edit/page_id-<?= $page->page_id ?>/language_code-<?= $site_language->code ?>/view-simple"><?= $page->page_title ?> <b class="binvisible">[<?= $lang->line('pages_link_edit') ?>]</b></a></span> <?= ($page->page_is_default > 0 ? ' <strong>' . $lang->line('pages_dialog_default_document') . '</strong>' : '') ?>
                                <span class="menu_element_uri"><a href="<?= ($site_language->is_default == 1 ? '' : $site_language->code . '/') . $page->page_uri . $url_suffix ?>" class="pages_uri"><?= shortname($page->page_uri . $url_suffix) ?> <b class="binvisible">[<?= $lang->line('pages_link_preview') ?>]</b></a></span></td>
                            <td class="link"><a href="<?= admin_url() ?>pages/edit/page_id-<?= $page->page_id ?>/language_code-<?= $site_language->code ?>/view-simple" title="<?= $lang->line('pages_link_edit') ?>"><img src="pepiscms/theme/img/pages/page_edit_22.png" alt="edit"></a></td>
                            <?php if (get_instance()->config->item('feature_is_enabled_menu')): ?>
                                <td class="link"><a class="delete json" href="<?= admin_url() ?>pages/delete/page_id-<?= $page->page_id ?>/language_code-<?= $site_language->code ?>/view-simple" title="<?= $lang->line('pages_link_delete') ?>"><img src="pepiscms/theme/img/pages/page_delete_22.png" alt=""></a></td>
                            <?php endif; ?>
                        </tr>

                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>
    <?php endif; ?>
<?php else: ?>
    <?= display_error($lang->line('pages_site_languages_are_not_defined')) ?>
<?php endif; ?>