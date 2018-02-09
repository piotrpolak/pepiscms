<?= display_breadcrumb(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings'), admin_url() . 'module' => $this->lang->line('label_installed_modules'), admin_url() . 'module/configure/' . $module => $this->lang->line('label_configure_module') . ' ' .  $this->Module_model->getModuleLabel($module, $this->lang->getCurrentLanguage())), module_icon_url($module)) ?>

<?= display_session_message() ?>

<?php
$actions = array();

$actions[] = array(
    'name' => $this->lang->line('global_button_back'),
    'link' => $this->formbuilder->getBackLink(),
    'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
);
?>

<?= display_action_bar($actions) ?>

<?= $form ?>