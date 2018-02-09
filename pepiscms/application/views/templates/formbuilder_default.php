<?= $form->openForm() ?>
<?= $form->getValidation() ?>

<?php 
$c_groups = count($form->getInputGroups());
$hidden = '';
foreach( $form->getInputGroups() as $key => $group ): ?>

<?php
$group_id = str_replace( ' ', '_', trim(strtolower($key)) );
?>
    <div class="table_wrapper" id="group_<?= $group_id ?>">
        <?php if ($c_groups == 1 && $form->getFormBuilder()->getTitle()): ?>
            <h4><?= $form->getFormBuilder()->getTitle() ?></h4>
        <?php endif; ?>

        <?php if ($c_groups > 1): ?>
            <h4 <?php if ($group['description']): ?>title="<?= $group['description'] ?>"<?php endif; ?>><?= get_instance()->lang->line($key) ?></h4>
        <?php endif; ?>

        <table class="formbuilder">

            <?php
            foreach ($group['fields'] as $field_name):
                $type = $form->getFieldAttribute($field_name, 'input_type');
                if ($type == FormBuilder::HIDDEN)
                {
                    $hidden .= $form->getFieldInput($field_name) . "\n";
                    continue;
                }
                ?>
                <tr id="field_<?= $field_name ?>">
                    <td class="optionsname">
                        <?php echo $form->getFieldLabel($field_name);
                        if (($description = $form->getFieldAttribute($field_name, 'description'))):
                        ?>
                            <a title="<?= $description ?>" class="question rFloated"><img src="pepiscms/theme/img/dialog/formbuilder/question_20.png" alt=""></a>
                        <?php endif ?>
                    </td>
                    <td>
                        <?= $form->getFieldInput($field_name) ?>
                    </td>
                </tr>
            <?php endforeach; ?>

        </table>
    </div>
<?php endforeach; ?>

<!-- hidden -->
<?= $hidden ?>
<!-- END hidden -->

<?= $form->getFormButtons() ?>
<?= $form->closeForm() ?>