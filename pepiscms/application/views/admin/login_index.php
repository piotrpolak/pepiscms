<?php include('application_header.php'); ?>

<script>
    <!--
$('document').ready(function() {
<?php if ($user_email): ?>
            $('#password').focus();
<?php else: ?>
            $('#user_email').focus();
<?php endif; ?>
    });
//-->
</script>
<div class="content_box has_title loginContainer">
    <h2><?= $lang->line('login_identify_yourself') ?></h2> 
    <?php if (isset($sessionexpired)): ?>
        <?= display_warning($lang->line('login_dialog_session_expired_login_required')) ?>
    <?php endif; ?>

    <?php if ($account_is_locked): ?>
        <?= display_warning($lang->line('login_dialog_account_is_locked')) ?>
    <?php elseif ($auth_error): ?>
        <?= display_error($lang->line('login_dialog_incorrect_login')) ?>
    <?php endif; ?>

    <div class="box_content">
        <form id="loginForm" method="post" action="<?= admin_url() ?>login/dologin" class="validable">
            <fieldset> 
                <label for="user_email"><?= $this->lang->line('login_label_email_or_login') ?></label>
                <input name="user_email" id="user_email" type="text" class="text required" value="<?= htmlentities($user_email) ?>" size="30"> 
                <label for="password"><?= $this->lang->line('login_label_password') ?></label>
                <input name="password" id="password" type="password" class="text required" size="30">
                <?php if (FALSE && $this->config->item('cms_enable_reset_password')): // TODO Implement reset password ?>
                    <a href="<?= admin_url() ?>login/resetpassword" title="<?= $this->lang->line('login_reset_password_description') ?> &raquo;"><?= $this->lang->line('login_reset_password') ?> &raquo;</a> 
                <?php endif; ?>
                <div class="actionButtons"> 
                    <button> 
                        <img src="<?= base_url() ?>pepiscms/theme/default/images/icons/userlogin_icon.png" alt="userLogin icon"> <?= $this->lang->line('login_button_login') ?>
                    </button> 
                </div> 
            </fieldset> 
        </form> 
    </div> 
</div> 
</div>
</div>
<?php
$cms_login_page_description = $this->config->item('cms_login_page_description');
if ($cms_login_page_description):
    ?>
    <div id="login_description">
        <hr class="darkHr"> 
        <p> 
            <?php
            if (is_array($cms_login_page_description))
            {
                if (isset($cms_login_page_description[$this->lang->getCurrentLanguage()]))
                {
                    echo $cms_login_page_description[$this->lang->getCurrentLanguage()];
                }
                else
                {
                    echo array_pop(array_reverse($cms_login_page_description));
                }
            }
            else
            {
                echo $cms_login_page_description;
            }
            ?>
        </p> 
    </div>
<?php endif; ?>
<?php include('application_footer.php'); ?>