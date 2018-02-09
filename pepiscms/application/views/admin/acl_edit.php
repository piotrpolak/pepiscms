<?php $this->load->helper('form'); ?>

<?= display_breadcrumb(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings'), admin_url() . 'acl' => $this->lang->line('acl_label_security_policy'), admin_url() . '/acl/edit/section-' . $section => $title), 'pepiscms/theme/img/acl/acl_32.png') ?>

<?= display_action_bar(array(array(
    'name' => $this->lang->line('global_button_back'),
    'link' => admin_url() . 'acl',
    'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
))) ?>

<?= display_session_message() ?>

<?php if (!count($entities)): ?>
    <?= display_tip($this->lang->line('acl_tip_no_entities_found')) ?>
<?php endif; ?>

<?php $disabled = '';
if (!$is_editable): $disabled = ' disabled="disabled"'; ?>
    <?= display_warning($this->lang->line('acl_warning_policy_read_only')) ?>
<?php endif; ?>

<form method="post" action="<?= admin_url() ?>acl/edit/section-<?= $section ?>">

    <div class="table_wrapper">
        <h4><?=$this->lang->line('acl_list_of_entities')?></h4>
        <table>
            <tr>
                <td class="optionsname"><label for="available_entities"><?=$this->lang->line('acl_available_entities_coma_separated')?></label></td>
                <td>
                    <input id="available_entities" type="text" name="available_entities"  value="<?= implode(',', $entities) ?>" class="text"<?= $disabled ?>>
                </td>
            </tr>
        </table>
    </div>

    <div class="buttons">
        <?php if ($is_editable): ?>
            <?=button_apply()?>
        <?php endif; ?>
    </div>

    <?php if (count($controllers) && count($entities)): ?>

        <?php if ($is_editable): ?>
            <?= display_tip($this->lang->line('acl_edit_tip')) ?>
        <?php endif; ?>

        <div class="table_wrapper">
            <h4><?= $title ?></h4>
            <table class="datagrid">
                <tr>
                    <th>Action</th>
                    <th><?=$this->lang->line('acl_entity')?></th>
                    <th class="medium"><?=$this->lang->line('acl_no_access')?></th>
                    <th class="medium"><?=$this->lang->line('acl_read')?></th>
                    <th class="medium"><?=$this->lang->line('acl_write')?></th>
                    <th class="medium"><?=$this->lang->line('acl_full_control')?></th>
                </tr>
                <?php foreach ($controllers as $controller): ?>

                    <?php if ($section != 'system') $controller->name = $section; ?>

                    <tr>
                        <td colspan="6" class="entity_group"><?= $controller->name ?></td>
                    </tr>

                    <?php foreach ($controller->methods as $method): $select_name = 'entity[' . $controller->name . '][' . $method->name . ']'; ?>
                        <tr>
                            <td class="optionsname"><?= $method->name ?></td>
                            <td style="text-align: right;">
                                <select name="<?= $select_name ?>"<?= $disabled ?>>
                                    <?php foreach ($entities as $entity): ?>
                                        <option value="<?= $entity ?>" <?= (isset($security_policy[$controller->name][$method->name]['entity']) && $entity == $security_policy[$controller->name][$method->name]['entity'] ? 'selected="selected"' : '') ?>>
                                            <?= $entity ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="blue"><input type="radio" name="access[<?= $controller->name ?>][<?= $method->name ?>]" value="NONE" <?= (isset($security_policy[$controller->name][$method->name]['access']) && $security_policy[$controller->name][$method->name]['access'] == SecurityPolicy::NONE ? 'checked="checked"' : '') . $disabled ?>></td>
                            <td class="green"><input type="radio" name="access[<?= $controller->name ?>][<?= $method->name ?>]" value="READ" <?= (isset($security_policy[$controller->name][$method->name]['access']) && $security_policy[$controller->name][$method->name]['access'] == SecurityPolicy::READ ? 'checked="checked"' : '') . $disabled ?>></td>
                            <td class="orange"><input type="radio" name="access[<?= $controller->name ?>][<?= $method->name ?>]" value="WRITE" <?= (isset($security_policy[$controller->name][$method->name]['access']) && $security_policy[$controller->name][$method->name]['access'] == SecurityPolicy::WRITE ? 'checked="checked"' : '') . $disabled ?>></td>
                            <td class="red"><input type="radio" name="access[<?= $controller->name ?>][<?= $method->name ?>]" value="FULL_CONTROL" <?= (isset($security_policy[$controller->name][$method->name]['access']) && $security_policy[$controller->name][$method->name]['access'] == SecurityPolicy::FULL_CONTROL ? 'checked="checked"' : '') . $disabled ?>></td>
                        </tr>
                    <?php endforeach; ?>

                <?php endforeach; ?>
            </table>
        </div>


        <div class="buttons">

            <?php if ($is_editable): ?>
                <?=button_cancel(admin_url() . 'acl') ?>
                <?=button_apply()?>
                <?=button_save('', FALSE, $lang->line('global_button_save_and_close'))?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</form>

<script type="text/javascript" src="<?=module_resources_url('cms_groups')?>cms_groups.js?v=<?= PEPISCMS_VERSION ?>"></script>