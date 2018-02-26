<?php

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See license.txt
 * @link                http://www.polak.ro/
 */

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Filemanager controller
 */
class Ajaxfilemanager extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('cms_enable_filemanager') === false || !$this->config->item('feature_is_enabled_filemanager')) {
            show_error($this->lang->line('global_feature_not_enabled'));
        }

        $this->assign('is_editor', false);
        $this->load->language('filemanager');

        $this->assign('title', $this->lang->line('filemanager_label'));
    }

    public function index()
    {
        redirect(admin_url() . 'ajaxfilemanager/browse/');
    }

    public function getjsonfilelist()
    {
        $files = $dirs = array();
        $user_files_dir = $this->config->item('uploads_path');
        $current_path = isset($_POST['path']) ? str_replace('//', '/', str_replace('../', '', $_POST['path'] . '/')) : '/';

        if (is_dir($user_files_dir . $current_path)) {
            $directory = opendir($user_files_dir . $current_path);
            while ($file = readdir($directory)) {
                if ($file == '..' || $file == '.') {
                    continue;
                }

                $f['name'] = htmlspecialchars($file);
                $f['mtime'] = filemtime($user_files_dir . $current_path . $file);
                $f['ctime'] = filectime($user_files_dir . $current_path . $file);
                $f['size'] = filesize($user_files_dir . $current_path . $file);

                if (is_dir($user_files_dir . $current_path . $file)) {
                    $dirs[$file] = $f;
                } else {
                    $files[$file] = $f;
                }
            }

            ksort($dirs);
            ksort($files);

            $response = array(
                'status' => 1,
                'message' => 'OK',
                'time' => time(),
                'path' => $current_path,
                'directories' => array(),
                'files' => array(),
            );

            foreach ($dirs as $f) {
                $response['directories'][] = array(
                    'name' => $f['name'],
                    'mtime' => $f['mtime'],
                    'ctime' => $f['ctime'],
                    'size' => '-1',
                );
            }
            foreach ($files as $f) {
                $response['files'][] = array(
                    'name' => $f['name'],
                    'mtime' => $f['mtime'],
                    'ctime' => $f['ctime'],
                    'size' => $f['size'],
                );
            }
        } else {
            $response = array(
                'status' => 0,
                'message' => $this->lang->line('filemanager_dialog_path_not_found_error'),
                'time' => time(),
                'path' => $current_path,
            );
        }
        //usleep(100000); // For interface testing only

        header('Content-type: application/x-javascript');
        echo json_encode($response);
    }

    /**
     * This is page dedicated for editors
     */
    public function editorbrowse()
    {
        $this->assign('is_editor', true);
        $this->assign('popup_layout', true);
        $this->assign('adminmenu', '');
        $this->browse();
    }

    public function browse()
    {
        //$is_editor = $this->getAttribute( 'is_editor' );
        $this->assign('upload_allowed_types', explode('|', $this->config->item('upload_allowed_types')));
        $this->display('admin/ajaxfilemanager_browse.php');
    }

    public function sendcommand()
    {
        $command = $_POST['command'];
        $current_path = $_POST['path'];
        $files = explode('/', $_POST['files']);
        $user_files_dir = $this->config->item('uploads_path');
        $e_status = 2;
        $error_messages = array();

        switch ($command) {
            case 'create':
                $new_path = isset($_POST['new_location']) ? $_POST['new_location'] : false;
                $new_path = str_replace('/', '_', $new_path);
                $new_path = str_replace('\\', '_', $new_path);

                if (strlen($new_path)) {
                    if (!file_exists($user_files_dir . $current_path . $new_path)) {
                        LOGGER::info('Creating a new directory: ' . $current_path . $new_path, 'FILEMANAGER');
                        mkdir($user_files_dir . $current_path . $new_path);
                    } else {
                        $e_status = 0;
                        $error_messages[] = $this->lang->line('filemanager_dialog_file_already_exists');
                    }
                }
                break;

            case 'delete':
                foreach ($files as $file) {
                    if (is_file($user_files_dir . $current_path . $file)) {
                        if (@unlink($user_files_dir . $current_path . $file)) {
                            LOGGER::info('Deleting file: ' . $current_path . $file, 'FILEMANAGER');
                        } else {
                            $error_messages[] = sprintf($this->lang->line('filemanager_dialog_unable_to_delete_file'), htmlentities($file));
                        }
                    } elseif (is_dir($user_files_dir . $current_path . $file)) {
                        if (@rmdir($user_files_dir . $current_path . $file)) {
                            LOGGER::info('Deleting directory: ' . $current_path . $file, 'FILEMANAGER');
                        } else {
                            $error_messages[] = sprintf($this->lang->line('filemanager_dialog_unable_to_delete_nonempty_directory'), htmlentities($file));
                        }
                    }
                }
                break;

            case 'move':
                $new_path = isset($_POST['new_location']) ? $_POST['new_location'] : false;

                if (!is_dir($user_files_dir . '/' . $new_path)) {
                    $error_messages[] = 'New location does not exist';
                    $e_status = 0;
                    break;
                }

                if (strpos($new_path, '../') != null || $new_path == $current_path) {
                    $error_messages[] = 'New location not set';
                    $e_status = 0;
                    break;
                }

                foreach ($files as $file) {
                    if (!file_exists($user_files_dir . '/' . $new_path . '/' . $file)) {
                        @rename($user_files_dir . $current_path . $file, $user_files_dir . '/' . $new_path . '/' . $file);
                        LOGGER::info('Moving file or directory: ' . $current_path . $file . ' to ' . $new_path . $file, 'FILEMANAGER');
                    } else {
                        $error_messages[] = sprintf($this->lang->line('filemanager_dialog_unable_to_move'), htmlentities($file));
                    }
                }
                break;

            case 'rename':
                if (count($files) < 1) {
                    $error_messages[] = 'Wrong command syntax, you must submit at least 2 file names.';
                    $e_status = 0;
                    break;
                }

                $new_name = str_replace('/', '_', $files[1]);
                $new_name = str_replace('\\', '_', $new_name);

                $file_name = str_replace('/', '_', $files[0]);
                $file_name = str_replace('\\', '_', $file_name);

                if (is_file($user_files_dir . $current_path . $file_name)) {
                    $upload_allowed_types = $this->config->item('upload_allowed_types');
                    $upload_allowed_types = explode('|', $upload_allowed_types);

                    $ext = mb_strtolower(str_replace('.', '', strrchr($new_name, '.')));
                    if (in_array($ext, $upload_allowed_types)) {
                        $can_rename = true;
                    } else {
                        $error_messages[] = $this->lang->line('filemanager_dialog_file_extension_illegal');
                        $e_status = 0;
                        break;
                    }
                } else { // For directories
                    $can_rename = true;
                }

                if ($can_rename) {
                    if ($file_name != $new_name) {
                        if (!file_exists($user_files_dir . $current_path . $new_name)) {
                            rename($user_files_dir . $current_path . $file_name, $user_files_dir . $current_path . $new_name);
                            LOGGER::info('Renaming: ' . $current_path . $file_name . ' to ' . $current_path . $new_name, 'FILEMANAGER');
                        } else {
                            $error_messages[] = $this->lang->line('filemanager_dialog_file_already_exists');
                            $e_status = 0;
                            break;
                        }
                    }
                }

            default:
                break;
        }

        $count_errors = count($error_messages);


        $response = array(
            'status' => ($count_errors > 0 ? $e_status : 1),
            'errors' => array(),
        );

        if ($count_errors) {
            foreach ($error_messages as $e) {
                $response['errors'][] = $e;
            }
        }

        header('Content-type: application/x-javascript');
        echo json_encode($response);
    }

    public function upload()
    {
        if (!isset($_POST['path'])) {
            echo json_encode(array('status' => 0, 'message' => 'Path not found'));
            return;
        }

        if ($_POST['path']{0} == '/') {
            $_POST['path'] = substr($_POST['path'], 1);
        }

        echo $this->genericupload($_POST['path']);
    }

    public function genericupload($current_path = '', $json = true, $file_field_name = 'file')
    {
        $make_filenames_nice = true;

        $error = false;

        $config['upload_path'] = $this->config->item('uploads_path') . $current_path;
        $config['allowed_types'] = $this->config->item('upload_allowed_types');

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($file_field_name)) {
            $error = $this->upload->display_errors('', '');
        }

        if (!$error) {
            if ($make_filenames_nice) {
                $this->load->helper('string');
                $udata = $this->upload->data();
                $filename = $udata['file_name'];

                $ext = strtolower(substr(strrchr($filename, '.'), 1));
                $fname_length = (strlen($filename) - strlen($ext) - 1);
                $fname = substr($filename, 0, $fname_length);

                $nice_file_name = niceuri($fname) . '.' . $ext;

                if ($nice_file_name != $filename) {
                    if (!file_exists($udata['file_path'] . $nice_file_name || strtolower($filename) == $nice_file_name)) {
                        rename($udata['full_path'], $udata['file_path'] . $nice_file_name);
                        $udata['file_name'] = $nice_file_name;
                    }
                }
            }

            LOGGER::info('Uploading file: ' . $current_path . $udata['file_name'], 'FILEMANAGER');
            if ($json) {
                return json_encode(array('status' => 1, 'message' => 'Upload success'));
            } else {
                return 'OK';
            }
        } else {
            if ($json) {
                return json_encode(array('status' => 1, 'message' => 'Upload success', 'error' => array('message' => $error)));
            } else {
                return 'Error: ' . $error;
            }
        }
    }

    public function absolutethumb()
    {
        $start_word = 'thumb';
        $pos = strpos($_SERVER['REQUEST_URI'], $start_word);
        $current_path = str_replace('/../', '', substr($_SERVER['REQUEST_URI'], $pos + strlen($start_word)) . '/');
        $current_path = str_replace('//', '/', $current_path);


        $i = strlen($current_path) - 1;
        if ($current_path{$i} == '/') {
            $current_path = substr($current_path, 0, $i);
        }
        if ($current_path{0} == '/') {
            $current_path = substr($current_path, 1);
        }

        $current_path = explode('/', $current_path);

        $size = $current_path[0];
        array_shift($current_path);
        $current_path = implode('/', $current_path);

        if (!($size > 0 && $size < 1000)) {
            $size = 100;
        }

        return $this->genericthumb(true, $size, $current_path);
    }

    public function thumb()
    {
        return $this->genericthumb();
    }

    private function genericthumb($absolute = false, $size = 60, $current_path = false)
    {
        if (!$current_path) {
            $start_word = 'thumb';
            $pos = strpos($_SERVER['REQUEST_URI'], $start_word);
            $current_path = str_replace('/../', '', substr($_SERVER['REQUEST_URI'], $pos + strlen($start_word)) . '/');
            $current_path = str_replace('//', '/', $current_path);

            $i = strlen($current_path) - 1;
            if ($current_path{$i} == '/') {
                $current_path = substr($current_path, 0, $i);
            }
            if ($current_path{0} == '/') {
                $current_path = substr($current_path, 1);
            }
        }

        $image_path = $absolute ? '' : $this->config->item('uploads_path');

        $cache_path = $this->config->item('cache_path');
        $cache_path = ($cache_path === '') ? 'application/cache/' : $cache_path;
        if ($cache_path{0} !== '/') {
            $cache_path = INSTALLATIONPATH . $cache_path;
        }
        $cache_path .= '_thumbs/';

        if (!file_exists($cache_path)) {
            if (!@mkdir($cache_path)) {
                LOGGER::warning('Unable to generate thumb cache directory', 'FILEMANAGER');
                show_error($this->lang->line('filemanager_unable_to_generate_thumb_directory'));
            }
        }

        $source_file = $image_path . $current_path;

        if (file_exists($source_file)) {
            $hash = md5(filemtime($source_file) . filesize($source_file));
            $thumb_file = $cache_path . $hash . '__s' . $size . '.jpg';

            if (!file_exists($thumb_file)) {
                $config = array();
                $config['source_image'] = $source_file;
                $config['new_image'] = $thumb_file;
                $config['width'] = $size;
                $config['height'] = $size;
                $config['image_library'] = 'gd2';

                $this->load->library('Image_lib', $config);
                if (!$this->image_lib->resize()) {
                    LOGGER::warning('Unable to generate thumb ' . $thumb_file . ' ' . strip_tags($this->image_lib->display_errors()), 'FILEMANAGER');
                    echo $thumb_file . ' - ';
                    echo strip_tags($this->image_lib->display_errors());
                    die();
                }
            }
        } else {
            $thumb_file = APPPATH . '../theme/img/ajaxfilemanager/broken_image_50.png';
        }

        $cache_seconds_valid = (3600 * 24);

        header_remove('Pragma');
        header('Content-type: image/jpg');
        header('Cache-Control: max-age=' . $cache_seconds_valid . ', private');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cache_seconds_valid) . ' GMT');

        readfile($thumb_file);
        die();
    }

    public function getpathsjson()
    {
        parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $_GET);
        $_GET['term'] = str_replace('../', '', $_GET['term']);
        $path = $this->config->item('uploads_path') . $_GET['term'] . '*';

        $len = strlen($this->config->item('uploads_path'));

        $paths = glob($path, GLOB_ONLYDIR);
        foreach ($paths as &$path) {
            $path = substr($path, $len);
            $path = ltrim($path, '/');
        }

        echo json_encode($paths);
    }

    /**
     * File proxy for intranet systems
     */
    public function getfile()
    {
        $max = $this->uri->total_segments() + 1;
        $current_path = '';
        for ($i = 4; $i < $max; $i++) {
            $current_path .= '/' . $this->uri->segment($i);
        }

        $current_path = str_replace('/../', '', $current_path);
        $current_path = str_replace('//', '/', $current_path);

        $i = strlen($current_path) - 1;
        if ($current_path{$i} == '/') {
            $current_path = substr($current_path, 0, $i);
        }
        if ($current_path{0} == '/') {
            $current_path = substr($current_path, 1);
        }

        $current_path = INSTALLATIONPATH . $this->config->item('uploads_path') . $current_path;

        if (!file_exists($current_path)) {
            LOGGER::notice('Error 404, accessing inexisting file: ' . $current_path, 'FILEMANAGER');
            show_404();
        }

        LOGGER::info('Accessing file: ' . $current_path, 'FILEMANAGER');

        header('PepisCMS-intranet-secured: yes');

        $this->load->helper('file');
        $basename = basename($current_path);
        $mime = get_mime_by_extension(strtolower($basename));
        header('Content-type: ' . $mime);
        header('Content-Disposition: inline; filename="' . $basename . '"');
        readfile($current_path);
        die();
    }
}
