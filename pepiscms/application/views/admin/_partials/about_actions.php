<?php
$actions = array(
    array(
        'name' => $this->lang->line('global_button_back'),
        'link' => admin_url() . 'utilities',
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
    ),
    array(
        'name' => $this->lang->line('global_about_pepiscms'),
        'link' => admin_url() . 'about',
        'icon' => 'pepiscms/theme/img/dialog/actions/action_16.png',
    ),
    array(
        'name' => $this->lang->line('about_label_theme_preview'),
        'link' => admin_url() . 'about/theme',
        'icon' => 'pepiscms/theme/img/dialog/actions/action_16.png',
    ),
);
?>
<?= display_action_bar($actions) ?>