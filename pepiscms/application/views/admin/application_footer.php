        <div class="clear"></div>
    </div>
</div>

<?php if (!$popup_layout): ?>
    <?php if ($this->auth->isAuthenticated()): ?>
        <footer>
            <p class="rFloated"><span><?php if ($this->config->item('cms_customization_support_line')): ?><?= $this->config->item('cms_customization_support_line') ?><?php endif; ?><?php if ($this->config->item('cms_customization_support_link')): ?> <a href="<?= $this->config->item('cms_customization_support_link') ?>" title="Help">Help</a><?php endif; ?></span></p>
            <p>PepisCMS <?= PEPISCMS_VERSION ?>
                <span class="separable">
                    <a href="<?= admin_url() ?>about"><?= $lang->line('about_the_system') ?></a>

                    <?php if (SecurityManager::hasAccess('logs', 'mylogin', 'logs') && ModuleRunner::isModuleInstalled('logs')): ?>	
                        <a href="<?= module_url('logs') ?>mylogin"><?= $lang->line('global_logs_view_own_login_history') ?></a>
                    <?php endif; ?>

                </span>
                <?php if (!isset($security_policy_violation) || !$security_policy_violation): ?> Page generated in <?= $this->benchmark->elapsed_time(); ?> seconds.<?php endif; ?>

            </p>
        </footer>
    <?php endif; ?>

    </div>

<?php endif; ?>
    <?php $html_customization_prefix = 'html_customization_'.($this->auth->isAuthenticated() ? 'logged_in' : 'not_logged_in'); ?>
    <?=$this->config->item($html_customization_prefix.'_body_append')?>

    <div id="heavy_operation_indicator">
        <img src="pepiscms/theme/img/popup/loader_32.gif" alt="">
        <p><?= $this->lang->line('global_heavy_operation_in_progress') ?></p>
    </div>


    <script src="pepiscms/3rdparty/lazyload/lazyload.min.js?v=<?= PEPISCMS_VERSION ?>"></script>
    <script>window.lazyload = new LazyLoad();</script>
</body>
</html>
