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
    $breadcrumb_array = array_merge($breadcrumb_array, array(module_url() . 'import' => $this->formbuilder->getTitle()));
    ?>
    <?= display_breadcrumb($breadcrumb_array, module_icon_url($module_name)) ?>
<?php endif; ?>

<?php if (!$this->input->getParam('direct')): ?>
    <?php

        if (!isset($actions) || count($actions) == 0)
        {
            $actions = array();
        }
    
        $actions = array_merge(array(array(
            'name' => $back_to_items_label,
            'link' => $back_link_for_edit . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . ($this->input->getParam('forced_filters') ? '/forced_filters-' . $this->input->getParam('forced_filters') : ''),
            'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
        )), $actions);
        
    ?>
    <?= display_action_bar($actions) ?>
<?php endif; ?>

<?= display_session_message() ?>

<?= display_tip($tip) ?>
<?= $formbuilder ?>
