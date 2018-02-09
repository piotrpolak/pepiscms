<?=email_html_open()?>

    <?=email_h1_open()?>
    Witaj <?=$display_name?>! 
    <?=email_h1_close()?>

    <?=email_p_open()?>
    Zostałeś zarejestrowany jako użytkownik projektu <?=$site_name?>.
    <?=email_p_close()?>
    
    <?=email_p_open()?>
    Strona logowania: <?=email_a_open(admin_url())?><?=admin_url()?><?=email_a_close()?>.
    <?=email_p_close()?>
    
    <?=email_p_close()?>
    Twój login: <?=$user_email."\n"?><br>
    Twoje hasło: <?=$password."\n"?><br>
    <?=email_p_close()?>
    
    <?=email_p_open()?>
    Po pierwszym logowaniu możesz zmienić swoje hasło pod adresem <?=email_a_open(admin_url().'changepassword')?><?=admin_url()?>changepassword<?=email_a_close()?>
    <?=email_p_close()?>

    <?=email_html_footer_open()?>
    Ta wiadomość zostałą wygenerowana aumatycznie i wysłana z panelu PepisCMS <?=PEPISCMS_VERSION?><br>
    Prosimy nie odpowiadać na tego maila. Data: <?=$date?><br><br>
    <?=$site_name?>
    <?=email_html_footer_close()?>

<?=email_html_close()?>