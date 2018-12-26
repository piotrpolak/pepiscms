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

class CreateCommand extends \ContainerAware implements CommandInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'create';
    }

    /**
     * @inheritdoc
     */
    public function execute($baseDir, $currentRelativePath, $input)
    {
        $newLocation = $this->getNewLocation($input);

        if (file_exists($baseDir . $currentRelativePath . $newLocation)) {
            throw new CommandException($this->lang->line('filemanager_dialog_file_already_exists'));
        }

        if (mkdir($baseDir . $currentRelativePath . $newLocation)) {
            \LOGGER::info('Created a new directory: ' . $currentRelativePath . $newLocation, 'FILEMANAGER');
        } else {
            \LOGGER::error('Unable to create a new directory: ' . $currentRelativePath . $newLocation, 'FILEMANAGER');
            throw new CommandException('Unable to create new directory');
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
        if ($newLocation) {
            $newLocation = str_replace('/', '_', $newLocation);
            $newLocation = str_replace('\\', '_', $newLocation);
        }

        if (!$newLocation || strlen($newLocation) == 0) {
            throw new CommandException('new_location POST attribute is required');
        }

        return $newLocation;
    }

}