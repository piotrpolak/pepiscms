<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php

    $breadcrumb_array = array();

    if (isset($bredcrumb_steps_assoc_array) && $bredcrumb_steps_assoc_array)
    {
        $breadcrumb_array = $bredcrumb_steps_assoc_array;
    }

    $breadcrumb_array = array_merge($breadcrumb_array, array(module_url() => $title));
    $breadcrumb_array = array_merge($breadcrumb_array, array(module_url() . ($id ? ($is_preview ? 'preview/id-' . $id : 'edit/id-' . $id) : 'edit') => ($id ? ($is_preview ? $this->lang->line('crud_label_preview') : $this->lang->line('crud_label_modify')) : $this->formbuilder->getTitle())));

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
    <?= display_breadcrumb($breadcrumb_array, module_icon_url($module_name)) ?>
<?php endif; ?>

<?php if (!$this->input->getParam('direct')): ?>
    <?php

    if (!isset($actions) || count($actions) == 0)
    {
        $actions = array();
    }

    if ($id)
    {
        if ($is_preview && $is_editable)
        {
            $actions[] = array(
                'name' => $this->lang->line('crud_label_modify'),
                'link' => module_url() . 'edit/id-' . $id . ($this->input->getParam('order_by') ? '/order_by-' . $this->input->getParam('order_by') : '') . ($this->input->getParam('order') ? '/order-' . $this->input->getParam('order') : '') . ($this->input->getParam('filters') ? '/filters-' . $this->input->getParam('filters') : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . ($this->input->getParam('forced_filters') ? '/forced_filters-' . $this->input->getParam('forced_filters') : ''),
                'icon' => 'pepiscms/theme/img/dialog/actions/action_16.png',
            );
        }
        if (!$is_preview && $is_previewable)
        {
            $actions[] = array(
                'name' => $this->lang->line('crud_label_preview'),
                'link' => module_url() . 'preview/id-' . $id . ($this->input->getParam('order_by') ? '/order_by-' . $this->input->getParam('order_by') : '') . ($this->input->getParam('order') ? '/order-' . $this->input->getParam('order') : '') . ($this->input->getParam('filters') ? '/filters-' . $this->input->getParam('filters') : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . ($this->input->getParam('forced_filters') ? '/forced_filters-' . $this->input->getParam('forced_filters') : ''),
                'icon' => 'pepiscms/theme/img/dialog/actions/action_16.png',
            );
        }
    }
    if( isset($back_to_items_label) && isset($back_link_for_edit) && $back_link_for_edit  )
    {
        $actions = array_merge(array(array(
            'name' => $back_to_items_label,
            'link' => $back_link_for_edit . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . ($this->input->getParam('forced_filters') ? '/forced_filters-' . $this->input->getParam('forced_filters') : ''),
            'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
        )), $actions);
    }
    ?>
    <?= display_action_bar($actions) ?>
<?php endif; ?>

<?= display_session_message() ?>

<?php if (isset($tooltip_text) && $tooltip_text): ?>
    <?= display_tip($tooltip_text) ?>
<?php endif; ?>