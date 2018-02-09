<?php foreach( $failed_configuration_tests as $test_name ): ?>
    <?= display_warning($lang->line('dashboard_test_'.$test_name)) ?>
<?php endforeach ?>

<div class="action_bar">
    <a href="<?= admin_url() ?>"><?= $this->lang->line('global_continue') ?></a>
</div>