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

    private $uploadsPath = '';

    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('cms_enable_filemanager') === false || !$this->config->item('feature_is_enabled_filemanager')) {
            show_error($this->lang->line('global_feature_not_enabled'));
        }

        $this->assign('is_editor', false);
        $this->load->language('filemanager');

        $this->assign('title', $this->lang->line('filemanager_label'));

        $this->uploadsPath = $this->config->item('uploads_path');
    }

    public function index()
    {
        redirect(admin_url() . 'ajaxfilemanager/browse/');
    }

    public function getjsonfilelist()
    {
        $files = $dirs = array();
        $user_files_dir = $this->uploadsPath;
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

        $this->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT')
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate')
            ->set_header('Cache-Control: post-check=0, pre-check=0')
            ->set_header('Pragma: no-cache')
            ->set_header('Content-type: application/x-javascript');


        $this->output->append_output(json_encode($response));
    }

    /**
     * This is page dedicated for editors
     */
    public function editorbrowse()
    {
        $this->assign('is_editor', true)
            ->assign('popup_layout', true)
            ->assign('adminmenu', '');

        $this->browse();
    }

    public function browse()
    {
        $this->assign('upload_allowed_types', explode('|', $this->config->item('upload_allowed_types')))
            ->display('admin/ajaxfilemanager_browse.php');
    }

    public function sendcommand()
    {
        $command = $_POST['command'];
        $currentRelativePath = $_POST['path'];

        /**
         * @var \PiotrPolak\PepisCMS\Filemanager\Command\CommandInterface[]
         */
        $commands = array(
            new \PiotrPolak\PepisCMS\Filemanager\Command\CreateCommand(),
            new \PiotrPolak\PepisCMS\Filemanager\Command\DeleteCommand(),
            new \PiotrPolak\PepisCMS\Filemanager\Command\MoveCommand(),
            new \PiotrPolak\PepisCMS\Filemanager\Command\RenameCommand(),
        );


        $command = $this->getMatchedCommand($commands, $command);

        try {
            $command->execute($this->uploadsPath, $currentRelativePath, $this->input);
        } catch (\PiotrPolak\PepisCMS\Filemanager\Command\CommandException $e) {
            return $this->sendStatus($e->getMessage());
        }

        $this->sendStatus();
    }

    public function upload()
    {
        if (!isset($_POST['path'])) {
            $this->sendStatus('Path not found');
            return;
        }

        if ($_POST['path']{0} == '/') {
            $_POST['path'] = substr($_POST['path'], 1);
        }

        echo $this->genericupload($_POST['path']);
    }

    private function genericupload($current_path = '', $file_field_name = 'file')
    {
        $make_filenames_nice = true;

        $error = false;

        $config['upload_path'] = $this->uploadsPath . $current_path;
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
            return $this->sendStatus();
        } else {
            return $this->sendStatus($error);
        }
    }

    /**
     * @param $size
     * @param $path
     */
    public function thumbsize(int $size, ...$parts)
    {
        $size = max(10, min(100, $size));
        return $this->genericthumb(false, $size, $this->getCurrentImagePath('/', '/' . join('/', $parts)));
    }

    public function thumb()
    {
        return $this->genericthumb();
    }

    private function genericthumb($absolute = false, $size = 50, $current_path = false)
    {
        if (!$current_path) {
            $start_word = 'thumb';
            $current_path = $this->getCurrentImagePath($start_word, $_SERVER['REQUEST_URI']);
        }

        $image_path = $absolute ? '' : $this->uploadsPath;

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
        $path = $this->uploadsPath . $_GET['term'] . '*';

        $len = strlen($this->uploadsPath);

        $paths = glob($path, GLOB_ONLYDIR);
        foreach ($paths as &$path) {
            $path = substr($path, $len);
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

        $current_path = INSTALLATIONPATH . $this->uploadsPath . $current_path;

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

    /**
     * @param array $commands
     * @param $command
     * @return \PiotrPolak\PepisCMS\Filemanager\Command\CommandInterface
     */
    private function getMatchedCommand(array $commands, $command)
    {
        foreach ($commands as $c) {
            if ($c->getName() == $command) {
                return $c;
            }
        }

        throw new \RuntimeException('No command handler found for ' . $command);
    }

    private function sendStatus($errorMessage = false)
    {
        $errors = array();
        if ($errorMessage) {
            $errors[] = $errorMessage;
        }

        $response = array(
            'status' => $errorMessage ? 0 : 1,
            'errors' => $errors,
        );

        header('Content-type: application/x-javascript');
        die(json_encode($response));
    }

    /**
     * @param string $start_word
     * @param string $uri
     * @return string
     */
    private function getCurrentImagePath(string $start_word, string $uri) : string
    {
        $pos = strpos($uri, $start_word);
        $current_path = str_replace('/../', '', substr($uri, $pos + strlen($start_word)) . '/');
        $current_path = str_replace('//', '/', $current_path);

        $i = strlen($current_path) - 1;
        if ($current_path[$i] == '/') {
            $current_path = substr($current_path, 0, $i);
        }
        if ($current_path[0] == '/') {
            $current_path = substr($current_path, 1);
        }
        return $current_path;
    }
}
