<?= display_breadcrumb(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings'), admin_url() . 'module' => $this->lang->line('label_installed_modules'), admin_url() . 'module/make' => $this->lang->line('modules_make_a_new_module')), 'pepiscms/theme/img/module/make_32.png') ?>

<?php
$actions = array();
$actions[] = array(
    'name' => $this->lang->line('label_show_installed_modules'),
    'link' => admin_url() . 'module/index/view-'.$view,
    'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
);
?>
<?= display_action_bar($actions) ?>

<?= display_session_message() ?>

<?php if (!$success): ?>
    <?= display_tip($this->lang->line('modules_make_tip')) ?>
    <?= $form ?>
<?php else: ?>
    <?= display_notification($this->lang->line('modules_make_success')) ?>
    <ul>
        <li><a href="<?= module_url($module_name) ?>"><?=sprintf($this->lang->line('modules_go_to_module'), $module_name)?></a></li>
        <li><a href="<?= module_url('translator') ?>show/module-<?= $module_name ?>/file_name-<?= $module_name ?>_lang.php"><?=sprintf($this->lang->line('modules_edit_module_translation'), $module_name)?></a></li>
        <li><a href="<?= admin_url() ?>module/make/view-<?=$view?>"><?= $this->lang->line('modules_make_a_new_module') ?></a></li>
    </ul>
<?php endif; ?>
