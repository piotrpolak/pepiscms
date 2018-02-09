<?php include($this->load->resolveModuleDirectory('crud') . 'views/admin/_edit_top.php'); ?>

<?= $form ?>

<?php if (isset($datagrid)): ?>
    <?= $datagrid ?>
<?php endif; ?>

<?php if ($id && $related_module_name && $related_module_filter_name): ?>
    <style>
        iframe.autoresize {
            width: 100%; border: none; overflow: hidden;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function () {

            var handle = $('iframe.autoresize');
            handle.load(function () {
                doAutoresize();
            });

            function doAutoresize() {
                var is_top_most = (top == self);
                var scroll_top = 0;
                if (is_top_most) {
                    scroll_top = $(top).scrollTop();
                }


                var current_height = handle.height();
                var desired_height = handle.contents().find('body').height() + 40;

                if (desired_height < current_height) {
                    if (is_top_most) {
                        $(top).animate({'scrollTop': scroll_top}, 300);
                    }
                    handle.animate({'height': desired_height}, 300);
                }
                else {
                    handle.height(desired_height);
                }
            }

            setInterval(doAutoresize, 400);
        });
    </script>

    <iframe src="<?= module_url($related_module_name) . 'index/layout-popup/forced_filters-' . DataGrid::encodeFiltersString(array($related_module_filter_name => $id)) ?>" class="autoresize"></iframe>
<?php endif; ?>