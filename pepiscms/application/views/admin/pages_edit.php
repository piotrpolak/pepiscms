<script src="pepiscms/js/pageeditor.js?v=<?= PEPISCMS_VERSION ?>"></script>
<?= display_breadcrumb(array(admin_url() . 'pages/index/language_code-' . $site_language->code . '/view-' . $view => $this->lang->line('pages_module_name'), admin_url() . 'pages/edit' . (isset($page->page_id) ? '/page_id-' . $page->page_id : '') . '/language_code-' . $site_language->code => (isset($page->page_id) ? $page->page_title : $this->lang->line('pages_write_new_page'))), 'pepiscms/theme/img/pages/page_white_world_32.png') ?>

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

<?= $form ?>
<script>
    var peui = new PageEditorUI({label_unsaved_changes: "<?= $lang->line('pages_label_all_unsaved_changes_will_be_lost_proceed') ?>"});
    peui.init();
</script>
