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
<?php if(count($actions)): ?>
    <?= display_action_bar($actions) ?>
<?php endif; ?>

<?= display_session_message() ?>

<?php if( !$kernel ): ?>
    <?= display_warning($this->lang->line('symfony2_bridge_no_kernel_error')) ?>
<?php else: ?>

    <?= display_tip($this->lang->line('symfony2_bridge_module_description')) ?>
    <ul class="dashboard_actions clear">
        <?= dashboard_box($this->lang->line('symfony2_bridge_clear_symfony2_cache'), module_url() . 'clear_cache', module_resources_url() . 'clear_cache_32.png') ?>
        <?= dashboard_box($this->lang->line('symfony2_bridge_symfony2_logs'), module_url() . 'logs', module_resources_url() . 'logs_32.png') ?>
    </ul>

    <div class="table_wrapper">
        <h4><?=$this->lang->line('symfony2_bridge_summary')?></h4>
        <table class="datagrid">
            <tr>
                <td class="optionsname"><?=$this->lang->line('symfony2_bridge_version')?>:</td>
                <td><?=constant(get_class($kernel).'::VERSION')?></td>
            </tr>
            <tr>
                <td class="optionsname"><?=$this->lang->line('symfony2_bridge_kernel_name')?>:</td>
                <td><?=$kernel->getName()?></td>
            </tr>
            <tr>
                <td class="optionsname"><?=$this->lang->line('symfony2_bridge_root_directory')?>:</td>
                <td><?=$kernel->getRootDir()?>/</td>
            </tr>
            <tr>
                <td class="optionsname"><?=$this->lang->line('symfony2_bridge_production_cache_directory')?>:</td>
                <td><?=$this->Symfony2_cache_model->getCacheDir('prod')?></td>
            </tr>
            <tr>
                <td class="optionsname"><?=$this->lang->line('symfony2_bridge_development_cache_directory')?>:</td>
                <td><?=$this->Symfony2_cache_model->getCacheDir('dev')?></td>
            </tr>
        </table>
    </div>
<?php endif; ?>