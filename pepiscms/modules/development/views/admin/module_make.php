<?php $is_utilities_only_module = FALSE; ?>
<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php
    $breadcrumb_array = array(module_url() => $this->lang->line('development_module_database_name'), module_url().'switch_user/' => $title);

    if( ModuleRunner::isModuleDisplayedInMenu() )
    {
        $parent_module_database_name = ModuleRunner::getParentModuleName();
        if( $parent_module_database_name )
        {
            $breadcrumb_array = array_merge(array(module_url($parent_module_database_name) => $this->Module_model->getModuleLabel($parent_module_database_name, $this->lang->getCurrentLanguage())), $breadcrumb_array);
        }
    }
    else
    {
        // If module is displayed in UTILITIES and not in MENU then display a back link
        if( ModuleRunner::isModuleDisplayedInUtilities($this->modulerunner->getRunningModuleName()) )
        {
            $is_utilities_only_module = TRUE;
            $breadcrumb_array = array_merge(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings')), $breadcrumb_array);
        }
    }
    ?>

    <?= display_breadcrumb($breadcrumb_array, module_icon_url()) ?>
<?php endif; ?>

<?php
$actions = array();
$actions[] = array(
    'name' => $this->lang->line('global_button_back'),
    'link' => module_url(),
    'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png'
);
?>
<?php if(count($actions)): ?>
    <?= display_action_bar($actions) ?>
<?php endif; ?>

<?=display_session_message()?>

<?php if (!$success): ?>
    <?= display_tip($this->lang->line('development_make_tip')) ?>
    <?= $form ?>
<?php else: ?>
    <?= display_notification($this->lang->line('development_make_success')) ?>
    <ul>
        <li><a href="<?= module_url($module_database_name) ?>"><?=sprintf($this->lang->line('modules_go_to_module'), $module_database_name)?></a></li>
        <li><a href="<?= module_url('translator') ?>show/module-<?= $module_database_name ?>/file_name-<?= $module_database_name ?>_lang.php"><?=sprintf($this->lang->line('development_edit_module_translation'), $module_database_name)?></a></li>
        <li><a href="<?= admin_url() ?>module/make/view-<?=$view?>"><?= $this->lang->line('development_make_a_new_module') ?></a></li>
    </ul>
<?php endif; ?>
