<?php $is_utilities_only_module = false; ?>
<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php
    $breadcrumb_array = array(module_url() => $this->lang->line('logs_module_name'), module_url().'ip/ip-'.$ip => $title);

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

<?php if( $ip_info['domain'] || $ip_info['country'] || $ip_info['state'] || $ip_info['town'] ): ?>
    <div class="table_wrapper">
        <h4><?=$this->lang->line('logs_ip_information')?></h4>
        <table class="datagrid">

                <?php if( $ip_info['domain'] ): ?>
                <tr>
                    <td class="optionsname"><?=$this->lang->line('logs_domain')?></td>
                    <td><?=$ip_info['domain']?></td>
                </tr>
                <?php endif; ?>
                <?php if( $ip_info['country'] ): ?>
                <tr>
                    <td class="optionsname"><?=$this->lang->line('logs_country')?></td>
                    <td><?=$ip_info['country']?></td>
                </tr>
                <?php endif; ?>
                <?php if( $ip_info['state'] ): ?>
                <tr>
                    <td class="optionsname"><?=$this->lang->line('logs_state')?></td>
                    <td><?=$ip_info['state']?></td>
                </tr>
                <?php endif; ?>
                <?php if( $ip_info['town'] ): ?>
                <tr>
                    <td class="optionsname"><?=$this->lang->line('logs_town')?></td>
                    <td><?=$ip_info['town']?></td>
                </tr>
                <?php endif; ?>

        </table>
    </div>
<?php endif; ?>

<?php if( count($ip_users) ): ?>
<div class="table_wrapper">
	<h4><?=$this->lang->line('users_using_this_ip')?></h4>
	<table class="datagrid">
	<?php foreach( $ip_users as $key => $value ):?>
	<tr>
		<td><a href="<?=module_url()?>user/id-<?=$key?>"><?=$value?></a></td>
	</tr>
	<?php endforeach; ?>
	</table>
</div>
<?php endif; ?>