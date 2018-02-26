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
 * Extended File Uploading Class
 */
class PEPISCMS_Upload extends CI_Upload
{
    /**
     * Verify that the filetype is allowed
     *
     * @param    bool $ignore_mime
     * @return    bool
     */
    public function is_allowed_filetype($ignore_mime = false)
    {
        if ($this->allowed_types === '*') {
            return true;
        }

        if (empty($this->allowed_types) or !is_array($this->allowed_types)) {
            $this->set_error('upload_no_file_types');
            return false;
        }

        $ext = strtolower(ltrim($this->file_ext, '.'));

        // Replacing long extensions
        $extensions_to_be_replaced = array('jpeg' => 'jpg');

        if (isset($extensions_to_be_replaced[$ext])) {
            $ext = $extensions_to_be_replaced[$ext];
        }

        if (!in_array($ext, $this->allowed_types, true)) {
            // PepisCMS modificaiton - added logging
            Logger::notice('File not allowed, wrong extension,  extension: ' . $ext . ', file_type:' . $this->file_type . ', allowed: ' . implode(',', $this->allowed_types), 'FILESYSTEM');
            return false;
        }

        // Images get some additional checks
        if (in_array($ext, array('gif', 'jpg', 'jpeg', 'jpe', 'png'), true) && @getimagesize($this->file_temp) === false) {
            // PepisCMS modificaiton - added logging
            Logger::notice('Submited file fails image check, extension: ' . $ext . ', file_type:' . $this->file_type . ', allowed: ' . implode(',', $this->allowed_types), 'FILESYSTEM');
            return false;
        }

        // PepisCMS modification - dont check mimetypes for selected types
        $ignore_mime_for_mimes = array('application/octet-stream', 'application/octetstream', 'application/force-download', 'text/x-comma-separated-values', 'text/plain');
        if (in_array($this->file_type, $ignore_mime_for_mimes)) {
            $ignore_mime = true;
        } elseif ($ext == 'pdf' && strpos($this->file_type, 'pdf')) { // application/x-pdf etc
            $ignore_mime = true;
        } elseif ($ext == 'xls') { // XLS files
            $ignore_mime = true;
        }
        // PepisCMS modification - end

        if ($ignore_mime === true) {
            return true;
        }

        // PepisCMS modificaiton - added logging
        if (isset($this->_mimes[$ext])) {
            $success = is_array($this->_mimes[$ext])
                ? in_array($this->file_type, $this->_mimes[$ext], true)
                : ($this->_mimes[$ext] === $this->file_type);

            if (!$success) {
                if (is_array($this->_mimes[$ext])) {
                    $mime = print_r($this->_mimes[$ext], true);
                } else {
                    $mime = $this->_mimes[$ext];
                }
                Logger::notice('File not allowed, wrong MIME type,  extension: ' . $ext . ', file_type:' . $this->file_type . ', mime: ' . $mime . ', allowed: ' . implode(',', $this->allowed_types), 'FILESYSTEM');
            }

            return $success;
        }

        // PepisCMS modificaiton - added logging
        Logger::notice('MIME type not found for: ' . $ext . ', file_type:' . $this->file_type . ', allowed: ' . implode(',', $this->allowed_types), 'FILESYSTEM');
        return false;
    }
}
