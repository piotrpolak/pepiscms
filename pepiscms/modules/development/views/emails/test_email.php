<?=email_html_open()?>

    <?php /* <img src="" width="590" style="margin-bottom: 30px"> */ ?>
    
    <?=email_h1_open()?>
    Dear <?=$name?>!
    <?=email_h1_close()?>

    <?=email_p_open()?>
    Test message body. Message sent by <?=$email?> at <?=$date?> server time.
    <?=email_p_close()?>
    
    <?=email_p_open()?>
    You can allways login back in CMS at <?=email_a_open(admin_url())?><?=admin_url()?><?=email_a_close()?>
    <?=email_p_close()?>

    <?=email_h2_open()?>
    Lorem ipsum dolor sit amet
    <?=email_h2_close()?>
    <?=email_p_open()?>
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nisl eros, lacinia ut eros eget, facilisis dignissim arcu. Nullam nec tellus sed magna pulvinar tempus rutrum eu nisl. Cras fermentum accumsan lectus, porttitor fringilla turpis blandit eget. Praesent et neque quis lacus dictum fermentum sed eu tortor. Aenean pellentesque urna euismod est hendrerit eleifend at vitae arcu. Nullam blandit, sapien ac convallis vehicula, purus enim lobortis neque, in scelerisque tortor turpis eget tellus. In tellus mi, rutrum vitae ligula sed, sollicitudin mattis arcu. Fusce sollicitudin rutrum mi euismod sollicitudin. Etiam at mi quis massa tincidunt consequat vel eu risus. Proin est neque, bibendum non bibendum in, adipiscing egestas justo. Sed lacinia nisi ullamcorper lectus lacinia posuere.
    <?=email_p_close()?>
    
    <?=email_h2_open()?>
    Lorem ipsum dolor sit amet
    <?=email_h2_close()?>
    <?=email_p_open()?>
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nisl eros, lacinia ut eros eget, facilisis dignissim arcu. Nullam nec tellus sed magna pulvinar tempus rutrum eu nisl. Cras fermentum accumsan lectus, porttitor fringilla turpis blandit eget. Praesent et neque quis lacus dictum fermentum sed eu tortor. Aenean pellentesque urna euismod est hendrerit eleifend at vitae arcu. Nullam blandit, sapien ac convallis vehicula, purus enim lobortis neque, in scelerisque tortor turpis eget tellus. In tellus mi, rutrum vitae ligula sed, sollicitudin mattis arcu. Fusce sollicitudin rutrum mi euismod sollicitudin. Etiam at mi quis massa tincidunt consequat vel eu risus. Proin est neque, bibendum non bibendum in, adipiscing egestas justo. Sed lacinia nisi ullamcorper lectus lacinia posuere.
    <?=email_p_close()?>

    <?=email_html_footer_open()?>
    This a debug email sent from PepisCMS <?=PEPISCMS_VERSION?> administration panel.
    Please do not reply to this message - most probably it will not be read by a human.
    Have a nice day :)<br>
    
    URL: <?=email_a_open(admin_url())?><?=admin_url()?><?=email_a_close()?><br>
    IP: <?=$ip?>, Server date: <?=$date?><br>
    <?=email_html_footer_close()?>

<?=email_html_close()?>