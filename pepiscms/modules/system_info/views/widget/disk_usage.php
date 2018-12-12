<?= $this->google_chart_helper->drawSimplePieChart(array(
    $this->lang->line('system_info_free') => round($free_space / 1024 / 1024, 2),
    $this->lang->line('system_info_occupied') => round($occupied_space / 1024 / 1024, 2)
), $this->lang->line('system_info_usage_in_megabytes'), 300, 200) ?>
