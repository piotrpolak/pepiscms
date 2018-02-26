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
 * Reads and caches directory content and caches it.
 *
 * @since 0.1.
 */
class CachedDirectoryReader
{
    private $cache_directory;
    private $cache_expires;

    /**
     * CachedDirectoryReader constructor.
     * @param bool|array $params
     */
    public function __construct($params = null)
    {
        $this->cache_directory = $params['cache_directory'];
        $this->cache_expires = $params['cache_expires'];
    }

    /**
     * Reads directory cached
     *
     * @param string $directory
     * @return array
     */
    public function readDirectory($directory = './')
    {
        $file_path = $this->cache_directory . '/dircache_' . md5($directory);

        if (file_exists($file_path) && $this->isCacheFileNonExpired($file_path)) {
            return unserialize(file_get_contents($file_path));
        } else {
            $files = $this->readDirectoryContents($directory);
        }

        file_put_contents($file_path, serialize($files));

        return $files;
    }

    /**
     * Reads directory contents
     *
     * @param string $directory
     * @return array
     */
    private function readDirectoryContents($directory = './')
    {
        $files = array();

        $dir = opendir($directory);

        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            if (is_dir($directory . $file)) {
                $files = array_merge($files, $this->readDirectoryContents($directory . $file . '/'));
            } elseif ($this->isHtmlFile($file)) {
                $files[] = $directory . $file;
            }
        }

        return $files;
    }

    /**
     * @param $file
     * @return bool
     */
    private function isHtmlFile($file)
    {
        return substr($file, strlen($file) - 5) == '.html' || substr($file, strlen($file) - 4) == '.htm';
    }

    /**
     * @param $file_path
     * @return bool
     */
    private function isCacheFileNonExpired($file_path)
    {
        return time() - filemtime($file_path) < $this->cache_expires;
    }
}
