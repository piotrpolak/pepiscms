<!DOCTYPE html> 
<html dir="ltr" lang="en" class="<?php if ($popup_layout): ?>popup<?php else: ?>plain<?php endif; ?> <?php if ($this->input->getParam('direct') == 1): ?>direct<?php endif; ?>"> 
    <head>
        <?php $this->load->config('_html_customization'); $html_customization_prefix = 'html_customization_'.($this->auth->isAuthenticated() ? 'logged_in' : 'not_logged_in');
        echo $this->config->item($html_customization_prefix.'_head_prepend')?> 
        <base href="<?= base_url() ?>">
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="robots" content="noindex">

        <link rel="stylesheet" type="text/css" href="pepiscms/theme/css/layout.css?v=<?= PEPISCMS_VERSION ?>" media="screen"> 
        <link rel="stylesheet" type="text/css" href="pepiscms/theme/default/css/style.css?v=<?= PEPISCMS_VERSION ?>" media="screen">

        <?php /* <link rel="stylesheet" type="text/css" href="pepiscms/theme/grey/css/style.css?v=<?=PEPISCMS_VERSION?>" media="screen"> */ ?>

        <script src="pepiscms/3rdparty/jquery/jquery.min.js"></script>

        <link rel="stylesheet" type="text/css" href="pepiscms/3rdparty/colorbox/theme/colorbox.css" media="screen">
        <script src="pepiscms/3rdparty/colorbox/jquery.colorbox-min.js"></script>

        <!-- qTip2 -->
        <link rel="stylesheet" type="text/css" href="pepiscms/3rdparty/qtip2/jquery.qtip.min.css" media="screen">
        <script src="pepiscms/3rdparty/qtip2/jquery.qtip.min.js"></script>

        <link rel="stylesheet" href="pepiscms/3rdparty/jqueryvalidation/css/validationEngine.jquery.css?v=<?= PEPISCMS_VERSION ?>" type="text/css"/>
        <script src="pepiscms/3rdparty/jqueryvalidation/js/languages/jquery.validationEngine-<?= $this->lang->getAdminLanguageCode() ?>.js?v=<?= PEPISCMS_VERSION ?>"></script>
        <script src="pepiscms/3rdparty/jqueryvalidation/js/jquery.validationEngine.js?v=<?= PEPISCMS_VERSION ?>"></script>

        <script src="pepiscms/js/jquery.frontend.js?v=<?= PEPISCMS_VERSION ?>"></script>

        <script src="pepiscms/js/popup.js?v=<?= PEPISCMS_VERSION ?>"></script>
        <script src="pepiscms/js/formvalidation.js?v=<?= PEPISCMS_VERSION ?>"></script>

        <!--[if IE]>
                <link rel="stylesheet" type="text/css" href="pepiscms/theme/default/css/ie.css" media="screen">
        <![endif]-->

        <!--[if lt IE 9]>
                <script src="pepiscms/3rdparty/html5shiv/html5shiv.min.js?v=<?= PEPISCMS_VERSION ?>"></script>
        <![endif]-->

        <title><?php if (isset($title)): ?><?= $title ?> - <?php endif; ?><?= $site_name ?> - PepisCMS</title> 
        <?=$this->config->item($html_customization_prefix.'_head_append')?>
    </head>
    <body id="<?=$body_id?>" class="<?php if ($popup_layout): ?>popup<?php else: ?>plain<?php endif; ?> <?php if ($this->input->getParam('direct') == 1): ?>direct<?php endif; ?>">
        <?=$this->config->item($html_customization_prefix.'_body_prepend')?>
        <?php if (!$popup_layout): ?>
            <header>
                <div class="box_content">

                    <?php
                    $logoUrl = trim($this->config->item('cms_customization_site_public_url'));
                    if( !$logoUrl )
                    {
                        $logoUrl = base_url();
                    }
                    else
                    {
                        if( strpos($logoUrl, '//') == -1 )
                        {
                            $logoUrl = base_url().$logoUrl;
                        }
                    }
                    ?>


                    <a href="<?=$logoUrl?>" id="home_link" title="<?= $this->config->item('site_name') ?>">
                        <?php if ($this->config->item('cms_customization_logo')): ?>
                            <img src="<?= $this->config->item('cms_customization_logo') ?>" alt="<?= $this->config->item('site_name') ?>">
                        <?php else: ?>
                            <?= $this->config->item('site_name') ?>
                        <?php endif; ?>
                    </a>

                    <?php if ($this->auth->isAuthenticated()): ?>
                        <div id="optional_actions_bar">
                            <span id="isLogged"><?= $user ?></span>
                            <?php if (SecurityManager::hasAccess('changepassword') && $this->auth->getDriver()->isPasswordChangeSupported()): ?>	
                                <a href="<?= admin_url(false) ?>changepassword" title="<?= $this->lang->line('changepassword_desc') ?>"><?= $lang->line('changepassword_change_password') ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div id="font_resize_bar">
                        <ul>
                            <li><a data-fontsize="small" href="#" title="Set small size">Small</a></li>
                            <li><a data-fontsize="nornal" class="active" href="#" title="Set normal size">Medium</a></li>
                            <li><a data-fontsize="big" href="#" title="Set big size">Big</a></li>
                        </ul>
                    </div>

                    <div id="language_bar">
                        <ul>
                            <?php foreach ($application_languages as $l): ?>
                                <li><a id="<?= $l[3] ?>_language" <?= ($current_language == $l[0] ? 'class="active"' : '') ?> href="<?= admin_url(false) ?>language/set/<?= $l[0] ?>" title="<?= $l[1] ?>"><img src="pepiscms/theme/img/languages/<?= $l[2] ?>" alt="<?= $l[1] ?>"></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php if ($this->auth->isAuthenticated()): ?>
                        <a href="<?= admin_url(false) ?>logout" id="logout_link" class="hasIcon" title="<?= $this->lang->line('global_logout') ?> &raquo;">
                            <img src="pepiscms/theme/default/images/icons/logout_icon.png" alt="">
                            <?= $this->lang->line('global_logout') ?>
                        </a>
                    <?php endif; ?>
                </div>
            </header>
        <?php endif; ?>

        <?php if ($this->auth->isAuthenticated() && !$popup_layout): ?>
            <div id="session_counter" style="display: none;">
                <a href="#" class="help_icon" data-hint="<?= $this->lang->line('session_expires_description') ?>"><?= $this->lang->line('session_expires_help') ?> &raquo;</a>
                <p><?= $this->lang->line('session_expires_in_min') ?> <span class="left">{min}</span>. <a href="<?= admin_url() ?>" class="refresh"><?= $this->lang->line('session_expires_refresh') ?></a></p>
            </div>

            <script>
                var sessionNowUTC = <?= time() ?>;
                var sessionTimeoutUTC = <?= $this->auth->getExpirationTimestamp() ?>;
                var adminUrl = '<?= admin_url() ?>';
            </script>
        <?php endif; ?>

        <div id="wrapper">
            <?= isset($adminmenu) ? $adminmenu : '' ?>
            <div id="content_wrapper">
                <div id="content">
                    <?php if ($this->auth->isAuthenticated()): ?>
                        <?php
                        $this->load->config('licence');
                        $licence_expiration_timestamp = $this->config->item('licence_expiration_timestamp');
                        $now_timestamp = time();
                        $days = $hours = 0;
                        $warning_message = false;
                        $is_expired = false;

                        if ($licence_expiration_timestamp)
                        {
                            $difference_timestamp = $licence_expiration_timestamp - $now_timestamp;

                            if ($difference_timestamp > 0)
                            {
                                $hours = floor($difference_timestamp / 3600);
                                $days = floor($hours / 24);


                                if ($days < 15)
                                {
                                    if ($hours > 24)
                                    {
                                        $warning_message = display_warning('Your software licence will expire in ' . $days . ' days.');
                                    }
                                    else
                                    {
                                        $warning_message = display_warning('Your software licence will expire in ' . $hours . ' hours.');
                                    }
                                }
                            }
                            else
                            {
                                $is_expired = true;
                                $warning_message = display_error('Your software licence has expired. Please contact system provider to continue using the system.');
                            }
                        }


                        if ($warning_message)
                        {
                            echo $warning_message;
                            if ($is_expired)
                            {
                                include('application_footer.php');
                                die();
                            }
                        }
                        ?>
<?php endif; ?>