<?php if (!$is_password_expired): ?>
    <?= display_breadcrumb(array(admin_url() . 'changepassword' => $this->lang->line('changepassword_change_password')), 'pepiscms/theme/img/changepassword/password_32.png') ?>
<?php endif; ?>

<?= display_session_message() ?>

<?php if ($is_password_expired): ?>
    <?= display_error($this->lang->line('changepassword_dialog_your_password_has_expired_change_id_before_to_continue')) ?>
<?php endif; ?>

<?= display_notification($this->lang->line('changepassword_desc')) ?>

<?=$form?>
