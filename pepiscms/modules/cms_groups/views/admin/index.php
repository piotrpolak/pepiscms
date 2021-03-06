<?php $is_utilities_only_module = false; ?>
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
            $is_utilities_only_module = true;
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
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png', // 'pepiscms/theme/img/dialog/actions/action_16.png', 'pepiscms/theme/img/dialog/actions/add_16.png'
        //'class' => ($this->input->getParam('layout') == 'popup' ? 'popup' : ''),
    );
}
?>

<?php if( SecurityManager::hasAccess( 'cms_groups', 'edit' )): ?>
<?php
    $actions[] = array(
            'name' => $lang->line('cms_groups_add'),
            'title' => $lang->line('cms_groups_add_desc'),
            'link' => module_url() . 'edit/',
            'icon' => 'pepiscms/theme/img/dialog/actions/add_16.png',
        );
?>
<?php endif; ?>

<?php if(count($actions)): ?>
    <?= display_action_bar($actions) ?>
<?php endif; ?>

<?=display_session_message()?>

<?=display_tip($this->lang->line('cms_groups_index_tip'))?>

<?=$datagrid?>
