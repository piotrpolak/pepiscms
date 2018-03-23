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
 * SSH model for working on remote filesystems
 *
 * @since 0.2.4
 */
class SSH_model extends Array_model
{
    /**
     * @var string|bool
     */
    private $host = false;

    /**
     * @var int
     */
    private $port = 22;

    /**
     * SSH username
     *
     * @var string|bool
     */
    private $username = false;

    /**
     * SSH password
     *
     * @var string|bool
     */
    private $password = false;

    /**
     * Default command to be executed remotely
     *
     * @var string|bool
     */
    private $command = false;

    /**
     * List of fields those values can contain spaces
     *
     * @var array
     */
    private $fields_that_can_contain_spaces = array();

    /** Field separator for parsing a feed
     *
     * @var string
     */
    private $feed_separator = ' ';

    /**
     * Sets feed field separator, default is space
     *
     * @param string $feed_separator
     * @return SSH_model $this
     */
    public function setFeedSeparator($feed_separator)
    {
        $this->feed_separator = $feed_separator;
        return $this;
    }

    /**
     * Returns field separator
     *
     * @return string
     */
    public function getFeedSeparator()
    {
        return $this->feed_separator;
    }

    /**
     * Sets remote host name
     *
     * @param string $host
     * @return SSH_model $this
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Returns remote host name
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets remote port
     *
     * @param int $port
     * @return SSH_model $this
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Returns remote port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets remote username
     *
     * @param string $username
     * @return SSH_model $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Returns remote username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets remote password
     *
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Returns remote password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets command to be executed upon reading the list
     *
     * @param $command
     * @return $this
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * Returns command to be executed upon reading the list
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Sets the list of names of fields that can contain spaces in their values
     *
     * @param array $fields_that_can_contain_spaces
     */
    public function setFieldNamesThatContainSpaces($fields_that_can_contain_spaces)
    {
        $this->fields_that_can_contain_spaces = $fields_that_can_contain_spaces;
    }

    /**
     * Returns the list of names of fields that can contain spaces in their values
     *
     * @return array
     */
    public function getFieldNamesThatContainSpaces()
    {
        return $this->fields_that_can_contain_spaces;
    }

    /**
     * Returns a raw array of objects
     *
     * @param mixed $extra_param
     * @return array a raw array of objects
     */
    public function getBasicFeed($extra_param)
    {
        try {
            // Attempting to execute command
            $contents = self::executeSshCommand($this->getHost(), $this->getPort(), $this->getUsername(), $this->getPassword(), $this->getCommand());
        } catch (Exception $e) {
            // Logging and parsing exception
            Logger::warning($e->getMessage(), 'SSH');
            show_error($e->getMessage()); // TODO Throw exception as PepisCMS 0.2.5
        }

        // Parsing command output
        return $this->parseCommandFeed($contents, $this->getFieldNamesThatContainSpaces());
    }

    /**
     * Executes command on a remote server
     *
     * @param string $host
     * @param int $port
     * @param bool|string $login
     * @param bool|string $password
     * @param bool|string $command
     * @return array
     * @throws Exception
     */
    public static function executeSshCommand($host, $port, $login = false, $password = false, $command = false)
    {
        // Attempting to connect
        $connection = self::getSshConnection($host, $port, $login, $password);

        // Attempting to execute command remotely
        $stream = @ssh2_exec($connection, $command);
        if (!$stream) {
            throw new Exception('ssh2_exec unable to execute command ' . $command);
        }

        // Reading command output line by line
        $contents = array();
        stream_set_blocking($stream, true);
        while ($line = fgets($stream, 1024)) {
            flush();
            $contents[] = $line;
        }
        fclose($stream);

        // Throwing exception upon empty response
        if (count($contents) == 0) {
            throw new Exception('ssh2_exec command executed but received an empty response ' . $command);
        }

        return $contents;
    }

    /**
     * Copies files between machines
     *
     * @param string $host
     * @param int $port
     * @param bool|string $login
     * @param bool|string $password
     * @param bool|string $local_file
     * @param bool|string $remote_file
     * @return bool
     * @throws Exception
     */
    public static function copyFileSsh($host, $port, $login = false, $password = false, $local_file = false, $remote_file = false)
    {
        // Attempting to connect
        $connection = self::getSshConnection($host, $port, $login, $password);

        // Copying file
        return ssh2_scp_send($connection, $local_file, $remote_file);
    }

    /**
     * Parses command into an array of objects
     *
     * @param array $contents
     * @param array $fields_that_can_contain_spaces
     * @return array
     */
    protected function parseCommandFeed($contents, $fields_that_can_contain_spaces = array())
    {
        // Line counter, -1 meaning reading headers
        $line_counter = -1;

        // The array containing parsed output
        $output = array();

        // Saving header starting/ending positions for parsing tabular data
        $header_positions = array();

        // For every read line
        foreach ($contents as $line) {
            // Parsing headers - for the first read line only
            if ($line_counter++ == -1) {
                // Exploding headers array, removing multiple spaces
                $headers = explode($this->getFeedSeparator(), preg_replace('/\s+/', ' ', $line));
                // Trim headers just in case and store its starting positions
                foreach ($headers as $header) {
                    // Preventing from having extra characters
                    $header = trim($header);
                    // Preventing from having an empty header
                    if (!$header) {
                        continue;
                    }
                    // Saving the starting position of every header
                    $header_positions[] = array(strtolower($header), strpos($line, $header));
                }

                // Walking trough all the headers
                $count_header_starting_positions = count($header_positions);
                for ($j = 0; $j < $count_header_starting_positions; $j++) {
                    // Getting end position as the starting position of the next header
                    $field_length = isset($header_positions[$j + 1][1]) ? $header_positions[$j + 1][1] - $header_positions[$j][1] : null;
                    // Saving the array
                    $header_positions[$j] = array($header_positions[$j][0], $header_positions[$j][1], $field_length);
                }
            } // For all the data rows
            else {
                // Creating row object
                $row = new stdClass();

                // Parsing data according to header definitions
                foreach ($header_positions as $header_position) {
                    // Getting metadata
                    $field_name = $header_position[0];
                    $field_starting_position = $header_position[1];
                    $length = $header_position[2];

                    // Expanding field (length) while possible
                    while (isset($line[$field_starting_position + $length - 1]) && $line[$field_starting_position + $length - 1] != ' ') {
                        $length++;
                    }

                    // Slicing the value - for the last element do not specify the length
                    if ($header_position[2] === null) {
                        $value = trim(substr($line, $field_starting_position));
                    } // Slicing the value - for any other element specify the length
                    else {
                        $value = trim(substr($line, $field_starting_position, $length));
                    }

                    // There are some fields that can not contain spaces - trim the value to the first space
                    if (!in_array($field_name, $fields_that_can_contain_spaces)) {
                        // Getting first space position
                        $space_pos = strpos($value, ' ');
                        // Checking whether the space occurs and trimming the value
                        if ($space_pos !== false) {
                            $value = substr($value, 0, $space_pos);
                        }
                    }

                    // Building the object according to Active Record data format
                    $row->$field_name = $value;
                }
                // Building array of objects
                $output[] = $row;
            }
        }

        return $output;
    }

    /**
     * Connects to remove host and returns the connection resource
     *
     * @param string $host
     * @param int $port
     * @param string $login
     * @param string $password
     * @return resource
     * @throws Exception
     */
    protected static function getSshConnection($host, $port, $login, $password)
    {
        // Checking the existance of SSH2 functions availibility
        if (!function_exists('ssh2_connect')) {
            Logger::error('ssh2_connect function was not found', 'SSH');
            show_error('ssh2_connect function was not found. Please see <a href="http://php.net/manual/en/ssh2.installation.php" target="_blank">http://php.net/manual/en/ssh2.installation.php</a> or <a href="http://packages.debian.org/source/sid/php-ssh2" target="_blank">http://packages.debian.org/source/sid/php-ssh2</a>');
        }

        // Attempting to connect
        $connection = ssh2_connect($host, $port);
        if (!$connection) {
            throw new Exception('ssh2_connect unable to connect to ' . $host . ':' . $port);
        }
        if (!ssh2_auth_password($connection, $login, $password)) {
            throw new Exception('ssh2_connect unable to authorize ' . $login);
        }

        return $connection;
    }

    /**
     * Parses command into an array of objects, helper function
     *
     * @param array $contents
     * @param array $fields_that_can_contain_spaces
     * @return array
     */
    protected function parseCommandFeedByExplode($contents, $fields_that_can_contain_spaces = array())
    {
        // Line counter, -1 meaning reading headers
        $line_counter = -1;
        // The array containing parsed output
        $output = array();
        // Header names
        $headers = array();

        // For every read line
        foreach ($contents as $line) {
            // Parsing headers - for the first read line only
            if ($line_counter++ == -1) {
                // Removing multiple spaces and exploding header values
                $headers = explode($this->getFeedSeparator(), preg_replace('/\s+/', ' ', $line));

                // Trimming headers
                foreach ($headers as &$header) {
                    $header = trim($header); // TODO apply array_walk
                }
            } // For all the data rows
            else {
                // Creating row object
                $row = new stdClass();

                // Getting line values, removing multiple spaces
                $line = explode($this->getFeedSeparator(), preg_replace('/\s+/', ' ', $line));

                // Parsing data according to header definitions
                for ($line_counter = 0; $line_counter < count($headers); $line_counter++) {
                    // Building the object according to Active Record data format
                    $row->{$headers[$line_counter]} = isset($line[$line_counter]) ? trim($line[$line_counter]) : null;
                }

                // Building array of objects
                $output[] = $row;
            }
        }

        return $output;
    }
}
