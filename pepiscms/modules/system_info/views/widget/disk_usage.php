<?= $this->google_chart_helper->drawSimplePieChart(array(
    $this->lang->line('system_info_free') => round($free_space / 1024 / 1024, 2),
    $this->lang->line('system_info_occupied') => round($occupied_space / 1024 / 1024, 2)
), $this->lang->line('system_info_usage_in_megabytes'), 300, 200) ?>
<p class="center"><?=$this->lang->line('system_info_occupied')?>: <?=byte_format($occupied_space)?>,
    <?=$this->lang->line('system_info_free')?>: <?=byte_format($free_space)?><?php if(SecurityManager::hasAccess('system_info', 'index', 'system_info')): ?>,
    <a href="<?=module_url('system_info')?>">&raquo; <?=$this->lang->line('system__info_view_all_stats')?></a><?php endif; ?>
</p>
