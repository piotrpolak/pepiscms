<?php if ($layout != 'popup'): ?>
    <?php

    $method_name = isset($method_name) && $method_name ? $method_name : 'index';
    $breadcrumb_array = array();

    if ($bredcrumb_steps_assoc_array)
    {
        $breadcrumb_array = $bredcrumb_steps_assoc_array;
    }

    $breadcrumb_array = array_merge($breadcrumb_array, array($module_base_url . $method_name => $title));

    // If module is displayed in UTILITIES and not in MENU then display a back link
    if( $is_utilities_only_module )
    {
        $breadcrumb_array = array_merge(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings')), $breadcrumb_array);
    }
    elseif( ModuleRunner::isModuleDisplayedInMenu() )
    {
        $parent_module_name = ModuleRunner::getParentModuleName();
        if( $parent_module_name )
        {
            $breadcrumb_array = array_merge(array(module_url($parent_module_name) => $this->Module_model->getModuleLabel($parent_module_name, $this->lang->getCurrentLanguage())), $breadcrumb_array);
        }
    }
    ?>

    <?= display_breadcrumb($breadcrumb_array, module_icon_url($module_name)) ?>
<?php endif; ?>

<?php

if (!isset($actions))
{
    $actions = array();
}

if ($is_addable && SecurityManager::hasAccess($running_module, 'edit', $running_module))
{
    $add_actions = array(array(
            'name' => $add_new_item_label,
            'link' => $module_base_url . 'edit' . ($order_by ? '/order_by-' . $order_by : '') . ($order ? '/order-' . $order : '') . ($filters ? '/filters-' . $filters : '') . ($forced_filters ? '/forced_filters-' . DataGrid::encodeFiltersString($forced_filters) : '') . ($layout ? '/layout-' . $layout : ''),
            'icon' => 'pepiscms/theme/img/dialog/actions/add_16.png',
            'class' => ($is_popup_enabled ? 'popup' : ''),
    ));
    $actions = array_merge($add_actions, $actions);
}

if (isset($back_action_for_index) && $back_action_for_index)
{
    $actions = array_merge(array($back_action_for_index), $actions);
}
if( $is_utilities_only_module )
{
    $actions = array_merge(array(array(
        'name' => $this->lang->line('global_button_back_to_utilities'),
        'link' => admin_url() . 'utilities',
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png'
    )), $actions);
}
?>

<?php if (count($actions)): ?>
    <?= display_action_bar($actions) ?>
<?php endif; ?>

<?= display_session_message() ?>

<?php if (isset($tooltip_text) && $tooltip_text): ?>
    <?= display_tip($tooltip_text) ?>
<?php endif; ?>

<?= $datagrid ?>