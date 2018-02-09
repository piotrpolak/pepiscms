<?php include($this->load->resolveModuleDirectory('crud') . 'views/admin/_edit_top.php'); ?>

<?= display_session_message() ?>

<?php if ($non_standard_account): ?>
    <?= display_warning($this->lang->line('cms_users_warning_non_standard_account')) ?>
<?php endif; ?>

<?= $form ?>

<script type="text/javascript" src="<?=module_resources_url()?>cms_users.js?v=<?= PEPISCMS_VERSION ?>"></script>