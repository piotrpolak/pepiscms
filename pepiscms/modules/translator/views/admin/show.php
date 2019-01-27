<?php $is_utilities_only_module = false; ?>
<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php
    $breadcrumb_array = array(module_url() => $this->lang->line('translator_module_name').' v'.$version, module_url().'show/module-'.$module.'/file_name-'.$file_name => $this->lang->line('translator_module').' '.$module);

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

<style>
	.field_actions {
		display: block;
		padding-top: 3px !important;
	}
	td {
		padding-top: 15px !important;
		padding-bottom: 15px !important;
	}
	.action.active b {
		color: #333;
	}
	.files a {
		width: 200px;
		display: inline-block;
		line-height: 1.6em;
	}
</style>

<?php foreach($errors_now_writeable as $error_not_writeable ): ?>
	<?=display_error(sprintf($this->lang->line('translator_error_not_writeable'), $error_not_writeable ))?>
<?php endforeach; ?>

<?php if( count($language_files) > 1 ): ?>
    <p class="files">
    <?php foreach( $language_files as $language_file ): ?>
        <?php if( $file_name == $language_file ):?>
        <a href="<?=module_url()?>show/module-<?=$module?>/file_name-<?=$language_file?>" class="action active"><b><?=$language_file?></b></a>
        <?php else: ?>
        <a href="<?=module_url()?>show/module-<?=$module?>/file_name-<?=$language_file?>" class="action"><?=$language_file?></a>
        <?php endif; ?>
    <?php endforeach; ?>
    </p>
<?php endif; ?>

<?php if($file_name): ?>
	<div class="table_wrapper">
	<h4><?=$this->lang->line('translator_list_of_fields')?></h4>
	<table class="datagrid">
		<tr>
			<th><?=$this->lang->line('translator_field_name')?></th>
			<?php foreach($languages as $lang_name): ?>
			<th><?=$lang_name?></th>
			<?php endforeach; ?>
		</tr>
		<?php foreach($keys as $key): ?>
		<tr id="<?=$key?>">
			<td>
				<span class="translation">
					<b><?=shortname($key)?></b>
				</span>
				<span class="field_actions separable">
					<a href="<?=module_url()?>delete/module-<?=$module?>/key-<?=$key?>/file_name-<?=$file_name?>" class="ask_for_confirmation"><?=$this->lang->line('translator_delete_field')?></a>
				</span>
			</td>
			<?php foreach($languages as $lang_name): $has_translation = isset($translation[$lang_name][$key]); ?>
				<td>
					<span class="translation">
						<?php if($has_translation): ?>
						<?=$translation[$lang_name][$key]?>
						<?php else: ?>
						<b><?=$this->lang->line('translator_na_missing')?></b>
					</span>
					<?php endif; ?>
					<span class="field_actions separable">
						<a href="<?=module_url()?>edit/module-<?=$module?>/language-<?=$lang_name?>/key-<?=$key?>/file_name-<?=$file_name?>"><?=$this->lang->line('translator_edit_field')?></a>
					</span>
				</td>
			<?php endforeach; ?>
		</tr>
		<?php endforeach; ?>
	</table>
	</div>
<?php endif; ?>