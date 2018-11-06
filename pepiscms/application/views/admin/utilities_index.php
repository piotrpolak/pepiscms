<?= display_breadcrumb(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings')), 'pepiscms/theme/img/utilities/utilities_32.png') ?>

<?= display_session_message() ?>


<div id="one_pane_utilities_layout">	

    <?php $this->load->library('ModuleRunner');
    $modules_in_utilities = ModuleRunner::getInstalledModulesNamesDisplayedInUtilitiesCached(); ?>

<?php if (SecurityManager::hasAccess('siteconfig') || SecurityManager::hasAccess('module') || SecurityManager::hasAccess('utilities', 'clean_cache') || SecurityManager::hasAccess('sitelanguages')): ?>
    <h1 class="contrasted"><?= $lang->line('label_common_utilities') ?></h1>
    <ul class="dashboard_actions clear">

        <?php if (SecurityManager::hasAccess('setup') && $this->config->item('feature_is_enabled_setup')): ?>
            <?= dashboard_box($lang->line('setup_module_name'), admin_url() . 'setup', 'pepiscms/theme/img/utilities/setup_32.png', $lang->line('utilities_label_siteconfig')) ?>
        <?php endif; ?>

        <?php if (SecurityManager::hasAccess('module')): ?>
            <?= dashboard_box($lang->line('modules_modules'), admin_url() . 'module', 'pepiscms/theme/img/module/module_32.png', $lang->line('utilities_modules_modules')) ?>
        <?php endif; ?>

        <?php if (SecurityManager::hasAccess('module')): ?>
            <?php foreach ($modules_in_utilities as $module_in_utilities): ?>
                <?php if (SecurityManager::hasAccess($module_in_utilities, 'index', $module_in_utilities)): ?>
                    <?= dashboard_box($this->Module_model->getModuleLabel($module_in_utilities, $this->lang->getCurrentLanguage()), admin_url() . 'module/run/' . $module_in_utilities, module_icon_url($module_in_utilities), $this->Module_model->getModuleDescription($module_in_utilities, $this->lang->getCurrentLanguage())) ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if(isset($dashboard_elements_grouped['common_utilities'])): ?>
            <?php foreach ($dashboard_elements_grouped['common_utilities'] as $dashboard_element): ?>
                <?=dashboard_box(
                    $dashboard_element['label'],
                    (isset($dashboard_element['url']) && $dashboard_element['url'] ? $dashboard_element['url'] :
                        ((isset($dashboard_element['module']) && $dashboard_element['module']) ? module_url($dashboard_element['controller']).$dashboard_element['method'] :
                            admin_url().$dashboard_element['controller'].'/'.$dashboard_element['method'])),
                    isset($dashboard_element['icon_url']) && $dashboard_element['icon_url'] ? $dashboard_element['icon_url'] : (isset($dashboard_element['controller']) ? module_icon_url($dashboard_element['controller']) : FALSE),
                    isset($dashboard_element['description']) ? $dashboard_element['description'] : FALSE,
                    isset($dashboard_element['is_popup']) ? $dashboard_element['is_popup'] : FALSE,
                    isset($dashboard_element['target']) ? $dashboard_element['target'] : FALSE
                )?>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
<?php endif; ?>


<?php if ((SecurityManager::hasAccess('utilities', 'flush_html_cache') || SecurityManager::hasAccess('utilities', 'flush_security_policy_cache') || SecurityManager::hasAccess('utilities', 'flush_system_cache') || SecurityManager::hasAccess('acl') || SecurityManager::hasAccess('logs') || SecurityManager::hasAccess('utilities', 'systeminfo'))): ?>

        <h1 class="contrasted"><?= $lang->line('utilities_label_cache') ?></h1>
        <ul class="dashboard_actions clear">

            <?php if (SecurityManager::hasAccess('utilities', 'flush_system_cache')): ?>
                <?= dashboard_box($lang->line('utilities_label_flush_system_cache'), admin_url() . 'utilities/flush_system_cache', 'pepiscms/theme/img/utilities/flush_32.png', $lang->line('utilities_label_flush_system_cache_desc')) ?>
            <?php endif; ?>

            <?php if (SecurityManager::hasAccess('utilities', 'flush_security_policy_cache')): ?>	
                <?= dashboard_box($lang->line('utilities_label_flush_security_policy_cache'), admin_url() . 'utilities/flush_security_policy_cache', 'pepiscms/theme/img/utilities/flush_security_policy_32.png', $lang->line('utilities_label_flush_security_policy_cache_desc')) ?>
            <?php endif; ?>

            <?= dashboard_box($lang->line('global_reload_privileges'), admin_url() . 'login/refresh_session', 'pepiscms/theme/img/utilities/flush_privileges_32.png') ?>
        </ul>

        <h1 class="contrasted"><?= $lang->line('label_system_utilities') ?></h1>
        <ul class="dashboard_actions clear">

            <?php if ($this->config->item('feature_is_enabled_acl') && SecurityManager::hasAccess('acl')): ?>
                <?= dashboard_box($lang->line('utilities_label_build_security_policy_for_securitymanager'), admin_url() . 'acl', 'pepiscms/theme/img/acl/acl_32.png', $lang->line('utilities_label_build_security_policy_for_securitymanager_desc')) ?>
            <?php endif; ?>

            <?php if ($this->config->item('feature_is_enabled_acl') && SecurityManager::hasAccess('acl', 'checkrights')): ?>
                <?= dashboard_box($lang->line('acl_label_security_policy_check_own_rights'), admin_url() . 'acl/checkrights', 'pepiscms/theme/img/acl/checkrights_32.png', $lang->line('acl_label_security_policy_check_own_rights')) ?>
            <?php endif; ?>

            <?php if (SecurityManager::hasAccess('about', 'configuration_tests')): ?>	
                <?= dashboard_box($lang->line('utilities_label_configuration_tests'), admin_url() . 'about/configuration_tests', 'pepiscms/theme/img/utilities/configuration_tests_32.png', $lang->line('utilities_label_configuration_tests_desc')) ?>
    <?php endif; ?>

        </ul>

<?php endif; ?>
</div>