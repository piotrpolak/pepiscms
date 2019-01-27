<?php $is_utilities_only_module = false; ?>
<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php
    $breadcrumb_array = array(module_url() => $title);

    if( ModuleRunner::isModuleDisplayedInMenu() )
    {
        $parent_module_name = ModuleRunner::getParentModuleName();
        if( $parent_module_name )
        {
            $breadcrumb_array = array_merge(array(module_url($parent_module_name) => $this->Module_model->getModuleLabel($parent_module_name, $this->lang->getCurrentLanguage())), $breadcrumb_array);
        }
    }
    else
    {
        // If module is displayed in UTILITIES and not in MENU then display a back link
        if( ModuleRunner::isModuleDisplayedInUtilities($this->modulerunner->getRunningModuleName()) )
        {
            $is_utilities_only_module = true;
            $breadcrumb_array = array_merge(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings')), $breadcrumb_array);
        }
    }
    ?>

    <?= display_breadcrumb($breadcrumb_array, module_icon_url()) ?>
<?php endif; ?>

<?php
$actions = array();
if( $is_utilities_only_module )
{
    $actions[] = array(
        'name' => $this->lang->line('global_button_back_to_utilities'),
        'link' => admin_url() . 'utilities',
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png'
    );
}
?>
<?php if(count($actions)): ?>
    <?= display_action_bar($actions) ?>
<?php endif; ?>

<?= display_session_message() ?>

<div class="table_wrapper">
    <h4><?= $this->lang->line('system_info_module_name') ?></h4>
    <table class="datagrid">

        <?php if ($current_user): ?>
            <tr>
                <td class="optionsname"><?=$this->lang->line('system_info_apache_user')?></td>
                <td><?= $current_user ?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_owner_of_installation_path')?></td>
            <td><?= $owner ?></td>
        </tr>
        <?php if (function_exists('apache_get_version')): ?>
            <tr>
                <td class="optionsname"><?=$this->lang->line('system_info_apache_version')?></td>
                <td><?= apache_get_version() ?></td>
            </tr>
        <?php endif; ?>
        <?php if (function_exists('apache_get_modules')): ?>
            <tr>
                <td class="optionsname"><?=$this->lang->line('system_info_apache_loaded_modules')?></td>
                <td><?= implode(', ', apache_get_modules()) ?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_php_version')?></td>
            <td><?= phpversion() ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_php_loaded_extensions')?></td>
            <td><?= implode(', ', get_loaded_extensions()) ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_peak_memory_usage_for_this_request')?></td>
            <td><?= byte_format(memory_get_peak_usage()) ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_mysql_version')?></td>
            <td><?= $this->db->version() ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_database_connection_info')?></td>
            <td>Host: <b><?= $database_config['hostname'] ?></b> / User: <b><?= $database_config['username'] ?></b> / Database: <b><?= $database_config['database'] ?></b></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_system_path')?></td>
            <td><?= realpath(dirname(BASEPATH)) ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_pepiscms_version')?></td>
            <td><?= PEPISCMS_VERSION ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_code_igniter_varsion')?></td>
            <td><?= CI_VERSION ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_system_production_ready')?></td>
            <td><?= (PEPISCMS_PRODUCTION_RELEASE ? $this->lang->line('global_dialog_yes') : $this->lang->line('global_dialog_no')) ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_installation_path')?></td>
            <td><?= realpath(INSTALLATIONPATH) ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_installation_path_writeable')?></td>
            <td><?= (is_writeable(INSTALLATIONPATH) ? $this->lang->line('global_dialog_yes') : $this->lang->line('global_dialog_no')) ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_pages_cache')?></td>
            <td><?= ($this->config->item('cache_expires') > 0 ? $this->config->item('cache_expires') . ' '.$this->lang->line('system_info_minutes') : $this->lang->line('system_info_not_enabled')) ?></td>
        </tr>
        <?php
        $this->load->helper(array('file', 'number'));
        $filesize_all = $this->System_info_model->getOccupiedSpace();
        ?>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_total_occupied_space')?></td>
            <td><?= byte_format($filesize_all) ?> (<?= $filesize_all ?> bytes)</td>
        </tr>

        <?php
        $free_space = $this->System_info_model->getFreeSpace()
        ?>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_disk_free_space')?></td>
            <td><?= byte_format($free_space) ?> (<?= $free_space ?> bytes)</td>
        </tr>

        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_disk_usage')?></td>
            <td><?= round($filesize_all/($filesize_all+$free_space)*100)?>%</td>
        </tr>

        <?php
        $quota = $this->config->item('system_info_max_quota_in_mb');
        ?>

        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_quota')?></td>
            <td><?= $quota ? byte_format($quota * 1024 * 1024) : 'N/A'?></td>
        </tr>


        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_quota_usage')?></td>
            <td><?= ($quota ? round($filesize_all/($quota*1024*1024)*100).'%' : 'N/A')?> </td>
        </tr>

        <?php
        $this->load->helper(array('file', 'number'));
        $files = get_filenames(INSTALLATIONPATH . 'application/cache', true);
        $filesize_all = 0;
        foreach ($files as $file)
        {
            $filesize_all += filesize($file);
        }
        ?>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_cache_file_size')?></td>
            <td><?= byte_format($filesize_all) ?> (<?= $filesize_all ?> bytes)</td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_file_manager_allowed_upload_extensions')?></td>
            <td><?= str_replace('|', ', ', $this->config->item('upload_allowed_types')) ?></td>
        </tr>

        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_is_email_smtp_enabled')?></td>
            <td><?= ($this->config->item('email_use_smtp') ? $this->lang->line('global_dialog_yes') : $this->lang->line('global_dialog_no')) ?></td>
        </tr>

        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_upload_maximum_size')?></td>
            <td><?= ini_get('upload_max_filesize') ?></td>
        </tr>

        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_post_maximum_size')?></td>
            <td><?= ini_get('post_max_size') ?></td>
        </tr>

        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_time_on_server_local')?></td>
            <td><?= date('Y-m-d H:i:s') ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_php_include_path')?></td>
            <td><?= get_include_path() ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_php_open_basedir')?></td>
            <td><?= ini_get('open_basedir') ?></td>
        </tr>
        <tr>
            <td class="optionsname"><?=$this->lang->line('system_info_php_disabled_functions')?></td>
            <td><?= implode(', ', explode(',', ini_get('disable_functions'))) ?></td>
        </tr>
    </table>
</div>