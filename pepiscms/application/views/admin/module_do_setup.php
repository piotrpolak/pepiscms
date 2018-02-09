<?= display_breadcrumb(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings'), admin_url() . 'module' => $this->lang->line('label_installed_modules'), admin_url() . 'module/do_setup/module-' . $module => $this->lang->line('label_module_setup') . ' ' . $module_label), module_icon_url($module)) ?>

<?= display_action_bar(array(array(
    'name' => $this->lang->line('global_button_back'),
    'link' => $this->formbuilder->getBackLink(),
    'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
))) ?>

<?= display_session_message() ?>

<?= $form ?>

<script type="text/javascript">
    $(document).ready(function() {
        function onIsDisplayedInMenuChange()
        {
            var handler = $('#field_parent_module_id');

            var val = $('#is_displayed_in_menu').prop('checked');

            if (val)
            {
                handler.show();
            }
            else
            {
                handler.hide();
            }
        }
        onIsDisplayedInMenuChange();

        $('#is_displayed_in_menu').change(onIsDisplayedInMenuChange);
    });
</script>