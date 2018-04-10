<?php
/**
 * PepisCMS 1.0.0 Installer
 */

/*
 * Quick installation instructions
 * 1. Copy this file to the directory where your project's directory
 * 	  Project's directory is the place where you usually keep index.php and the rest of the files.
 * 2. Comment or remove "die" line of this file
 * 3. Configure core path. The core is used as a library and can be just anywhere.
 * 	  Do not edit files in the core directory - you will loose the possibility to upgrade PepisCMS
 * 4. Run install.php from your browser and follow the instructions
 */

if (file_exists('pepiscms')) {
    die('Access blocked. Please copy this file into your project directory and configure the $vendor_path.');
}

define('BASEPATH', '.');

/**
 * Path to composer vendor directory
 *
 * Example: ./vendor/
 */
$vendor_path = './vendor/';


// -----------------------------------------------------------------------------
//
// DO NOT EDIT ANYTHING BELOW THIS LINE
// -----------------------------------------------------------------------------
$version = '1.0.0';
ini_set('display_errors', 0);
$log = $errors = $warnings = array();
$core_path = $vendor_path . '/piotrpolak/pepiscms/';
$templates_base_path = $core_path . 'pepiscms/resources/config_template/';
$show_paths = TRUE;

if (!file_exists($core_path)) {
    $errors[] = 'Variable $core_path points to a directory that does not exist. Edit installer.php to configure the $core_path to point to PepisCMS library directory.';
} elseif (!file_exists($templates_base_path)) {
    $errors[] = 'Variable $core_path points to a directory that does not seem to be a valid PepisCMS library directory. Note the core directory must contain both pepiscms/ and codeigniter/ directory.';
} elseif (!file_exists($core_path . 'pepiscms/application/version.php')) {
    $errors[] = 'File version.php not found. Make sure that core directory contains a valid PepisCMS library';
} elseif (!is_writable('./')) {
    $errors[] = 'The current directory is not writeable! Use chmod -R 0765 ./ and make sure Apache is properly configured!';
}

if (file_exists($core_path . 'pepiscms/application/version.php')) {
    require_once($core_path . 'pepiscms/application/version.php');
    $version = PEPISCMS_VERSION;
}

if (file_exists('./index.php')) {
    $show_paths = FALSE;
    $errors[] = 'File index.php already exists';
}
if (file_exists('.htaccess')) {
    $show_paths = FALSE;
    $errors[] = 'File .htaccess already exists';
}

if (file_exists('application/config/config.php')) {
    $show_paths = FALSE;
    $errors[] = 'File application/config/config.php already exists';
}

if (file_exists('application/config/database.php')) {
    $show_paths = FALSE;
    $errors[] = 'File application/config/database.php already exists';
}

if ($show_paths) {
    // Preventing some security issues
    $log[] = 'Path where core directory is located $core_path = ' . $core_path;
    $log[] = 'Path where core index.php and .htacces templates are located is located $templates_base_path = ' . $templates_base_path;
}

if (!ini_get('short_open_tag') == '1') {
    $warnings[] = 'PHP setting short_open_tag must be set to On. Change the configuration value in order to obtain maximum performance. (php.ini short_open_tag = On)';
}

if (function_exists('apache_get_modules') && !in_array('mod_rewrite', apache_get_modules())) {
    $errors[] = 'Apache mod_rewrite must be loaded, otherwise the system will throw 500 errors.';
}

if (!count($errors)) {
    if (!copy($templates_base_path . 'template_.htaccess', './.htaccess')) {
        $errors[] = 'Unable to copy .htaccess';
    }
}

$success = FALSE;
if (!count($errors)) {
    $contents = file_get_contents($templates_base_path . 'template_index.php');
    if ($contents) {
        if (!file_put_contents('./index.php', str_replace('TEMPLATE_VENDOR_PATH', $vendor_path, $contents))) {
            $errors[] = 'Unable to write index.php';
        }
    }
    if (!count($errors)) {
        $success = TRUE;
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>PepisCMS Installation</title>
    <style type="text/css">
        <!--
        body {
            background: #EAEAEA;
        }

        .wrapper {
            width: 600px;
            margin-left: auto;
            margin-right: auto;
            margin-top: 30px;
            background: #FFF;
            padding: 20px;
            border: #AEAEAE solid 1px;
        }

        * {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0px;
            padding: 0px;
        }

        body {
            margin: 20px;
        }

        h1 {
            margin-bottom: 10px;
        }

        h2 {
            margin-top: 20px;
            font-size: 14px;
            color: #333;
        }

        p {
            font-size: small;
            padding: 10px;
        }

        p.error {
            color: #8C1C38;
            font-weight: bold;
        }

        p.warning {
            color: #685a38;
            font-weight: bold;
        }

        p.errorbox {
            color: #8C1C38;
            border: solid 1px #8C1C38;
            margin: 20px 0 20px 0;
            font-weight: bold;
        }

        p.successbox {
            color: green;
            border: solid 1px green;
            margin: 20px 0 20px 0;
            font-weight: bold;
        }

        p.sub {
            margin-top: 30px;
            color: #666666;
            font-size: small;
        }

        ol, ul {
            margin: 10px;
            margin-left: 40px;
        }

        ol li {
            font-size: x-small;
        }

        a {
            color: #1174D6;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        -->
    </style>
</head>

<body>
<div class="wrapper">
    <h1>PepisCMS Installation</h1>

    <?php if ($success): ?>
        <p class="successbox">Success! Template files are successfully installed. Now it is time to <a
                    href="./admin/installer/">configure your application</a>.</p>
    <?php endif; ?>
    <?php if (count($errors)): ?>
        <p class="errorbox">The installation is aborted. Please fix the issues listed below and try again.
            However you can try to <a href="./admin/installer/">configure your application</a>.</p>
        <?php foreach ($errors as $line): ?>
            <p class="error"><?= $line ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php foreach ($warnings as $line): ?>
        <p class="warning"><?= $line ?></p>
    <?php endforeach; ?>
    <?php foreach ($log as $line): ?>
        <p><?= $line ?></p>
    <?php endforeach; ?>

    <p class="sub">PepisCMS v<?= $version ?></p>
</div>
</body>
</html>