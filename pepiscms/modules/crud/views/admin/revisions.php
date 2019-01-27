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
            $is_utilities_only_module = true;
            $breadcrumb_array = array_merge(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings')), $breadcrumb_array);
        }
    }

    $breadcrumb_array = array_merge($breadcrumb_array, array(module_url() => $title));
    $breadcrumb_array = array_merge($breadcrumb_array, array(module_url() . 'revisions/id-' . $id  => $this->lang->line('crud_label_revisions')));
    ?>
    <?= display_breadcrumb($breadcrumb_array, module_icon_url($module_name)) ?>
<?php endif; ?>

<?= display_session_message() ?>

<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php

    $actions = array(array(
        'name' => $this->lang->line('global_button_back'),
        'link' => module_url() . 'edit/id-' . $id . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . ($this->input->getParam('forced_filters') ? '/forced_filters-' . $this->input->getParam('forced_filters') : ''),
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
    ))  ?>
    <?= display_action_bar($actions) ?>
<?php endif; ?>

<?php if (isset($tooltip_text) && $tooltip_text): ?>
    <?= display_tip($tooltip_text) ?>
<?php endif; ?>

<?php if( !count($revision_summary) ): ?>
    <?= display_tip($this->lang->line('crud_revisions_no_entries')) ?>
<?php else: ?>
    <div class="table_wrapper">
        <h4><?=$this->lang->line('crud_label_revisions')?></h4>
        <table class="datagrid">
            <thead>
                <tr>
                    <th><?=$this->lang->line('crud_revisions_hour')?></th>
                    <th><?=$this->lang->line('crud_revisions_metadata')?></th>
                </tr>
            </thead>
            <tbody>
            <?php $last_date = false; ?>
            <?php foreach($revision_summary as $revision_summary_line): ?>
                <?php
                    $is_current_revision = false;
                    if( $last_date == false )
                    {
                        $is_current_revision = true;
                    }
                    $datetime = new DateTime($revision_summary_line->revision_datetime);
                    $date = $datetime->format('Y-m-d');
                ?>
                <?php if( $last_date !== $date ): $last_date = $date; ?>
                    <tr>
                        <td colspan="3" class="entity_group"><?=$date?></td>
                    </tr>
                <?php endif; ?>
                <tr <?php if($is_current_revision): ?>class="green"<?php endif; ?>>
                    <td><?php if($is_current_revision): ?><b><?php endif; ?><?=$datetime->format('H:i:s')?><?php if($is_current_revision): ?></b><?php endif; ?> <a href="<?=module_url().'revision/id-'.$id.'/revision_id-'.$revision_summary_line->id?><?=($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . ($this->input->getParam('forced_filters') ? '/forced_filters-' . $this->input->getParam('forced_filters') : '')?>"><?=$this->lang->line('crud_label_revision_show')?></a></td>
                    </td>
                    <td>
                        <?php if($is_current_revision): ?><b><?=$this->lang->line('crud_label_revision_current')?></b><?php endif; ?>
                        <?php if( count($revision_summary_line->metadata)): ?>
                            <ul>
                            <?php foreach( $revision_summary_line->metadata as $key => $value): ?>
                                    <li><?=$key?>: <?=$value?></li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>