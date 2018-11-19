<?= $this->google_chart_helper->drawSimplePieChart(array(
    'free' => round($free_space / 1024 / 1024, 2),
    'occupied' => round($occupied_space / 1024 / 1024, 2)
), 'Usage in megabytes', 300, 200) ?>
