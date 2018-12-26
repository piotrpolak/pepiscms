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

class MoveCommand extends \ContainerAware implements CommandInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'move';
    }

    /**
     * @inheritdoc
     */
    public function execute($baseDir, $currentRelativePath, $input)
    {
        $files = $this->getFiles($input);

        $new_path = $this->getNewLocation($input);

        if (!is_dir($baseDir . '/' . $new_path)) {
            throw new CommandException('New location does not exist');
        }

        if (strpos($new_path, '../') != null || $new_path == $currentRelativePath) {
            throw new CommandException('New location not set');
        }

        foreach ($files as $file) {
            if (file_exists($baseDir . '/' . $new_path . '/' . $file)) {
                \LOGGER::error('Unable to move: ' . $currentRelativePath . $file . ' to ' . $new_path . $file . '. File does not exist.', 'FILEMANAGER');
                throw new CommandException('File does not exist ' . $file);
            }


            if (@rename($baseDir . $currentRelativePath . $file, $baseDir . '/' . $new_path . '/' . $file)) {
                \LOGGER::info('Moving file or directory: ' . $currentRelativePath . $file . ' to ' . $new_path . $file, 'FILEMANAGER');
            } else {
                \LOGGER::error('Unable to move: ' . $currentRelativePath . $file . ' to ' . $new_path . $file, 'FILEMANAGER');
                throw new CommandException(sprintf($this->lang->line('filemanager_dialog_unable_to_move'), htmlentities($file)));
            }
        }
    }

    /**
     * @param $input
     * @return mixed
     * @throws CommandException
     */
    private function getNewLocation($input)
    {
        $newLocation = $input->post('new_location');

        if (!$newLocation || strlen($newLocation) == 0) {
            throw new CommandException('new_location POST attribute is required');
        }

        return $newLocation;
    }

    /**
     * @param \PEPISCMS_Input $input
     * @return array
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