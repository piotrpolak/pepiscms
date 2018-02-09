<h2>403 - <?= $this->lang->line('global_dialog_not_sufficient_priviliges_error') ?></h2>
<p><?= $this->lang->line('global_dialog_not_sufficient_priviliges_error_explanation') ?>:</p>
<ul>
    <li><?= $this->lang->line('global_dialog_not_sufficient_priviliges_error_reason_1') ?></li>
    <li><?= $this->lang->line('global_dialog_not_sufficient_priviliges_error_reason_2') ?></li>
</ul>

<div class="action_bar">
    <a href="<?= admin_url() ?>"><?= $this->lang->line('global_go_back_to_main_page') ?></a>
</div>