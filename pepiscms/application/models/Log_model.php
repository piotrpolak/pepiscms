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
 * User group model
 *
 * @since 0.2.2
 */
class Log_model extends Generic_model
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable($this->config->item('database_table_logs'));
    }

    /**
     * Returns the list of users by IP address
     *
     * @param string $ip
     * @return array
     */
    public function getUsersByIp($ip)
    {
        $out = array();
        $result = $this->db->select($this->getTable() . '.user_id, user_email')
            ->from($this->getTable())
            ->join($this->config->item('database_table_users'),
                $this->getTable() . '.user_id=' . $this->config->item('database_table_users') . '.user_id')
            ->where('ip', $ip)
            ->get()
            ->result();

        foreach ($result as $row) {
            $out[$row->user_id] = $row->user_email;
        }

        return $out;
    }

    /**
     * Returns IP info based on external services
     * A cached wrapper to the helper of the same name
     *
     * @param string $ip
     * @return array
     */
    public function ip_info($ip)
    {
        $tag = 'ip_check_' . $ip;
        $this->load->helper('ip');
        $info = $this->auth->getSessionVariable($tag);
        if (!$info) {
            $info = ip_info($ip);
            $this->auth->setSessionVariable($tag, $info);
        }

        return $info;
    }

    /**
     * Returns user activity summary
     *
     * @param int $user_id
     * @return array
     */
    public function getUserActivitySummaryByUserId($user_id)
    {
        $out = array();

        $row = $this->db->select('min(timestamp) AS min_timestamp, MAX(timestamp) AS max_timestamp')
            ->from($this->getTable())
            ->where('user_id', $user_id)
            ->get()->row();

        $out['first_seen_timestamp'] = $row->min_timestamp;
        $out['last_seen_timestamp'] = $row->max_timestamp;

        return $out;
    }

    /**
     * Returns IP activity summary
     *
     * @param string $ip
     * @return array
     */
    public function getIpActivitySummaryByIp($ip)
    {
        $ip_where = $ip;

        if (!is_array($ip_where)) {
            $ip_where = array($ip_where);
        }

        $result = $this->db->select('MIN(timestamp) AS min_timestamp, MAX(timestamp) AS max_timestamp, ip')
            ->from($this->getTable())
            ->where_in('ip', $ip_where)
            ->group_by('ip')
            ->order_by('id', 'DESC')
            ->get()->result();

        $out = array();
        foreach ($result as $row) {
            $out[$row->ip]['first_seen_timestamp'] = $row->min_timestamp;
            $out[$row->ip]['last_seen_timestamp'] = $row->max_timestamp;
        }

        if (!is_array($ip)) {
            return $out[$ip];
        }

        // Else return array
        return $out;
    }

    /**
     * Returns IP activity summary
     *
     * @param int $user_id
     * @return array
     */
    public function getIpActivitySummaryUserId($user_id)
    {
        $user_id_where = $user_id;

        if (!is_array($user_id_where)) {
            $user_id_where = array($user_id_where);
        }

        $result = $this->db->select('MIN(timestamp) AS min_timestamp, MAX(timestamp) AS max_timestamp, ip')
            ->from($this->getTable())
            ->where_in('user_id', $user_id_where)
            ->group_by('ip')
            ->order_by('max_timestamp', 'DESC')
            ->get()->result();

        $out = array();
        foreach ($result as $row) {
            $out[$row->ip]['first_seen_timestamp'] = $row->min_timestamp;
            $out[$row->ip]['last_seen_timestamp'] = $row->max_timestamp;
        }

        // Else return array
        return $out;
    }

    /**
     * Returns the list of accounts related to account specified
     *
     * @param int $user_id
     * @return array|bool
     */
    public function getRelatedUsersByUserId($user_id)
    {
        $ips = $this->getIpsByUserId($user_id);

        if (count($ips) == 0) {
            return false;
        }

        return $this->db->select($this->config->item('database_table_users') . '.*')
            ->from($this->getTable())
            ->join($this->config->item('database_table_users'),
                $this->config->item('database_table_users') . '.user_id=' . $this->getTable() . '.user_id')
            ->where_in('ip', $ips)
            ->where($this->config->item('database_table_users') . '.user_id !=', $user_id)
            ->group_by($this->config->item('database_table_users') . '.user_id')
            ->get()
            ->result();
    }

    /**
     * Returns the list IPs by user id
     *
     * @param string $user_id
     * @return array
     */
    public function getIpsByUserId($user_id)
    {
        $result = $this->db->select('ip')
            ->from($this->config->item('database_table_logs'))
            ->where('user_id', $user_id)
            ->order_by('timestamp', 'DESC')
            ->group_by('ip')
            ->get()
            ->result();

        $out = array();
        foreach ($result as $row) {
            $out[] = $row->ip;
        }

        return $out;
    }

    /**
     * @param $days_before
     * @return array
     */
    public function getWarningStatistics($days_before)
    {
        $result = $this->db->select('DATE(timestamp) as date, count(id) as count')
            ->from($this->getTable())
            ->where_in('level', array(Logger::MESSAGE_LEVEL_WARNING, Logger::MESSAGE_LEVEL_ERROR))
            ->where('timestamp > "' . date('Y-m-d h:i:s', time() - ($days_before * 24 * 3600)) . '"')
            ->group_by('date')
            ->order_by('date')
            ->get()
            ->result();

        $output = array();
        foreach ($result as $line) {
            $output[$line->date] = $line->count;
        }

        return $output;
    }

    /**
     * Imports CodeIgniter logs
     *
     * @return bool
     */
    public function importFrameworkLogs()
    {
        // Reading logs from the given location
        $log_path = $this->config->item('log_path');
        $logs = glob($log_path . '*.php');

        // Return if there are no logs
        if (!$logs || count($logs) == 0) {
            return false;
        }

        // For every file
        foreach ($logs as $log_file) {
            // Read and explode contents
            $contents = file_get_contents($log_file);
            $contents = explode("\n", $contents);

            // For every line
            foreach ($contents as &$line) {
                // Explode line and check if it is parsable
                $line = explode(" - ", $line);
                if (!isset($line[1])) {
                    continue;
                }

                // Parsing values
                $servity = $line[0];
                list($timestamp, $message) = explode(' --> ', $line[1], 2);

                // Cleanup
                unset($line);

                // Default collection name
                $collection = 'FRAMEWORK';

                // Cleaning up the message
                $message = str_replace(' --> ', '', $message);


                // Logging notice
                if (strpos($message, 'Severity: Notice') !== false) {
                    $message = str_replace('Severity: Notice', '', $message);
                    $servity = 'NOTICE';
                    $collection = 'PHP';
                    if ($this->config->item('log_threshold') < 3) {
                        continue;
                    }
                } // Logging error
                elseif (strpos($message, 'Severity: Error') !== false) {
                    $message = str_replace('Severity: Error', '', $message);
                    $servity = 'ERROR';
                    $collection = 'PHP';
                } // Logging warning
                elseif (strpos($message, 'Severity: Warning') !== false) {
                    $message = str_replace('Severity: Warning', '', $message);
                    $servity = 'WARNING';
                    $collection = 'PHP';
                }

                // Logging based on the level
                switch ($servity) {
                    case 'ERROR':
                        Logger::error($message, $collection);
                        break;
                    case 'WARNING':
                        Logger::warning($message, $collection);
                        break;
                    case 'NOTICE':
                        Logger::notice($message, $collection);
                        break;
                    default:
                        Logger::debug($message, $collection);
                        break;
                }
            }

            // Removed unneeded file
            unlink($log_file);
        }

        return true;
    }

    /**
     * Removes framework related logs
     *
     * @return bool
     */
    public function removeFrameworkLogs()
    {
        return $this->db->where('collection', 'FRAMEWORK')->or_where('collection', 'PHP')->delete($this->getTable());
    }
}
