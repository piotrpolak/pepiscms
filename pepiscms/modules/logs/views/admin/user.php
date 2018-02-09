<?php $is_utilities_only_module = FALSE; ?>
<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php
    $breadcrumb_array = array(module_url() => $this->lang->line('logs_module_name'), module_url().'user/id-'.$user_data->user_id => $title);

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

<?= display_session_message() ?>

<div class="table_wrapper">
	<h4><?=$this->lang->line('logs_user_details')?></h4>
	<table class="datagrid">
	<tr>
		<td class="optionsname">
            <?=$this->lang->line('logs_email')?>
		</td>
		<td>
			<?=$user_data->user_email?><?php if(SecurityManager::hasAccess('users', 'edit', 'users') && $this->Module_model->isInstalled( 'users' )):?> - <a href="<?=module_url('users')?>/edit/id-<?=$user_data->user_id?>" target="_blank">Edit profile</a><?php endif; ?>
		</td>
	</tr>
	<tr>
		<td class="optionsname">
            <?=$this->lang->line('logs_name')?>
		</td>
		<td>
			<?=$user_data->display_name?>
		</td>
	</tr>
	<tr>
		<td class="optionsname">
            <?=$this->lang->line('logs_last_seen')?>
		</td>
		<td>
			<?=$user_activity['last_seen_timestamp']?>
		</td>
	</tr>
	<tr>
		<td class="optionsname">
            <?=$this->lang->line('logs_first_seen')?>
		</td>
		<td>
			<?=$user_activity['first_seen_timestamp']?>
		</td>
	</tr>
	</table>
</div>

<?php if( count($related_users) > 0 ): ?>
<div class="table_wrapper">
	<h4><?=$this->lang->line('users_using_this_ip')?></h4>
	<table class="datagrid">
	<?php foreach( $related_users as $related_user ):?>
	<tr>
		<td>
			<a href="<?=module_url()?>user/id-<?=$related_user->user_id?>"><?=$related_user->user_email?></a>
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
</div>
<?php endif; ?>

<div class="table_wrapper">
	<h4><?=$this->lang->line('users_user_ips')?></h4>
	<table class="datagrid">
		<tr>
			<th><?=$this->lang->line('logs_ip')?></th>
			<th><?=$this->lang->line('logs_last_seen')?></th>
			<th><?=$this->lang->line('logs_first_seen')?></th>
		</tr>
	<?php foreach( $user_ips_activity as $ip => $activity ):?>
	<tr>
		<td>
			<a href="<?=module_url()?>ip/ip-<?=$ip?>"><?=$ip?></a>
		</td>
		<td><?=$activity['last_seen_timestamp']?></td>
		<td><?=$activity['first_seen_timestamp']?></td>
	</tr>
	<?php endforeach; ?>
	</table>
</div>