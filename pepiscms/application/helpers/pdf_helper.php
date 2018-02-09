<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

if (!function_exists('html_to_pdf')) {

    /**
     * Converts HTML to PDF using pdfGenerator
     *
     * @param string $html
     * @param string|boolean $save_path
     * @param string|boolean $base_url_for_relative_urls
     * @return mixed
     */
    function html_to_pdf($html, $save_path = FALSE, $base_url_for_relative_urls = FALSE)
    {
        if ($base_url_for_relative_urls) {
            $html = preg_replace("#(<\s*(a|img|style|script)\s+[^>]*(href|src)\s*=\s*[\"'])(?!http)([^\"'>]+)([\"'>]+)#", '$1' . $base_url_for_relative_urls . '$4$5', $html);
        }

        $CI = &get_instance();
        $CI->load->library('PDFGenerator');
        $CI->pdfgenerator->setHtml($html);
        $CI->pdfgenerator->setBinaryBasePath(INSTALLATIONPATH . 'application/binaries/');
        $CI->pdfgenerator->setTempBasePath(INSTALLATIONPATH . 'application/cache/tmp/');

        if ($save_path) {
            return $CI->pdfgenerator->toFile($save_path);
        }

        return $CI->pdfgenerator->display();
    }

}