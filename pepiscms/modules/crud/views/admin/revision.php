<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php

    $breadcrumb_array = array();

    if ($bredcrumb_steps_assoc_array)
    {
        $breadcrumb_array = $bredcrumb_steps_assoc_array;
    }

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

    $breadcrumb_array = array_merge($breadcrumb_array, array(module_url() => $title));
    $breadcrumb_array = array_merge($breadcrumb_array, array(module_url() . 'revisions/id-' . $id  => $this->lang->line('crud_label_revisions')));
    $breadcrumb_array = array_merge($breadcrumb_array, array(module_url() . 'revisions/id-' . $id.'/revision_id-'.$revision_id  => $this->lang->line('crud_label_revision').' '.$revision_id));
    ?>
    <?= display_breadcrumb($breadcrumb_array, module_icon_url($module_name)) ?>
<?php endif; ?>

<?= display_session_message() ?>

<?php if (!$this->input->getParam('direct')): ?>
    <?php

    $actions = array(array(
        'name' => $this->lang->line('crud_label_back_list_of_revisions'),
        'link' => module_url().'revisions/id-' . $id . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . ($this->input->getParam('forced_filters') ? '/forced_filters-' . $this->input->getParam('forced_filters') : ''),
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
    ))  ?>
    <?= display_action_bar($actions) ?>
<?php endif; ?>

<?php if (isset($tooltip_text) && $tooltip_text): ?>
    <?= display_tip($tooltip_text) ?>
<?php endif; ?>

<?php if($is_identical): ?>
    <?= display_notification($this->lang->line('crud_revisions_identical')) ?>
<?php endif; ?>

<div class="table_wrapper">
    <h4><?=$this->lang->line('crud_label_revision')?> <?=$revision_id?></h4>
    <table class="datagrid">
        <?php
        $revision = (array) $revision;
        $revision_current = (array) $revision_current;
        ?>
        <tr>
            <th><?=$this->lang->line('crud_label_revision_field')?></th>
            <th><b><?=$this->lang->line('crud_label_revision_current')?></b></th>
            <th><?=sprintf($this->lang->line('crud_label_revision_older'), $revision_id)?> <?php //$revision->revision_datetime?></th>
        </tr>
        <?php foreach($revision as $key => $value): ?>
            <tr<?=($revision_current[$key] != $revision[$key] ? ' class="revision_changed red"' : '')?>>
                <td class="optionsname"><?=isset($key_names[$key])?$key_names[$key]:$key?></td>
                <td class="green"><?=html_escape($revision_current[$key])?></td>
                <td class="<?php if($revision_current[$key] != $revision[$key]):?>red<?php else: ?>gray<?php endif; ?>"><i><?=html_escape($revision[$key])?></i>
                <?php if($revision_current[$key] != $revision[$key]):?><br><br><a href="<?=module_url()?>revisionrestorefield/id-<?=$id?>/revision_id-<?=$revision_id?>/field-<?=$key?><?=($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . ($this->input->getParam('forced_filters') ? '/forced_filters-' . $this->input->getParam('forced_filters') : '')?>" class="ask_for_confirmation"><?=$this->lang->line('crud_label_revision_restore')?></a><?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<style>
    .revision_changed td {
        font-weight: bold;
        color: red;
    }
</style>