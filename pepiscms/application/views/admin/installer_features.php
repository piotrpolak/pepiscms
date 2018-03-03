<h1><?=$this->lang->line('installer_wizzard')?></h1>
<p><?=sprintf($this->lang->line('installer_wizzard_welcome_message'), PEPISCMS_VERSION)?></p>

<?= display_steps($steps, 'dot_steps') ?>

<?=$this->formbuilder->generate()?>

<script>
    $(document).ready(function() {

        var $defaultLanguageSelect = $('#default_language');

        function onAvailableLanguagesChange(event)
        {
            $.each($('#field_available_languages input[type=checkbox]'), function(index, $checkbox)
            {
                $checkbox = $($checkbox);
                var $element = $('option[value='+$checkbox.val()+']');
                if( $checkbox.prop('checked') == true )
                {
                    $element.removeAttr('disabled');
                }
                else
                {
                    $element.attr('disabled', 'disabled');
                }
            });
        }
        onAvailableLanguagesChange();

        $('#field_available_languages input[type=checkbox]').change(onAvailableLanguagesChange);
    });
</script>