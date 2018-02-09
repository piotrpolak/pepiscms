<?php $is_utilities_only_module = FALSE; ?>
<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php
    $breadcrumb_array = array(module_url() => $this->lang->line('logs_module_name'), module_url().'details/id-'.$id => $title);

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
	<h4><?=$this->lang->line('logs_log_details')?></h4>
	<table class="datagrid">
        <tr>
            <td class="optionsname"><?=$this->lang->line('logs_timestamp')?></td>
            <td><?=$item->timestamp?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('logs_level')?></td>
            <td><?=$level_labels[$item->level]?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('logs_collection')?></td>
            <td><?=$item->collection?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('logs_message')?></td>
            <td><?=nl2br($item->message)?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('logs_user_id')?></td>
            <td><?php if($item->user_id): ?><a href="<?=module_url()?>user/id-<?=$item->user_id?>"><?=$users[$item->user_id]?></a><?php endif; ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('logs_ip')?></td>
            <td><a href="<?=module_url()?>ip/ip-<?=$item->ip?>"><?=$item->ip?></a</td>
        </tr>
	</table>
</div>