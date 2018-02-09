<?= $form->openForm() ?>
<?= $form->getValidation() ?>

<?php
$c_groups = count( $form->getInputGroups() );
$hidden = '';
foreach ( $form->getInputGroups() as $key => $group ):
    $group_id = str_replace( ' ', '_', trim(strtolower($key)) );
?>
    <div class="table_wrapper" id="group_<?= $group_id ?>">
        <?php if ($c_groups == 1 && $form->getFormBuilder()->getTitle()): ?>
            <h4><?= $form->getFormBuilder()->getTitle() ?></h4>
        <?php endif; ?>

        <?php if ($c_groups > 1): ?>
            <h4 <?php if ($group['description']): ?>title="<?= $group['description'] ?>"<?php endif; ?>><?= get_instance()->lang->line($key) ?></h4>
        <?php endif; ?>

        <div class="floatingform">
            <div class="formbuilder">

                <?php
                foreach ($group['fields'] as $field_name):

                    $type = $form->getFieldAttribute($field_name, 'input_type');
                    if ($type == FormBuilder::HIDDEN)
                    {
                        $hidden .= $form->getFieldInput($field_name) . "\n";
                        continue;
                    }
                    elseif ($type == FormBuilder::LINE_BREAK)
                    {
                        ?>
                        <div style="clear: both;" class="line_break"></div>
                        <?php
                        continue;
                    }

                    $full_width = $type == FormBuilder::TEXTAREA || $type == FormBuilder::RTF || $type == FormBuilder::RTF_FULL;
                    $auto_height = $type == FormBuilder::IMAGE || $type == FormBuilder::RADIO || $type == FormBuilder::MULTIPLECHECKBOX;
                    $auto_width = $type == FormBuilder::IMAGE;
                    ?>

                    <div id="field_<?= $field_name ?>" class="field<?= $full_width ? ' long' : '' ?><?= $auto_height ? ' autoheight' : '' ?><?= $auto_width ? ' autowidth' : '' ?>">
                        <div class="optionsname">
                            <?php echo $form->getFieldLabel($field_name);
                            if (($description = $form->getFieldAttribute($field_name, 'description'))):
                            ?>
                                <a title="<?= $description ?>" class="question"><img src="pepiscms/theme/img/dialog/formbuilder/question_20.png" alt=""></a>
                            <?php endif ?>
                        </div>
                        <div class="input">
                            <?= $form->getFieldInput($field_name) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div style="clear: both;"></div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
<?php endforeach; ?>

<!-- hidden -->
<?= $hidden ?>
<!-- END hidden -->

<?= $form->getFormButtons() ?>
<?= $form->closeForm() ?>
