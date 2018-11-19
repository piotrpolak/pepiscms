<?php if (!$render_as_module): ?>
    <?= display_breadcrumb(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings'), admin_url() . 'module' => $this->lang->line('modules_installed_modules'), admin_url() . 'module/do_setup/module-' . $module => $this->lang->line('modules_module_setup') . ' ' . $module_label), module_icon_url($module)) ?>
    <?= display_action_bar(array(array(
        'name' => $this->lang->line('global_button_back'),
        'link' => $this->formbuilder->getBackLink(),
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
    ))) ?>
<?php else: ?>
    <?= display_breadcrumb(array(admin_url() . 'module/do_setup/module-' . $module . '/render_as_module-true' => $this->lang->line('modules_module_setup') . ' ' . $module_label), module_icon_url($module)) ?>
<?php endif; ?>

<?= display_session_message() ?>

<?= $form ?>