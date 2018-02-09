<?php $this->load->helper('form'); ?>

<?= display_breadcrumb(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings'), admin_url() . 'acl' => $this->lang->line('acl_label_security_policy')), 'pepiscms/theme/img/acl/acl_32.png') ?>

<?= display_action_bar(array(array(
    'name' => $this->lang->line('global_button_back'),
    'link' => admin_url() . 'utilities',
    'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
))) ?>

<?= display_session_message() ?>

<?= display_tip($this->lang->line('acl_index_tip')); ?>

<div class="table_wrapper">
    <table class="datagrid">
        <tr>
            <td colspan="1" class="entity_group"><?= $this->lang->line('acl_system') ?></td>
        </tr>
        <tr>
            <td>
                <a href="<?= admin_url() ?>acl/edit/section-system"><img src="pepiscms/theme/img/acl/acl_32.png" alt=""></a>
                <b><a href="<?= admin_url() ?>acl/edit/section-system"><?= $this->lang->line('acl_core_policy') ?></a></b>
            </td>
        </tr>

        <?php if (count($installed_modules)): ?>

            <?php foreach( $installed_modules as $modules_group_name => $modules): ?>

            <tr>
                <td colspan="1" class="entity_group"><?=$this->lang->line('acl_'.$modules_group_name)?></td>
            </tr>
                <?php foreach ($modules as $module_name): ?>
                    <tr>
                        <td>
                            <a href="<?= admin_url() ?>acl/edit/section-<?= $module_name ?>"
                               title="<?= $this->Module_model->getModuleDescription($module_name, $this->lang->getCurrentLanguage()) ?>"><img
                                    src="<?= module_icon_url($module_name) ?>" alt="icon"></a> <b><a
                                    href="<?= admin_url() ?>acl/edit/section-<?= $module_name ?>"
                                    title="<?= $this->Module_model->getModuleDescription($module_name, $this->lang->getCurrentLanguage()) ?>"><?= $this->Module_model->getModuleLabel($module_name, $this->lang->getCurrentLanguage()) ?></a></b>
                            <?php if (!SecurityPolicy::existsModulePolicy($module_name)): ?>
                                <?= $this->lang->line('acl_security_policy_not_defined') ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>

        <?php endif; ?>
    </table>
</div>