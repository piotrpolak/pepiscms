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
 * PDF Generator based on WKPDF
 * @author Christian Sciberras, Piotr Polak
 * @version 1.1-forked
 *
 * This class has been modified by Piotr Polak and it is not compatible with the original class by Christian Sciberras
 *
 * Licence notice: The class is based on code of Christian Sciberras (WKPDF class), see
 *
 *
 */
class PDFGenerator
{
    /**
     * PDF generated as landscape (vertical).
     */
    const PDF_PORTRAIT = 'Portrait';

    /**
     * PDF generated as landscape (horizontal).
     */
    const PDF_LANDSCAPE = 'Landscape';

    /**
     * Private use variables.
     */
    private $html = '';
    private $orient = 'Portrait';
    private $size = 'A4';
    private $toc = false;
    private $copies = 1;
    private $grayscale = false;
    private $title = '';
    private static $cpu = '';
    private $base_path = './';
    private $temp_base_path = './';

    public function getBinaryBasePath()
    {
        return $this->base_path;
    }

    public function setBinaryBasePath($base_path)
    {
        return $this->base_path = $base_path;
    }

    public function getTempBasePath()
    {
        return $this->temp_base_path;
    }

    public function setTempBasePath($temp_base_path)
    {
        if (!file_exists($temp_base_path)) {
            mkdir($temp_base_path);
        }
        return $this->temp_base_path = $temp_base_path;
    }

    public function getTempWebBasePath()
    {
        return $this->temp_web_base_path;
    }

    public function setTempWebBasePath($temp_web_base_path)
    {
        return $this->temp_web_base_path = $temp_web_base_path;
    }

    /**
     * Function that attempts to return the kind of CPU.
     * @return string CPU kind ('amd64' or 'i386').
     * @throws Exception
     */
    private static function _getCPU()
    {
        if (self::$cpu == '') {
            if (`grep -i amd /proc/cpuinfo` != '') {
                self::$cpu = 'amd64';
            } elseif (`grep -i intel /proc/cpuinfo` != '') {
                self::$cpu = 'i386';
            } else {
                if (class_exists('Logger')) {
                    Logger::error('WKPDF couldn\'t determine CPU ("' . `grep -i vendor_id /proc/cpuinfo` . '").', 'PDFGENERATOR');
                }
                throw new Exception('WKPDF couldn\'t determine CPU ("' . `grep -i vendor_id /proc/cpuinfo` . '").');
            }
        }
        return self::$cpu;
    }

    /**
     * Constructor: initialize command line and reserve temporary file.
     */
    public function __construct()
    {
    }

    public function getCMD()
    {
        $cmd = $this->getBinaryBasePath() . 'wkhtmltopdf-' . self::_getCPU();
        if (!file_exists($cmd)) {
            $output = null;
            $return_val = null;
            $last_line = exec('which wkhtmltopdf', $output, $return_val);
            if ($return_val == 0) {
                $cmd = 'xvfb-run wkhtmltopdf';
            } else {
                if (class_exists('Logger')) {
                    Logger::error('WKPDF static executable "' . htmlspecialchars($cmd, ENT_QUOTES) . '" was not found.', 'PDFGENERATOR');
                }
                throw new Exception('WKPDF static executable "' . htmlspecialchars($cmd, ENT_QUOTES) . '" was not found.');
            }
        } else {
            $cmd = '"' . $cmd . '"';
        }

        return $cmd;
    }

    /**
     * Set orientation, use constants from this class.
     * By default orientation is portrait.
     * @param string $mode Use constants from this class.
     */
    public function setOrientation($mode)
    {
        $this->orient = $mode;
    }

    public function getOrientation()
    {
        return $this->orient;
    }

    /**
     * Set page/paper size.
     * By default page size is A4.
     * @param string $size Formal paper size (eg; A4, letter...)
     */
    public function setPageSize($size)
    {
        $this->size = $size;
    }

    public function getPageSize()
    {
        return $this->size;
    }

    /**
     * Whether to automatically generate a TOC (table of contents) or not.
     * By default TOC is disabled.
     * @param bool $enabled True use TOC, false disable TOC.
     */
    public function setToc($enabled)
    {
        $this->toc = $enabled;
    }

    public function isToc()
    {
        return $this->toc;
    }

    /**
     * Set the number of copies to be printed.
     * By default it is one.
     * @param int $count Number of page copies.
     */
    public function setCopies($count)
    {
        $this->copies = $count;
    }

    public function getCopies()
    {
        return $this->copies;
    }

    /**
     * Whether to print in grayscale or not.
     * By default it is OFF.
     * @param bool True to print in grayscale, false in full color.
     */
    public function setGrayscale($mode)
    {
        $this->grayscale = $mode;
    }

    public function getGrayscale()
    {
        return $this->grayscale;
    }

    /**
     * Set PDF title. If empty, HTML <title> of first document is used.
     * By default it is empty.
     * @param string Title text.
     */
    public function setTitle($text)
    {
        $this->title = $text;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set html content.
     * @param string $html New html content. It *replaces* any previous content.
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }

    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Convert HTML to PDF.
     */
    protected function render()
    {
        do {
            $rand = mt_rand();
            $input_temporar_file = $this->getTempBasePath() . $rand . '.html';
        } while (file_exists($input_temporar_file));
        do {
            $output_temporar_file = $this->getTempBasePath() . mt_rand() . '.pdf';
        } while (file_exists($input_temporar_file));
        file_put_contents($input_temporar_file, $this->html);

        $command = '' . $this->getCmd() . ''
            . (($this->copies > 1) ? ' --copies ' . $this->copies : '')         // number of copies
            . ' --orientation ' . $this->orient                // orientation
            . ' --page-size ' . $this->size                 // page size
            . ($this->toc ? ' --toc' : '')                      // table of contents
            . ($this->grayscale ? ' --grayscale' : '')               // grayscale
            . (($this->title != '') ? ' --title "' . $this->title . '"' : '')      // title
            . ' "' . $input_temporar_file . '" '                       // URL and optional to write to STDOUT
            . ' "' . $output_temporar_file . '" ';

        $output = false;
        $return_var = false;
        exec($command, $output, $return_var);
        unlink($input_temporar_file);

        if ((int)$return_var > 1) {
            if (class_exists('Logger')) {
                Logger::error('WKPDF shell error, return code ' . (int)$return_var . '.', 'PDFGENERATOR');
            }
            throw new Exception('WKPDF shell error, return code ' . (int)$return_var . '.');
        }

        if (!file_exists($output_temporar_file)) {
            if (class_exists('Logger')) {
                Logger::error('WKPDF did not generate PDF file', 'PDFGENERATOR');
            }
            throw new Exception('WKPDF did not generate PDF file');
        }

        return $output_temporar_file;
    }

    public function download()
    {
        $output_temporar_file = $this->render();
        header('Content-Description: File Transfer');
        header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream', false);
        header('Content-Type: application/download', false);
        header('Content-Type: application/pdf', false);


        header('Content-Disposition: attachment; filename="' . basename($output_temporar_file) . '";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($output_temporar_file));
        readfile($output_temporar_file);
        unlink($output_temporar_file);
    }

    public function display()
    {
        $output_temporar_file = $this->render();
        header('Content-Type: application/pdf');
        header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Content-Length: ' . filesize($output_temporar_file));
        header('Content-Disposition: inline; filename="' . basename($output_temporar_file) . '";');
        readfile($output_temporar_file);
        unlink($output_temporar_file);
    }

    public function toFile($to_file)
    {
        $output_temporar_file = $this->render();
        return rename($output_temporar_file, $to_file);
    }
}
