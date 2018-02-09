<?php if (!$is_password_expired): ?>
    <?= display_breadcrumb(array(admin_url() . 'changepassword' => $this->lang->line('changepassword_change_password')), 'pepiscms/theme/img/changepassword/password_32.png') ?>
<?php endif; ?>

<?= display_session_message() ?>

<?php if ($is_password_expired): ?>
    <?= display_error($this->lang->line('changepassword_dialog_your_password_has_expired_change_id_before_to_continue')) ?>
<?php endif; ?>

<?php if ($warning): ?>
    <?= display_warning($warning) ?>
<?php endif; ?>

<?= display_notification($this->lang->line('changepassword_desc')) ?>

<form method="post" action="" class="smallform validable">

    <input type="hidden" name="confirm" value="true">
    <div class="table_wrapper">
        <h4><?= $this->lang->line('changepassword_change_password') ?></h4>
        <table>
            <?php if(!$is_user_password_virgin): ?>
            <tr>
                <td class="optionsname">
                    <label for="password"><?= $lang->line('changepassword_label_current_password') ?></label>
                </td>
                <td>
                    <input id="password" type="password" name="password" size="20" class="text required">
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="optionsname">
                    <label for="new_password"><?= $lang->line('changepassword_label_new_password') ?></label>
                </td>
                <td>
                    <input id="new_password" type="password" name="new_password" size="20" class="text required">
                </td>
            </tr>			
            <tr>
                <td class="optionsname">
                    <label for="confirm_new_password"><?= $lang->line('changepassword_label_confirm_new_password') ?></label>
                </td>
                <td>
                    <input id="confirm_new_password" type="password" name="confirm_new_password" size="20" class="text required">
                </td>
            </tr>					
        </table>
    </div>

    <div class="buttons">
        <?= button_cancel(admin_url()) ?>
        <?= button_save() ?>
    </div>

</form>