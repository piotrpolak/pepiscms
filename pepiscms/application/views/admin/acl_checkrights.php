<?php $this->load->helper('form'); ?>

<?= display_breadcrumb(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings'), admin_url() . 'acl' => $this->lang->line('acl_label_security_policy'), admin_url() . 'acl/checkrights' => $this->lang->line('acl_label_security_policy')), 'pepiscms/theme/img/acl/checkrights_32.png') ?>

<?= display_action_bar(array(array(
    'name' => $this->lang->line('global_button_back'),
    'link' => admin_url() . 'utilities',
    'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
))) ?>

<?= display_session_message() ?>

<div class="table_wrapper">
    <h4><?=$this->lang->line('acl_system_access_and_own_rights')?></h4>
    <table class="datagrid">
        <tr>
            <th class="super_long"><?=$this->lang->line('acl_action')?></th>
            <th class="long"><?=$this->lang->line('acl_access_granted')?></th>
            <th><?=$this->lang->line('acl_required_rights')?></th>
        </tr>
        <?php foreach ($controllers as $controller): ?>

            <tr>
                <td colspan="7" class="entity_group"><?= $controller->name ?></td>
            </tr>

            <?php foreach ($controller->methods as $method): $has_access = SecurityManager::hasAccess($controller->name, $method->name); ?>
                <tr class="<?= ($has_access ? 'green' : 'red')?>">
                    <td><?= $method->name ?></td>
                    <td><?= ( $has_access ? $this->lang->line('global_dialog_yes') : $this->lang->line('global_dialog_no')) ?></td>
                    <td><?php $rights = SecurityManager::getRequiredAccessRight($controller->name, $method->name); ?>
                        <?php if($rights['entity']): ?><?=implode(':', $rights) ?><?php else: ?>-<?php endif; ?></td>
                </tr>
            <?php endforeach; ?>

    <?php endforeach; ?>
    </table>
</div>

<div class="table_wrapper">
    <h4><?=$this->lang->line('acl_module_access_and_own_rights')?></h4>
    <table class="datagrid">
        <thead>
        <th class="super_long"><?=$this->lang->line('acl_module')?></th>
        <th class="long"><?=$this->lang->line('acl_access_granted')?></th>
        <th><?=$this->lang->line('acl_required_rights')?></th>
        </thead>
        <tbody>
            <?php
            foreach (ModuleRunner::getAvailableModules() as $module):
                $has_access = SecurityManager::hasAccess($module, 'index', $module);
                if (!$this->Module_model->isAdminControllerRunnable($module))
                {
                    continue;
                }
                ?>
                <tr class="<?= ($has_access ? 'green' : 'red')?>">
                    <td class="optionsname"><?= $module ?></td>
                    <td><?= ( $has_access ? $this->lang->line('global_dialog_yes') : $this->lang->line('global_dialog_no')) ?></td>
                <?php $access = SecurityManager::getRequiredAccessRight($module, 'index', $module); ?>
                    <td>
                        <?php if($access['entity']): ?><?= $access['entity'] ?>:<?= $access['min_access'] ?><?php else: ?>-<?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>