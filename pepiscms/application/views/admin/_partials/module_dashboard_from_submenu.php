<ul class="dashboard_actions clear">
    <?php foreach($this->Module_model->getModuleAdminSubmenuElements($this->modulerunner->getRunningModuleName(), 'dontcare') as $descriptor): ?>
        <?= dashboard_box($descriptor['label'], module_url($descriptor['controller']) . $descriptor['method'], str_replace('_16.', '_32.', $descriptor['icon_url']), $descriptor['description']) ?>
    <?php endforeach ?>
</ul>