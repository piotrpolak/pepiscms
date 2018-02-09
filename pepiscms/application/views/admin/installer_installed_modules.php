<h1><?=$this->lang->line('installer_wizzard')?></h1>
<p><?=sprintf($this->lang->line('installer_wizzard_welcome_message'), PEPISCMS_VERSION)?></p>

<?= display_steps($steps, 'dot_steps') ?>

<?=$this->formbuilder->generate()?>