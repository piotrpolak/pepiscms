<h1><?=$this->lang->line('installer_wizzard')?></h1>
<p><?=sprintf($this->lang->line('installer_wizzard_welcome_message'), PEPISCMS_VERSION)?></p>

<?=display_steps($steps, 'dot_steps') ?>

<?= $this->formbuilder->generate()?>

<script>
    $(document).ready(function() {
        function onValueChange()
        {
            var handler = $('#field_hostname, #field_username, #field_password, #field_database');

            var val = $('#database_config_type').val();

            if (val === "symfony_import")
            {
                handler.hide();
            }
            else
            {
                handler.show();
            }
        }
        onValueChange();

        $('#database_config_type').change(onValueChange);
    });
</script>