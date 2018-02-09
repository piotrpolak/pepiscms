<?= display_breadcrumb(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings'), admin_url() . 'module' => $this->lang->line('label_installed_modules'), admin_url() . 'module/setup' => $this->lang->line('label_installed_modules')), 'pepiscms/theme/img/module/module_32.png') ?>

<?php
$actions = array();
$actions[] = array(
    'name' => $this->lang->line('label_show_installed_modules'),
    'link' => admin_url() . 'module/index/view-'.$view,
    'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
);
?>
<?= display_action_bar($actions) ?>

<?= display_session_message() ?>

<?php if (count($modules) > 0): ?>
    <div class="table_wrapper">
        <h4><?= $this->lang->line('label_installed_modules') ?></h4>
        <table class="datagrid">

            <?php foreach ($modules as $module): ?>
                <tr>
                    <td><img src="<?= module_icon_url($module) ?>" alt="icon" title="<?= $this->Module_model->getModuleDescription($module, $this->lang->getCurrentLanguage()) ?>"> <b title="<?= $this->Module_model->getModuleDescription($module, $this->lang->getCurrentLanguage()) ?>"><?= $this->Module_model->getModuleLabel($module, $this->lang->getCurrentLanguage()) ?></b></td>
                    <td class="link"><a href="<?= admin_url() ?>module/do_setup/module-<?= $module ?>/install-1/view-<?=$view?>" title="<?= $lang->line('modules_install') ?>"><img src="pepiscms/theme/img/module/install_16.png" alt="icon"></a></td>
                </tr>

            <?php endforeach; ?>
        </table>
    </div>
<?php else: ?>
    <p><?= $lang->line('label_there_are_no_modules_that_are_not_installed') ?></p>
<?php endif; ?>