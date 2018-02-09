<div style="width: 500px; margin-left: auto; margin-right: auto; margin-toP: 100px;">
    <?= display_error($message); ?>
    <?php if ($explanation): ?>
        <p><?= $explanation ?></p>
    <?php endif; ?>
    <form action="" method="POST" style="margin-top: 20px;">
        <input type="hidden" name="confirm" value="1">
        <div class="lFloated"><?= button_cancel() ?></div>
    </form>
</div>