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

<?= display_session_message() ?>

<?=display_tip( $this->lang->line('translator_index_tip') )?>

<div class="table_wrapper">
	<h4><?=$this->lang->line('translator_list_of_modules')?></h4>
	<table>
		<tr>
			<th><?=$this->lang->line('translator_module')?></th>
			<th><?=$this->lang->line('translator_available_languages')?></th>
		</tr>

        <?php foreach( $available_modules as $modules_group_name => $modules): if(!count($modules)) { continue; } ?>

        <tr>
            <td colspan="2" class="entity_group"><?=$this->lang->line('translator_'.$modules_group_name)?></td>
        </tr>

            <?php foreach($modules as $module => $languages): ?>
                <tr>
                    <td><a href="<?=module_url()?>show/module-<?=$module?>"><img src="<?=module_icon_url($module)?>" alt="icon" /></a> <a href="<?=module_url()?>show/module-<?=$module?>"><b><?=$this->Module_model->getModuleLabel($module, $this->lang->getCurrentLanguage())?></b></a></td>
                    <td><a href="<?=module_url()?>show/module-<?=$module?>"><?=implode(', ', $languages)?></a></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
	</table>
</div>