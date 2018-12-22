<?=$this->google_chart_helper->drawSimpleLineChart($values, $this->lang->line('logs_timestamp'), $this->lang->line('logs_issues'),
    '100%', 200, 50, 'date', 'number', array('red'));?>

<?php if(SecurityManager::hasAccess('logs', 'index', 'logs')): ?>
<p class="center">
    <a href="<?=module_url('logs')?>">&raquo; <?=$this->lang->line('logs_view_all')?></a>
</p>
<?php endif; ?>