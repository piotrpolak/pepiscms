<?= display_breadcrumb(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings'), admin_url() . 'setup' => $this->lang->line('setup_module_name')), 'pepiscms/theme/img/utilities/setup_32.png') ?>

<?= display_action_bar(array(array(
    'name' => $this->lang->line('global_button_back_to_utilities'),
    'link' => admin_url() . 'utilities',
    'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
))) ?>

<?= display_session_message() ?>

<?= $form ?>

<script src="pepiscms/js/setup.js?v=<?= PEPISCMS_VERSION ?>"></script>
