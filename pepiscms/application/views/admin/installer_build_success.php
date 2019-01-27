<h1><?=$this->lang->line('installer_wizzard')?></h1>
<p><?=sprintf($this->lang->line('installer_wizzard_welcome_message'), PEPISCMS_VERSION)?></p>

<?= display_steps($steps, 'dot_steps') ?>

<?=display_notification($this->lang->line('installer_build_success_message'))?>

<p><?=button_next(admin_url().'installer/go_to_admin/', 'rFloated', false, $this->lang->line('installer_go_to_administration_panel'))?></p>
