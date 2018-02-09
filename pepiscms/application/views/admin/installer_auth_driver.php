<h1><?=$this->lang->line('installer_wizzard')?></h1>
<p><?=sprintf($this->lang->line('installer_wizzard_welcome_message'), PEPISCMS_VERSION)?></p>

<?= display_steps($steps, 'dot_steps') ?>

<?= $this->formbuilder->generate() ?>

<script type="text/javascript">
    $(document).ready(function() {
        function onAuthDriverChange()
        {
            var handler = $('#field_cas_server, #field_cas_port, #field_cas_path');

            var val = $('#authentification_driver').val();

            if (val === "cas")
            {
                handler.show();
            }
            else
            {
                handler.hide();
            }
        }
        onAuthDriverChange();

        $('#authentification_driver').change(onAuthDriverChange);
    });
</script>