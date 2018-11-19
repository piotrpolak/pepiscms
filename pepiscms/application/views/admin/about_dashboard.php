<?= display_breadcrumb(array(admin_url() . 'about/dashboard' => $this->lang->line('dashboard_welcome_to') . ' ' . $this->config->item('site_name')), 'pepiscms/theme/img/about/dashboard_32.png') ?>
<?= display_session_message() ?>

<?php if(count($widgets)): ?>
<div id="two_pane_layout">
    <div class="left_option_pane">
            <?php foreach($widgets as $widget): if(!$widget['show_in_side_pane']) continue?>
                <h1 class="contrasted"><?= $widget['label'] ?></h1>
                <?=$widget['widget_html']?>
            <?php endforeach ?>
    </div>

    <div class="right_content_pane">
<?php endif; ?>
        <?php if( count($dashboard_elements_grouped)): ?>
            <?php foreach($dashboard_elements_grouped as $group => $dashboard_elements): ?>
                <h1 class="contrasted"><?= $lang->line($group) ?></h1>
                <ul class="dashboard_actions">
                    <?php foreach($dashboard_elements as $dashboard_element): ?>
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
                </ul>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if(count($failed_configuration_tests)): ?>
            <h1 class="contrasted"><?= $lang->line('dashboard_system_status') ?></h1>
            <?php foreach( $failed_configuration_tests as $test_name ): ?>
                <?= display_warning($lang->line('dashboard_test_'.$test_name)) ?>
            <?php endforeach ?>
        <?php endif; ?>

<?php if(count($widgets)): ?>
    </div>
</div>
<?php endif; ?>