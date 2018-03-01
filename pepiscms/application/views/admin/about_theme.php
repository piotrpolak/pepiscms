<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?= display_breadcrumb(array(admin_url() . 'about' => $this->lang->line('global_about_pepiscms'), admin_url() . 'about/theme' => $this->lang->line('about_label_theme_preview')), 'pepiscms/theme/img/about/about_32.png') ?>

    <?php
    $actions = array(
        array(
            'name' => $this->lang->line('global_button_back'),
            'link' => admin_url() . 'utilities',
            'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png',
        ),
        array(
            'name' => $this->lang->line('global_about_pepiscms'),
            'link' => admin_url() . 'about',
            'icon' => 'pepiscms/theme/img/dialog/actions/action_16.png',
        ),
        array(
            'name' => $this->lang->line('about_label_theme_preview'),
            'link' => admin_url() . 'about/theme',
            'icon' => 'pepiscms/theme/img/dialog/actions/action_16.png',
        ),
    );
    ?>
    <?= display_action_bar($actions) ?>

    <?= display_session_message() ?>

    <?php
    // TODO Redo using helpers
    $actions = array(
        array(
            'name' => 'Add folder',
            'link' => '#',
            'icon' => 'pepiscms/theme/img/ajaxfilemanager/folder_add_16.png',
        ),
        array(
            'name' => 'Add new',
            'link' => '#',
            'icon' => 'pepiscms/theme/img/dialog/actions/add_16.png',
        ),
        array(
            'name' => 'Install',
            'link' => '#',
            'icon' => 'pepiscms/theme/img/module/install_16.png',
        ),
        array(
            'name' => 'No image element',
            'link' => '#',
            'icon' => '',
        ),
            )
    ?>
    <?= display_action_bar($actions) ?>

    <h3 style="margin-top: 1.5em;">Buttons</h3>
    <?= button_save() ?>
    <?= button_apply() ?>
    <?= button_cancel() ?>
    <?= button_back() ?>
    <?= button_next() ?>
    <?= button_previous() ?>

    <?php // TODO Add all possible buttons ?>

    <h1>Heading 1 - error pages</h1>
    
    <p>There are two types of error pages: <a href="admin/about/theme_404">404 error page</a> and <a href="admin/about/theme_error">generic error page</a></p>
    <h2>Heading 2</h2>
    <h3>Heading 3</h3>


    <h1 class="contrasted">Heading 1 - high contrast</h1>

    <p>
        Lorem Ipsum is <a href="#">simply dummy text</a> of the printing and typesetting industry.
        Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
        when an unknown printer took a galley of type and scrambled it to make a type specimen book.
        It has survived not only five centuries, but also the leap into electronic typesetting,
        remaining essentially unchanged. It was popularised in the 1960s with the release of
        Letraset sheets containing Lorem Ipsum passages, and more recently with
        desktop publishing software like Aldus
        PageMaker including versions of Lorem Ipsum
    </p>
    <h3>Unordered list</h3>
    <ul>
        <li>Lorem Ipsum</li>
        <li>Ipsum passages</li>
        <li>Scrambled it to make a type specimen book</li>
    </ul>
    <h3>Ordered list</h3>
    <ol>
        <li>Lorem Ipsum</li>
        <li>Ipsum passages</li>
        <li>Scrambled it to make a type specimen book</li>
    </ol>
    <h3>Popup</h3>
    <p>Press <a href="<?= admin_url() ?>about/theme" class="popup" title="Popup window">here to open popup</a> window containing the same page.</p>

    <h3>Messages</h3>
    <?= display_error('Error: PageMaker including versions of Lorem Ipsum!') ?>
    <?= display_warning('Warning: PageMaker including versions of Lorem Ipsum!') ?>
    <?= display_notification('Notification: Something less important than warning but persistant (not like success).') ?>
    <?= display_success('Success: This message will disappear in some seconds!') ?>
    <?= display_tip('Tip: PageMaker including versions of Lorem Ipsum!') ?>

    <h3>Pie chart</h3>
    <?=$this->google_chart_helper->drawSimplePieChart(array('PHP' => 30, 'C#' => 25, 'JavaEE' => 55, 'C++' => 72, 'JavaScript' => 10), 'Pie chart', 300, 200)?>
    <h3>Line chart</h3>
    <?=$this->google_chart_helper->drawSimpleLineChart(array('2010' => 75, '2011' => 95, '2012' => 90, '2013' => 87, '2014' => 95, '2015' => 120), 'Year', 'Weight')?>
    <h3>Multiline chart</h3>
    <?=$this->google_chart_helper->drawSimpleLineChart(array('2010' => array(75, 85), '2011' => array(95, 105), '2012' => array(90, 100), '2013' => array(87, 97), '2014' => array(95, 105), '2015' => array(120, 130)), 'Year', array('Weight A', 'Weight B'))?>
    <h3>Column chart</h3>
    <?=$this->google_chart_helper->drawSimpleColumnChart(array('2010' => 75, '2011' => 95, '2012' => 90, '2013' => 87, '2014' => 95, '2015' => 120), 'Year', 'Weight')?>
    <h3>Multicolumn chart</h3>
    <?=$this->google_chart_helper->drawSimpleColumnChart(array('2010' => array(75, 85), '2011' => array(95, 105), '2012' => array(90, 100), '2013' => array(87, 97), '2014' => array(95, 105), '2015' => array(120, 130)), 'Year', array('Weight A', 'Weight B'))?>


    <?php
    $steps = array(
        array(
            'name' => 'One',
            'description' => 'Dolor sit amet.',
            'active' => FALSE,
        ),
        array(
            'name' => 'Two',
            'description' => 'Ut enim ad minim veniam.',
            'active' => FALSE,
        ),
        array(
            'name' => 'Three',
            'description' => 'Anim id est laborum.',
            'active' => FALSE,
        ),
        array(
            'name' => 'Four',
            'description' => 'Anim id est laborum.',
            'active' => FALSE,
        ),
        array(
            'name' => 'Five',
            'description' => 'Eenim ad minim veniam mon.',
            'active' => TRUE,
        ),
    );
    ?>

    <h3 style="margin-top: 1.5em;">Circle Steps</h3>
    <?= display_steps_circle($steps) ?>

    <h3 style="margin-top: 1.5em;">Dot Steps</h3>
    <?= display_steps_dot($steps) ?>




    <?=$formbuilder?>

    <h3 style="margin-top: 1.5em;">Folder tree view</h3>
    <nav class="file_tree">
        <a href="#">Root</a>
        <ul class="level-1">
            <li><a href="#" class="folder">bin</a></li>
            <li class="has_items">
                <a href="#" class="folder">sbin</a>
                <ul class="level-2">
                    <li><a href="#">apache</a></li>
                    <li><a href="#">clear</a></li>
                    <li><a href="#">ls</a></li>
                </ul>	
            </li>
            <li><a href="#" class="folder">proc</a></li>
            <li><a href="#" class="folder">usr</a></li>
        </ul>

    </nav>



<?php else: ?>
    <?php
    $actions = array(
        array(
            'name' => 'Close',
            'class' => 'cancel',
            'link' => '#',
            'icon' => 'pepiscms/theme/back_16.png',
        ),
            )
    ?>
    <?= display_action_bar($actions) ?>
    <h2>Popup widnow</h2>
    <p>Popup window should be able to change it's title by itself</p>

<?php endif; ?>

<script type="text/javascript">
    ppLib_2.applyOvTitle('.overlay_window', 'Siała baba mak...', 'pepiscms/theme/img/ajaxfilemanager/folder_16.png');
</script>