<?php if( !$add_new ):
	$label = $lang->line('cms_groups_header_edit_group');
else:
	$label = $lang->line('cms_groups_header_group_add');
endif; ?>

<?php $is_utilities_only_module = false; ?>
<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php
    $breadcrumb_array = array(module_url() => $this->lang->line('cms_groups_module_name'), module_url().'edit'.(isset($group->group_id) ? '/id-'.$group->group_id : '') => $label);

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
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png'
    );
}
?>

<?php
	$actions = array(
        array(
            'name' => $this->lang->line('global_button_back'),
            'link' => module_url(),
            'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
        ),
	);
?>
<?php if(count($actions)): ?>
    <?= display_action_bar($actions) ?>
<?php endif; ?>

<?=display_session_message()?>

<?php echo $this->form_validation->error_string(get_warning_begin(), get_warning_end()); ?>

<form method="post" action="" class="smallform">

	<input type="hidden" name="confirm" value="true">
	<?php if(!$add_new):?>
	<input type="hidden" name="initial_group_name" value="<?=$group->group_name?>">
	<?php endif; ?>

	<?=display_tip($this->lang->line('cms_groups_edit_tip'))?>
	
	<div class="table_wrapper">
		<h4><?=$label?></h4>
		<table>
			<tr>
				<td class="optionsname">
				   <label for="display_name"><?=$lang->line('cms_groups_label_group_name')?>:</label>
				</td>
				<td>
				   <input id="display_name" type="text" name="display_name"  value="<?php echo set_value('display_name', $group->group_name); ?>" class="text">
				</td>
			</tr>
		</table>
		<table class="datagrid">
			<tr>
				<th><?=$this->lang->line('cms_groups_section')?></th>
				<th><?=$this->lang->line('acl_entity')?></th>
				<th class="medium"><?=$this->lang->line('acl_no_access')?></th>
				<th class="medium"><?=$this->lang->line('acl_read')?></th>
				<th class="medium"><?=$this->lang->line('acl_write')?></th>
				<th class="medium"><?=$this->lang->line('acl_full_control')?></th>
			</tr>


            <?php foreach( $entities as $entities_group_name => $controllers): ?>

                <tr>
                    <td colspan="6" class="entity_group"><?=$this->lang->line('cms_groups_'.$entities_group_name)?></td>
                </tr>

                <?php foreach( $controllers as $controller => $entities ): ?>
                    <?php foreach($entities as $entity): if( $entity == 'DUMMY' ) continue; ?>
                        <tr>
                            <td><b><?=ucfirst(str_replace('_', ' ', $controller))?></b></td>
                            <td style="text-align: right;"><b><?=ucfirst(str_replace('_', ' ', $entity))?></b></td>
                            <td class="blue"><input type="radio" value="NONE" name="access[<?=$entity?>]" <?=( ( !isset($group->access[$entity]) || $group->access[$entity] == SecurityPolicy::NONE ) || (!isset($_POST['access'][$entity]) || $_POST['access'][$entity] == 'NONE') ? 'checked="checked"' : '')?>></td>
                            <td class="green"><input type="radio" value="READ" name="access[<?=$entity?>]" <?=( ( isset($group->access[$entity]) && $group->access[$entity] == SecurityPolicy::READ ) || isset($_POST['access'][$entity]) && $_POST['access'][$entity] == 'READ' ? 'checked="checked"' : '')?>></td>
                            <td class="orange"><input type="radio" value="WRITE" name="access[<?=$entity?>]" <?=( ( isset($group->access[$entity]) && $group->access[$entity] == SecurityPolicy::WRITE) || isset($_POST['access'][$entity]) && $_POST['access'][$entity] == 'WRITE' ? 'checked="checked"' : '')?>></td>
                            <td class="red"><input type="radio" value="FULL_CONTROL" name="access[<?=$entity?>]" <?=( ( isset($group->access[$entity]) && $group->access[$entity] == SecurityPolicy::FULL_CONTROL) || isset($_POST['access'][$entity]) && $_POST['access'][$entity] == 'FULL_CONTROL' ? 'checked="checked"' : '')?>></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>

            <?php endforeach; ?>

		</table>
	</div>
	
	<div class="buttons">
		<?=button_cancel(module_url())?>
        <?php if(isset($group->group_id)): ?>
        <?=button_apply()?>
        <?php endif; ?>
		<?=button_save('', false, $this->lang->line('global_button_save_and_close'))?>
	</div>
</form>

<script type="text/javascript" src="<?=module_resources_url()?>cms_groups.js?v=<?= PEPISCMS_VERSION ?>"></script>