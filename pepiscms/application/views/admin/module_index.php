<?= display_breadcrumb(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings'), admin_url() . 'module' => $this->lang->line('modules_installed_modules')), 'pepiscms/theme/img/module/module_32.png') ?>

<?= display_session_message() ?>

<?php
    $actions = array();

    $actions[] = array(
        'name' => $this->lang->line('global_button_back_to_utilities'),
        'link' => admin_url() . 'utilities',
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
    );
?>
<?php if (SecurityManager::hasAccess('module', 'setup')): ?>
    <?php
    $actions[] = array(
        'name' => $this->lang->line('modules_install_a_new_module'),
        'link' => admin_url() . 'module/setup/view-'.$view,
        'icon' => 'pepiscms/theme/img/module/install_16.png',
    );
    ?>
<?php endif; ?>

<?= display_action_bar($actions) ?>

<div class="rFloated view_selector utilities_view_selector"><?= $lang->line('modules_view') ?>
    <a href="<?=admin_url()?>module/index/view-menu"<?=($view=='menu'?' class="active"':'')?>><?= $lang->line('modules_view_by_menu') ?></a>
    <a href="<?=admin_url()?>module/index/view-utilities"<?=($view=='utilities'?' class="active"':'')?>><?= $lang->line('modules_view_by_utilities') ?></a>
</div>
<div class="clear"></div>

<?php if( $view == 'utilities' ): ?>
    <?php if (count($installed_modules_in_utilities)): ?>
        <div class="table_wrapper">
            <h4><?= $this->lang->line('modules_installed_modules') ?></h4>
            <table class="datagrid">
                <?php foreach ($installed_modules_in_utilities as $module): ?>
                    <tr>
                        <td>
                            <?php if ($this->Module_model->isAdminControllerRunnable($module->name) && SecurityManager::hasAccess($module->name, 'index', $module->name)): ?>
                                <a href="<?= admin_url() ?>module/run/<?= $module->name ?>" title="<?= $this->Module_model->getModuleDescription($module->name, $this->lang->getCurrentLanguage()) ?>"><img src="<?= module_icon_url($module->name) ?>" alt="icon"></a> <b><a href="<?= admin_url() ?>module/run/<?= $module->name ?>" title="<?= $this->Module_model->getModuleDescription($module->name, $this->lang->getCurrentLanguage()) ?>"><?= $this->Module_model->getModuleLabel($module->name, $this->lang->getCurrentLanguage()) ?></a></b>
                            <?php else: ?>
                                <img src="<?= module_icon_url($module->name) ?>" alt="icon"> <b><?= $this->Module_model->getModuleLabel($module->name, $this->lang->getCurrentLanguage()) ?></b>
                            <?php endif; ?>
                        </td>
                        <?php if (SecurityManager::hasAccess('module', 'move')): ?>
                            <td class="medium">
                                <?php if ($module->is_displayed_in_utilities): ?>
                                    <a href="<?= admin_url() ?>module/move/direction-up/module-<?= $module->name ?>/view-<?=$view?>"><img src="pepiscms/theme/img/dialog/datagrid/up_16.png" alt="up"></a>
                                    <a href="<?= admin_url() ?>module/move/direction-down/module-<?= $module->name ?>/view-<?=$view?>"><img src="pepiscms/theme/img/dialog/datagrid/down_16.png" alt="down"></a>
                                <?php endif; ?></td>
                        <?php endif; ?>

                        <?php if (SecurityManager::hasAccess('module', 'do_setup')): ?>
                            <td class="narrow"><a href="<?= admin_url() ?>module/do_setup/module-<?= $module->name ?>/view-<?=$view?>" title="<?= $lang->line('modules_setup_module') ?>"><img src="pepiscms/theme/img/module/module_setup_16.png" alt="icon"></a></td>
                        <?php endif; ?>

                        <?php if (SecurityManager::hasAccess('module', 'uninstall')): ?>
                            <td class="narrow"><a href="<?= admin_url() ?>module/uninstall/<?= $module->name ?>/view-<?=$view?>" class="ask_for_confirmation" title="<?= $lang->line('modules_uninstall_module') ?>"><img src="pepiscms/theme/img/module/uninstall_16.png" alt="icon"></a></td>
                        <?php endif; ?>
                    </tr>

                 <?php endforeach ?>
            </table>
        </div>
    <?php else: ?>
        <?= display_notification($lang->line('modules_there_are_no_installed_modules')) ?>
    <?php endif; ?>
<?php elseif( $view == 'menu' ): ?>
    <?php if (count($installed_modules_with_no_parent)): ?>
        <div class="table_wrapper">
            <h4><?= $this->lang->line('modules_installed_modules') ?></h4>
            <table class="datagrid">
                <?php foreach ($installed_modules_with_no_parent as $module): ?>
                    <tr>
                        <td>
                            <?php if ($this->Module_model->isAdminControllerRunnable($module->name) && SecurityManager::hasAccess($module->name, 'index', $module->name)): ?>
                                <a href="<?= admin_url() ?>module/run/<?= $module->name ?>" title="<?= $this->Module_model->getModuleDescription($module->name, $this->lang->getCurrentLanguage()) ?>"><img src="<?= module_icon_url($module->name) ?>" alt="icon"></a> <b><a href="<?= admin_url() ?>module/run/<?= $module->name ?>" title="<?= $this->Module_model->getModuleDescription($module->name, $this->lang->getCurrentLanguage()) ?>"><?= $this->Module_model->getModuleLabel($module->name, $this->lang->getCurrentLanguage()) ?></a></b>
                            <?php else: ?>
                                <img src="<?= module_icon_url($module->name) ?>" alt="icon"> <b><?= $this->Module_model->getModuleLabel($module->name, $this->lang->getCurrentLanguage()) ?></b>
                            <?php endif; ?>
                        </td>
                        <?php if (SecurityManager::hasAccess('module', 'move')): ?>
                            <td class="medium">
                                <?php if ($module->is_displayed_in_menu): ?>
                                <a href="<?= admin_url() ?>module/move/direction-up/module-<?= $module->name ?>/view-<?=$view?>"><img src="pepiscms/theme/img/dialog/datagrid/up_16.png" alt="up"></a>
                                <a href="<?= admin_url() ?>module/move/direction-down/module-<?= $module->name ?>/view-<?=$view?>"><img src="pepiscms/theme/img/dialog/datagrid/down_16.png" alt="down"></a>
                                <?php endif; ?></td>
                        <?php endif; ?>

                        <?php if (SecurityManager::hasAccess('module', 'do_setup')): ?>
                            <td class="narrow"><a href="<?= admin_url() ?>module/do_setup/module-<?= $module->name ?>/view-<?=$view?>" title="<?= $lang->line('modules_setup_module') ?>"><img src="pepiscms/theme/img/module/module_setup_16.png" alt="icon"></a></td>
                        <?php endif; ?>

                        <?php if (SecurityManager::hasAccess('module', 'uninstall')): ?>
                            <td class="narrow"><a href="<?= admin_url() ?>module/uninstall/<?= $module->name ?>/view-<?=$view?>" class="ask_for_confirmation" title="<?= $lang->line('modules_uninstall_module') ?>"><img src="pepiscms/theme/img/module/uninstall_16.png" alt="icon"></a></td>
                        <?php endif; ?>
                    </tr>

                    <?php if( isset($installed_modules_with_parrent_grouped_by_parent[$module->module_id]) ): ?>
                        <?php foreach ($installed_modules_with_parrent_grouped_by_parent[$module->module_id] as $sub_module): ?>
                        <tr>
                            <td style="padding-left: 60px;">
                                <?php if ($this->Module_model->isAdminControllerRunnable($sub_module->name) && SecurityManager::hasAccess($sub_module->name, 'index', $sub_module->name)): ?>
                                    <a href="<?= admin_url() ?>module/run/<?= $sub_module->name ?>" title="<?= $this->Module_model->getModuleDescription($sub_module->name, $this->lang->getCurrentLanguage()) ?>"><img src="<?= module_icon_url($sub_module->name) ?>" alt="icon"></a> <b><a href="<?= admin_url() ?>module/run/<?= $sub_module->name ?>" title="<?= $this->Module_model->getModuleDescription($sub_module->name, $this->lang->getCurrentLanguage()) ?>"><?= $this->Module_model->getModuleLabel($sub_module->name, $this->lang->getCurrentLanguage()) ?></a></b>
                                <?php else: ?>
                                    <img src="<?= module_icon_url($sub_module->name) ?>" alt="icon"> <b><?= $this->Module_model->getModuleLabel($sub_module->name, $this->lang->getCurrentLanguage()) ?></b>
                                <?php endif; ?>
                            </td>
                            <td class="medium">
                            <?php if (FALSE && SecurityManager::hasAccess('module', 'move')): ?>
                                <?php if ($sub_module->is_displayed_in_menu): ?>
                                <a href="<?= admin_url() ?>module/move/direction-up/module-<?= $sub_module->name ?>/view-<?=$view?>"><img src="pepiscms/theme/up.png" alt="up"></a>
                                <a href="<?= admin_url() ?>module/move/direction-down/module-<?= $sub_module->name ?>/view-<?=$view?>"><img src="pepiscms/theme/down.png" alt="down"></a>
                                <?php endif; ?>
                            <?php endif; ?>
                            </td>

                            <?php if (SecurityManager::hasAccess('module', 'do_setup')): ?>
                                <td class="narrow">
                                    <a href="<?= admin_url() ?>module/do_setup/module-<?= $sub_module->name ?>/view-<?=$view?>" title="<?= $lang->line('modules_setup_module') ?>"><img src="pepiscms/theme/img/module/module_setup_16.png" alt="icon"></a>
                                </td>
                            <?php endif; ?>

                            <?php if (SecurityManager::hasAccess('module', 'uninstall')): ?>
                                <td class="narrow"><a href="<?= admin_url() ?>module/uninstall/<?= $sub_module->name ?>/view-<?=$view?>" class="ask_for_confirmation" title="<?= $lang->line('modules_uninstall_module') ?>"><img src="pepiscms/theme/img/module/uninstall_16.png" alt="icon"></a></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>

                <?php endforeach; ?>
            </table>
        </div>
    <?php else: ?>
        <?= display_notification($lang->line('modules_there_are_no_installed_modules')) ?>
    <?php endif; ?>
<?php endif; ?>