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

namespace PiotrPolak\PepisCMS\Filemanager\Command;

class RenameCommand extends \ContainerAware implements CommandInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'rename';
    }

    /**
     * @inheritdoc
     */
    public function execute($baseDir, $currentRelativePath, $input)
    {
        $files = $this->getFiles($input);

        $new_name = $this->cleanupFileName($files[1]);
        $file_name = $this->cleanupFileName($files[0]);

        if (file_exists($baseDir . $currentRelativePath . $new_name)) {
            throw new CommandException($this->lang->line('filemanager_dialog_file_already_exists'));

        }

        if ($file_name == $new_name) {
            throw new CommandException('New name is the same as the old one.');
        }

        if (is_file($baseDir . $currentRelativePath . $file_name)) {
            $upload_allowed_types = $this->config->item('upload_allowed_types');
            $upload_allowed_types = explode('|', $upload_allowed_types);

            $ext = mb_strtolower(str_replace('.', '', strrchr($new_name, '.')));
            if (!in_array($ext, $upload_allowed_types)) {
                throw new CommandException($this->lang->line('filemanager_dialog_file_extension_illegal'));
            }
        }


        if (@rename($baseDir . $currentRelativePath . $file_name, $baseDir . $currentRelativePath . $new_name)) {
            \LOGGER::info('Renamed: ' . $currentRelativePath . $file_name . ' to ' . $currentRelativePath . $new_name, 'FILEMANAGER');
        } else {
            \LOGGER::error('Unable to rename: ' . $currentRelativePath . $file_name . ' to ' . $currentRelativePath . $new_name, 'FILEMANAGER');
            throw new CommandException('Unable to rename: ' . $currentRelativePath . $file_name . ' to ' . $currentRelativePath . $new_name);
        }
    }


    /**
     * @param \PEPISCMS_Input $input
     * @return array
     */
    private function getFiles($input)
    {
        $files = explode('/', $input->post('files'));
        if (count($files) < 2) {
            throw new \RuntimeException('No files specified');
        }
        return $files;
    }

    /**
     * @param array $files
     * @return mixed
     */
    private function cleanupFileName($name)
    {
        return str_replace(array('/', '\\'), '_', $name);
    }
}