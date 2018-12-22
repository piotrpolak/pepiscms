<?php include($this->load->resolveModuleDirectory('crud') . 'views/admin/_edit_top.php'); ?>

<?= $form ?>

<?php if (isset($datagrid)): ?>
    <?= $datagrid ?>
<?php endif; ?>