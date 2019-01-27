<?php $is_utilities_only_module = false; ?>
<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php
    $breadcrumb_array = array(module_url() => $this->lang->line('translator_module_name').' v'.$version, module_url().'show/module-'.$module.'/file_name-'.$file_name => $this->lang->line('translator_module').' '.$module, module_url().'edit/language-'.$language.'/module-'.$module.'/key-'.$key.'/file_name-'.$file_name => $key.' ('.$language.')');

    if( ModuleRunner::isModuleDisplayedInMenu() )
    {
        $parent_module_name = ModuleRunner::getParentModuleName();
        if( $parent_module_name )
        {
            $breadcrumb_array = array_merge(array(module_url($parent_module_name) => $this->Module_model->getModuleLabel($parent_module_name, $this->lang->getCurrentLanguage())), $breadcrumb_array);
        }
    }
    else
    {
        // If module is displayed in UTILITIES and not in MENU then display a back link
        if( ModuleRunner::isModuleDisplayedInUtilities($this->modulerunner->getRunningModuleName()) )
        {
            $is_utilities_only_module = true;
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

<?= display_session_message() ?>

<?php if( $en_value ): ?>
<?=display_tip('EN: '.$en_value)?>
<?php endif; ?>

<?=$form?>
