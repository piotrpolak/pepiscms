<?=email_html_open()?>

    <?=email_h1_open()?>
    Hello <?=$display_name?>! 
    <?=email_h1_close()?>

    <?=email_p_open()?>
    You have been registered as a user for <?=$site_name?>.
    <?=email_p_close()?>
    
    <?=email_p_open()?>
    Login page: <?=email_a_open(admin_url())?><?=admin_url()?><?=email_a_close()?>.
    <?=email_p_close()?>
    
    <?=email_p_open()?>
    Login: <?=$user_email."\n"?><br>
    Password: <?=$password."\n"?><br>
    <?=email_p_close()?>
    
    <?=email_p_open()?>
    You are encouraged to change your password after you login at <?=email_a_open(admin_url().'changepassword')?><?=admin_url()?>changepassword<?=email_a_close()?>
    <?=email_p_close()?>

    <?=email_html_footer_open()?>
    This is an automatically generated message sent from sent from PepisCMS <?=PEPISCMS_VERSION?><br>
    Please do not reply to this email. Date: <?=$date?><br><br>
    <?=$site_name?>
    <?=email_html_footer_close()?>

<?=email_html_close()?>