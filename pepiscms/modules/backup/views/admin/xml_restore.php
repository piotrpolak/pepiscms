<?php $is_utilities_only_module = false; ?>
<?php if ($this->input->getParam('layout') != 'popup'): ?>
        <?php
        $breadcrumb_array = array(module_url() => $this->lang->line('backup_module_name'), module_url() . 'xml_restore' => $title);

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

<?= display_tip($this->lang->line('backup_xml_restore_tip')) ?>

<?= $formbuilder ?>