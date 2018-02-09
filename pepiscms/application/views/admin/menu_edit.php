<?= display_breadcrumb(array(admin_url() . 'pages/index/language_code-' . $site_language->code => $this->lang->line('pages_module_name'), admin_url() . 'menu/edit/item_id-' . $item_id . '/language_code-' . $site_language->code => $this->lang->line('pages_menuelement_edit')), 'pepiscms/theme/img/pages/page_white_world_32.png') ?>
<?php

$actions = array(
    array(
        'name' => $this->lang->line('global_button_back'),
        'title' => $this->lang->line('pages_list_desc'),
        'link' => admin_url() . 'pages/index/language_code-' . $site_language->code . '/view-' . $view,
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
    ),
);
?>
<?= display_action_bar($actions) ?>

<?= display_session_message() ?>

<?= $form?>