<?= display_breadcrumb(array(admin_url() . 'about' => $this->lang->line('global_about_pepiscms')), 'pepiscms/theme/img/about/about_32.png') ?>

<?php require '_partials/about_actions.php' ?>

<?= display_session_message() ?>

<div id="two_pane_layout">
    <div class="left_option_pane">
        <div style="padding-right: 2em;">
            <p>PepisCMS is an extensible web based content management system developed by Piotr Polak <a href="http://www.polak.ro/" target="_blank">www.polak.ro</a>.</p>
            <p>PepisCMS is written in PHP on top of extended CodeIgniter framework.</p>
            <h1 class="contrasted">Copyright</h1>
            <p>&copy; Copyright Piotr Polak 2007-<?= date('Y') ?></p>
        </div>
    </div>
    <div class="right_content_pane">
        <h1 class="contrasted">Changelog</h1>

        <?= $changelog ?>
    </div>
</div>