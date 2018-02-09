<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php
    $breadcrumb_array = array(module_url() => $this->lang->line('logs_module_name'), module_url().'mylogin' => $title);

    // If module is displayed in UTILITIES and not in MENU then display a back link
    if( $is_utilities_only_module )
    {
        $breadcrumb_array = array_merge(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings')), $breadcrumb_array);
    }
    ?>

    <?= display_breadcrumb($breadcrumb_array, module_icon_url()) ?>
<?php endif; ?>

<?=display_session_message()?>

<?=$datagrid?>