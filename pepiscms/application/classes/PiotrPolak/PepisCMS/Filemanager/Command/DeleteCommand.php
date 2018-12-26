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

class DeleteCommand extends \ContainerAware implements CommandInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'delete';
    }

    /**
     * @inheritdoc
     */
    public function execute($baseDir, $currentRelativePath, $input)
    {
        $files = $this->getFiles($input);

        foreach ($files as $file) {
            $fullPath = $baseDir . $currentRelativePath . $file;

            if (!file_exists($fullPath)) {
                throw new CommandException('File does not exist');
            }

            if (is_file($fullPath)) {
                if (@unlink($fullPath)) {
                    \LOGGER::info('Deleting file: ' . $currentRelativePath . $file, 'FILEMANAGER');
                } else {
                    throw new CommandException(sprintf($this->lang->line('filemanager_dialog_unable_to_delete_file'), htmlentities($file)));
                }
            } elseif (is_dir($fullPath)) {
                if (@rmdir($fullPath)) {
                    \LOGGER::info('Deleting directory: ' . $currentRelativePath . $file, 'FILEMANAGER');
                } else {
                    throw new CommandException(sprintf($this->lang->line('filemanager_dialog_unable_to_delete_nonempty_directory'), htmlentities($file)));
                }
            }
        }
    }

    /**
     * @param \PEPISCMS_Input $input
     * @return String[]
     * @throws CommandException
     */
    private function getFiles($input)
    {
        $files = explode('/', $input->post('files'));
        if (count($files) < 1) {
            throw new CommandException('No files specified');
        }
        return $files;
    }

}