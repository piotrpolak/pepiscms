<?php include('application_header.php'); ?>
<div class="content_box has_title loginContainer">
    <h2><?= $lang->line('login_identify_yourself') ?></h2>	

    <?php if (isset($logoutsuccess)): ?>
        <?= display_notification($lang->line('login_dialog_logout')) ?>
    <?php endif; ?>

    <div class="box_content">
        <?= button_generic($lang->line('login_dialog_login_again'), admin_url() . 'login', 'pepiscms/theme/img/dialog/buttons/apply_16.png') ?>
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