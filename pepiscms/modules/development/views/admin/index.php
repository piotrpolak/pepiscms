<?php $is_utilities_only_module = FALSE; ?>
<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php
    $breadcrumb_array = array(module_url() => $title);

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
            $is_utilities_only_module = TRUE;
            $breadcrumb_array = array_merge(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings')), $breadcrumb_array);
        }
    }
    ?>

    <?= display_breadcrumb($breadcrumb_array, module_icon_url()) ?>
<?php endif; ?>

<?php
$actions = array();
if( $is_utilities_only_module )
{
    $actions[] = array(
        'name' => $this->lang->line('global_button_back_to_utilities'),
        'link' => admin_url() . 'utilities',
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png'
    );
}
?>
<?php if(count($actions)): ?>
    <?= display_action_bar($actions) ?>
<?php endif; ?>

<?=display_session_message()?>

<?= display_tip($this->lang->line('development_index_tip')) ?>

<ul class="dashboard_actions clear">
    <?= dashboard_box($this->lang->line('development_fix_missing_translations'), module_url() . 'fix_missing_translation_files', module_resources_url() . 'language_32.png') ?>
    <?= dashboard_box($this->lang->line('development_generate_header_file'), module_url() . 'generate_header_file', module_resources_url() . 'toolbox_32.png') ?>
    <?= dashboard_box($this->lang->line('development_send_test_email'), module_url() . 'send_test_email', module_resources_url() . 'email_32.png') ?>
    <?= dashboard_box($this->lang->line('development_switch_user'), module_url() . 'switch_user', module_resources_url() . 'switch_user_32.png') ?>
    <?= dashboard_box($this->lang->line('development_fix_autoincrement_on_cms_tables'), module_url() . 'fix_autoincrement_on_cms_tables', module_resources_url() . 'database_fix_32.png') ?>
 </ul>
