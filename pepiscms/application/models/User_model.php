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
 * User model
 *
 * @since 0.1.0
 */
class User_model extends Generic_model
{

    /**
     * Minimum minimum allowed length of the password
     *
     * @var int
     */
    private $minimum_allowed_password_length = 4;

    /**
     * Minimum minimum allowed strength of the password
     *
     * @var int
     */
    private $minimum_allowed_password_strength = 2;

    /**
     * Allowed hashing algorithms
     *
     * @var int
     */
    private $allowed_hashing_algorithms = array('md5', 'sha512');

    /**
     * Default constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTable($this->config->item('database_table_users'));
        $this->setIdFieldName('user_id');
        $this->setAcceptedPostFields(array('title', 'phone_number', 'image_path', 'birth_date', 'alternative_email', 'note', 'status'));

        // Reading password constraints from config
        if ($this->config->item('security_minimum_allowed_password_length') > 0 || $this->config->item('security_minimum_allowed_password_length') === 0) {
            $this->minimum_allowed_password_length = $this->config->item('security_minimum_allowed_password_length');
        }
        if ($this->config->item('security_minimum_allowed_password_strength') > 0 || $this->config->item('security_minimum_allowed_password_strength') === 0) {
            $this->minimum_allowed_password_strength = $this->config->item('security_minimum_allowed_password_strength');
        }

        $this->load->helper('date');
    }

    /**
     * Generates random string used as salt
     *
     * @return string
     */
    public function generateSalt()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36); // Taken from FOSUserBundle
    }

    /**
     * Returns encoded password
     *
     * If you call the function with implicit values then the generated hash will be fully compatible with previous versions of PepisCMS
     *
     * @param $password
     * @param string $salt
     * @param string $algorithm
     * @param int $iterations
     * @return bool|string
     */
    public function encodePassword($password, $salt = '', $algorithm = 'md5', $iterations = 1)
    {
        // Checking whether the specified algorithm is allowed
        if (!in_array($algorithm, $this->allowed_hashing_algorithms)) {
            return false;
        }

        // HAHA Plaintext by default!
        $digest = $password;

        // Iterating digest, gluing salt at the end at every iteration
        for ($i = 0; $i < $iterations; $i++) {
            $digest = hash($algorithm, $digest . $salt, false);
        }

        return $digest;
    }

    /**
     * Returns default hashing algorithm
     *
     * @return string
     */
    public function getDefaultHashingAlgorithm()
    {
        return 'sha512';
    }

    /**
     * Returns default number of iterations
     *
     * @return int
     */
    public function getDefaultNumberOfIterations()
    {
        return 5;
    }

    /**
     * Registers a new user and sends email notification
     *
     * @param string $display_name
     * @param string $user_email
     * @param bool $user_login
     * @param bool $password
     * @param array $group_ids
     * @param bool $is_root
     * @param bool $send_email_notification
     * @param array $data
     * @param int $account_type
     * @return bool
     */
    public function register($display_name, $user_email, $user_login = false, $password = false, $group_ids = array(), $is_root = false, $send_email_notification = true, $data = array(), $account_type = 0)
    {
        // If there is no password specified, lets generate one for the user
        if (!$password) {
            $password = $this->generateEasyPassword($this->getMinimumAllowedPasswordLenght() + 2);
        }

        // Reseting user login
        if (!trim($user_login)) {
            $user_login = null;
        }

        // Generating salt and reading algorithm info
        $hashing_salt = $this->generateSalt();
        $hashing_algorithm = $this->getDefaultHashingAlgorithm();
        $hasing_iterations = $this->getDefaultNumberOfIterations();

        // Encoding password
        $encoded_password = $this->encodePassword($password, $hashing_salt, $hashing_algorithm, $hasing_iterations);

        // Setting query data
        $this->db->set('display_name', $display_name)
            ->set('is_root', $is_root ? 1 : 0)
            ->set('user_email', $user_email)
            ->set('user_login', $user_login)
            ->set('account_type', $account_type)
            ->set('password', $encoded_password)
            ->set('status', 1)
            ->set('hashing_salt', $hashing_salt)
            ->set('hashing_algorithm', $hashing_algorithm)
            ->set('hashing_iterations', $hasing_iterations)
            ->set('title', '');

        // Setting extra user information
        if (count($data)) {
            foreach ($data as $key => $value) {
                if (in_array($key, $this->getAcceptedPostFields())) {
                    $this->db->set($key, $value);
                }
            }
        }

        // Attempting to register user
        $success = $this->db->insert($this->getTable());
        if (!$success) {
            return false;
        }

        $user_id = $this->db->insert_id();

        // Binding user to groups
        if (is_array($group_ids)) {
            $this->bindUserToGroups($user_id, $group_ids);
        }

        // Another (useless?) check
        if (!$user_email) {
            return false;
        }

        // Sending notification if case
        if ($send_email_notification) {
            $site_name = $this->config->item('site_name');

            $this->load->library('EmailSender');
            $email_data = array(
                'display_name' => $display_name,
                'user_email' => $user_email,
                'base_url' => base_url(),
                'password' => $password,
                'site_name' => $site_name,
                'date' => date('Y-m-d'),
            );

            $email_language = $this->lang->getCurrentLanguage();

            // Formating user subject
            $email_subject = sprintf($this->lang->line('email_registration_notification_subject'), $site_name);

            // Sending email
            $success = $this->emailsender->sendSystemTemplate($user_email, $this->config->item('site_email'),
                $site_name, $email_subject, 'register', $email_data, true, $email_language);

            // Logging success/error
            if ($success) {
                LOGGER::info('Registering user, sending notification', 'USER', $user_id);
            } else {
                LOGGER::warning('Enable to send registration notification to user_id:' . $user_id, 'USER', $user_id);
            }
        } else {
            LOGGER::info('Registering user, notification not sent', 'USER', $user_id);
        }

        // Returning registered user id
        return $user_id;
    }

    /**
     * Redoes group label
     *
     * @param int $user_id
     * @return bool
     */
    public function redoGroupsLabel($user_id)
    {
        // Getting group names
        $result = $this->db->select('group_name')
            ->from($this->config->item('database_table_groups'))
            ->join($this->config->item('database_table_user_to_group'),
                $this->config->item('database_table_user_to_group') . '.group_id = '
                . $this->config->item('database_table_groups') . '.group_id')
            ->where($this->config->item('database_table_user_to_group') . '.user_id', $user_id)
            ->get()
            ->result();

        $group_names = array();
        foreach ($result as $line) {
            $group_names[] = $line->group_name;
        }

        // Formating string
        $label = implode(', ', $group_names);

        // Saving in the database
        return $this->db->where('user_id', $user_id)->set('groups_label', $label)->update($this->getTable());
    }

    /**
     * Returns associative array of user access
     *
     * @param int $user_id
     * @return array
     */
    public function getUserAccess($user_id)
    {
        // Getting pairs of entity and granted access for the given user
        $result = $this->db->select('entity, access')
            ->from($this->config->item('database_table_group_to_entity') . ' AS ge')
            ->join($this->config->item('database_table_user_to_group') . ' as ugl', 'ge.group_id = ugl.group_id')
            ->where('ugl.user_id', $user_id)
            ->get()
            ->result();

        $entity_access = array();

        foreach ($result as $line) {
            // For lower values
            if (isset($entity_access[$line->entity]) && $entity_access[$line->entity] > $line->access) {
                continue;
            }

            // For any values
            $entity_access[$line->entity] = (int)$line->access;
        }

        return $entity_access;
    }

    /**
     * Checks whether the email exists
     *
     * @param string $user_email
     * @return bool
     */
    public function emailExists($user_email)
    {
        return $this->db->where('user_email', $user_email)->from($this->getTable())->count_all_results() > 0;
    }

    /**
     * Returns user ID by user login
     *
     * @param string $user_login
     * @return bool
     */
    public function getUserIdByUserLogin($user_login)
    {
        // What a surprise
        if (!$user_login) {
            return false;
        }

        // Reading user id
        $row = $this->db->select('user_id')
            ->where('user_login', $user_login)
            ->from($this->getTable())
            ->limit(1)
            ->get()
            ->row();

        // No user, no ID :(
        if (!$row) {
            return false;
        }

        return $row->user_id;
    }

    /**
     * Returns the minimum allowed password length
     *
     * @return int
     */
    public function getMinimumAllowedPasswordLenght()
    {
        return $this->minimum_allowed_password_length;
    }

    /**
     * Returns the minimum allowed password strength
     * @return int
     */
    public function getMinimumAllowedPasswordStrength()
    {
        return $this->minimum_allowed_password_strength;
    }

    /**
     * Tells whether the passowrd is strong enough
     *
     * TODO Move to password generator?
     *
     * @param string $password
     * @return bool
     */
    public function isPassowrdStrongEnough($password)
    {
        // Checking password length
        if (strlen($password) < $this->getMinimumAllowedPasswordLenght()) {
            return false;
        }

        // Checking password strength
        if ($this->getPasswordStrenght($password) < $this->getMinimumAllowedPasswordStrength()) {
            return false;
        }

        return true;
    }

    /**
     * Returns password strength as int from 0 to 3
     *
     * TODO Move to password generator?
     *
     * @param string $password
     * @return int
     */
    public function getPasswordStrenght($password)
    {
        // Based on http://passwordadvisor.com/CodePhp.aspx
        $score = 0;

        if (strlen($password) < 1) {
            return $score; // 0
        }

        if (preg_match("/[a-z]/", $password) && preg_match("/[A-Z]/", $password)) {
            $score++; // 1
        }

        if (preg_match("/[0-9]/", $password)) {
            $score++; // 2
        }

        if (preg_match("/[^a-zA-Z0-9]/", $password)) {
            $score++; // 3
        }

        return $score;
    }

    /**
     * Generates a strong password of a given lenght
     *
     * TODO Move to password generator?
     *
     * @param int $length
     * @return string
     */
    public function generateStrongPassword($length)
    {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&*?_,-_[]()0123456789';
        $max = strlen($str);
        if (!$length) {
            $length = 12;
        }

        $j = 0;
        $password = '';
        do {
            if (++$j > 15) {
                show_error('User_model::generateStrongPassword has entered an infinite loop. Execution stopped ' . $password);
            }
            $password = '';
            for ($i = 0; $i < $length; $i++) {
                $password .= $str{rand(0, $max - 1)};
            }
        } while (!$this->isPassowrdStrongEnough($password));

        return $password;
    }

    /**
     * Generates a satisfactory strong password of a given lenght
     *
     * TODO Move to password generator?
     *
     * @param int $length
     * @return string
     */
    public function generateAcceptablePassword($length)
    {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str_special_chars = '!@#$%&*?_,-_[]()';

        $max = strlen($str);
        if (!$length) {
            $length = 12;
        }

        $j = 0;
        do {
            if (++$j > 15) {
                show_error('User_model::generateStrongPassword has entered an infinite loop. Execution stopped');
            }

            $password = '';
            for ($i = 0; $i < $length; $i++) {
                $password .= $str{rand(0, $max - 1)};
            }

            $rand_special_char_position = rand(1, $length - 2);

            do {
                $rand_digit_position = rand(1, $length - 2);
            } while ($rand_digit_position == $rand_special_char_position);

            $password{$rand_digit_position} = rand(0, 9);
            $password{$rand_special_char_position} = $str_special_chars{rand(0, strlen($str_special_chars) - 1)};
        } while (!$this->isPassowrdStrongEnough($password));

        return $password;
    }

    /**
     * Generates an easy password of a given lenght
     *
     * TODO Move to password generator?
     *
     * @param int $length
     * @return string
     */
    public function generateEasyPassword($length)
    {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $max = strlen($str);
        if (!$length) {
            $length = 12;
        }

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $str{rand(0, $max - 1)};
        }

        return $password;
    }

    /**
     * Resets password for a given user
     *
     * @param int $user_id
     * @return bool
     */
    public function resetPasswordByUserId($user_id)
    {
        // Reading user info
        $user = $this->getById($user_id, 'user_email, display_name');
        if (!$user) {
            return false;
        }
        // Generate a new password
        $password = $this->generateAcceptablePassword($this->getMinimumAllowedPasswordLenght());

        // Try to change user password
        if ($this->changePasswordByUserId($user_id, $password, true)) {
            $site_name = $this->config->item('site_name');

            $this->load->library('EmailSender');
            $email_data = array(
                'display_name' => $user->display_name,
                'user_email' => $user->user_email,
                'base_url' => $this->input->is_cli_request() ? '' : base_url(), // TODO find nice solution for finding out the URL
                'password' => $password,
                'site_name' => $site_name
            );

            $email_language = $this->lang->getCurrentLanguage();

            // Formatting subject
            $email_subject = sprintf($this->lang->line('email_reset_password_subject'), $site_name);

            $success = $this->emailsender->sendSystemTemplate($user->user_email, $this->config->item('site_email'),
                $this->config->item('site_name'), $email_subject, 'new_password', $email_data, false, $email_language);

            // Logging
            if ($success) {
                LOGGER::info('Resetting user password', 'USER', $user_id);
            } else {
                // TODO Rollback
                LOGGER::error('User password reseted but email notification not sent', 'USER', $user_id);
            }

            return true;
        } else {
            LOGGER::error('Unable to reset password, probably the resetted pasword is to weak.', 'USER', $user_id);
        }

        return false;
    }

    /**
     * Returns the number of consecutive unsuccessful login attempts for a given account
     * This number is reseted once the user successfully logs in
     *
     * @param int $user_id
     * @return int
     */
    public function getNumberOfConsecutiveUnsuccessfullAuthorizationsByUserId($user_id)
    {
        $start_id = false;

        $row = $this->db->select('id')
            ->from($this->config->item('database_table_logs'))
            ->where('collection', 'LOGIN')
            ->where('user_id', $user_id)// User as we need a logged session
            ->where('level', Logger::MESSAGE_LEVEL_INFO)
            ->order_by('id DESC')
            ->limit(1)
            ->get()
            ->row();

        if ($row) {
            $start_id = $row->id;
        }


        $this->db->from($this->config->item('database_table_logs'))
            ->where('collection', 'LOGIN')
            ->where('resource_id', $user_id)// Resource as we dont need a logged session
            ->where('level', Logger::MESSAGE_LEVEL_WARNING);

        if ($start_id) {
            $this->db->where($this->config->item('database_table_logs') . '.id > ', $start_id);
        }

        return $this->db->count_all_results();
    }

    /**
     * Change password by user id
     *
     * @param int $user_id
     * @param string $password
     * @param bool $reset
     * @return bool
     */
    public function changePasswordByUserId($user_id, $password, $reset = false)
    {
        if (!$this->isPassowrdStrongEnough($password)) {
            // Redundant check but we need to protect the system
            return false;
        }

        if ($reset) {
            $password_last_changed_timestamp = null;
        } else {
            $password_last_changed_timestamp = utc_timestamp();
        }

        $hashing_salt = $this->generateSalt();
        $hashing_algorithm = $this->getDefaultHashingAlgorithm();
        $hashing_iterations = $hashing_iterations = $this->getDefaultNumberOfIterations();
        $password_encoded = $this->encodePassword($password, $hashing_salt, $hashing_algorithm, $hashing_iterations);

        $this->db->set('password', $password_encoded)
            ->set('password_last_changed_timestamp', $password_last_changed_timestamp)
            ->where('user_id', $user_id)
            ->set('hashing_salt', $hashing_salt)
            ->set('hashing_algorithm', $hashing_algorithm)
            ->set('hashing_iterations', $hashing_iterations);

        $success = $this->db->update($this->getTable());

        if ($success) {
            $this->Password_history_model->registerChange($user_id, $password_encoded, $hashing_salt, $hashing_algorithm,
                $hashing_iterations);
        }

        return $success;
    }

    /**
     * Inactivates an user
     *
     * @param int $user_id
     * @return bool
     */
    public function inactivateById($user_id)
    {
        LOGGER::info('Inactivating user', 'USER', $user_id);
        return $this->db->set('status', -1)
            ->where('user_id', $user_id)
            ->update($this->getTable());
    }

    /**
     * Activates an user
     *
     * @param int $user_id
     * @return bool
     */
    public function activateById($user_id)
    {
        LOGGER::info('Activating user', 'USER', $user_id);
        return $this->db->set('status', 1)
            ->where('user_id', $user_id)
            ->update($this->getTable());
    }

    /**
     * Locks the user account
     *
     * @param int $user_id
     * @return bool
     */
    public function lockById($user_id)
    {
        LOGGER::info('Locking user', 'USER', $user_id);
        return $this->db->set('is_locked', 1)
            ->where('user_id', $user_id)
            ->update($this->getTable());
    }

    /**
     * Unlocks the user account
     *
     * @param int $user_id
     * @return bool
     */
    public function unlockById($user_id)
    {
        Logger::info('Unlocking account', 'LOGIN', $user_id);
        return $this->db->set('is_locked', 0)
            ->where('user_id', $user_id)
            ->update($this->getTable());
    }

    /**
     * Updates an user
     *
     * @param int $user_id
     * @param string $display_name
     * @param bool $user_login
     * @param bool $group_ids
     * @param bool $password
     * @param null $is_root
     * @param array $data
     * @return bool
     */
    public function update($user_id, $display_name, $user_login = false, $group_ids = false, $password = false, $is_root = null, $data = array())
    {
        // Change user password if specified
        if ($password) {
            $this->changePasswordByUserId($user_id, $password);
        }

        // Setting extra accepted fields
        if (count($data)) {
            foreach ($data as $key => $value) {
                if (in_array($key, $this->getAcceptedPostFields())) {
                    $this->db->set($key, $value);
                }
            }
        }

        // Setting user login if the field is specified
        if ($user_login !== false) {
            $this->db->set('user_login', $user_login);
        }

        // Marking user as root if the field is specified
        if ($is_root !== null) {
            $this->db->set('is_root', $is_root ? 1 : 0);
        }

        // Executing query
        $success = $this->db->set('display_name', $display_name)
            ->where('user_id', $user_id)->update($this->getTable());

        // Assigning user to the groups
        if (is_array($group_ids)) {
            $this->bindUserToGroups($user_id, $group_ids);
        }

        // Logging status
        if ($success) {
            LOGGER::info('Updating user', 'USER', $user_id);
        }

        return $success;
    }

    /**
     * Binds user to groups, redoes all the existing bindings
     *
     * @param int $user_id
     * @param array $group_ids
     * @return bool
     */
    public function bindUserToGroups($user_id, $group_ids)
    {
        // Do the action only if the $group_ids is array
        // NOTE: If the array is empty, the user will be detached from the groups
        if (is_array($group_ids)) {
            // Cleaning up
            $this->db->where('user_id', $user_id)
                ->delete($this->config->item('database_table_user_to_group'));

            // Inserting every single group
            foreach ($group_ids as $group_id) {
                $this->db->set('group_id', $group_id)
                    ->set('user_id', $user_id)
                    ->insert($this->config->item('database_table_user_to_group'));
            }
        }

        // Recomputes groups label
        return $this->redoGroupsLabel($user_id);
    }

    /**
     * Returns user id by user email
     *
     * @param string $user_email
     * @return int|Boolean
     */
    public function getUserIdByEmail($user_email)
    {
        $row = $this->db->select('user_id')
            ->where('user_email', $user_email)
            ->get($this->getTable())
            ->row();

        if ($row) {
            return $row->user_id;
        }

        return false;
    }

    /**
     * Returns the list of ids of the groups the user belongs to
     *
     * @param int $user_id
     * @return array
     */
    public function getGroupsIdsByUserId($user_id)
    {
        $result = $this->db->select('group_id')
            ->where('user_id', $user_id)
            ->get($this->config->item('database_table_user_to_group'))
            ->result();

        $rows = array();
        foreach ($result as $line) {
            $rows[] = $line->group_id;
        }

        return $rows;
    }

    /**
     * Validates user by email
     *
     * @param int $user_id
     * @param string $password
     * @return User
     */
    public function validateByUserId($user_id, $password)
    {
        // Attempt to select user password metadata
        $row = $this->db->select('user_id, hashing_salt, hashing_algorithm, hashing_iterations')
            ->from($this->getTable())
            ->where('user_id', $user_id)
            ->where('status > 0')
            ->where('is_locked', 0)
            ->limit(1)
            ->get()
            ->row();

        // No row - wrong user id
        if (!$row) {
            return false;
        }

        $password_encoded = $this->encodePassword($password, $row->hashing_salt, $row->hashing_algorithm,
            $row->hashing_iterations);

        $row = $this->db->select('*')
            ->where('user_id', $row->user_id)
            ->where('password', $password_encoded)
            ->where('status > 0')
            ->where('is_locked', 0)
            ->limit(1)
            ->get($this->getTable())
            ->row();

        // If the user was selected, then return the user
        if ($row) {
            // When the password was encoded older algorithm, then
            if ($row->hashing_algorithm != $this->getDefaultHashingAlgorithm()) {
                $this->changePasswordByUserId($row->user_id, $password);
            }

            return $row;
        }

        return false;
    }

    /**
     * Validates user by email
     *
     * @param string $user_email
     * @param string $password
     * @return User
     */
    public function validateByEmail($user_email, $password)
    {
        // Attempt to select user password metadata
        $row = $this->db->select('user_id, hashing_salt, hashing_algorithm, hashing_iterations')
            ->from($this->getTable())
            ->where('user_email', $user_email)
            ->where('status > 0')
            ->where('is_locked', 0)
            ->limit(1)
            ->get()
            ->row();

        // No row - wrong user id
        if (!$row) {
            return false;
        }

        $password_encoded = $this->encodePassword($password, $row->hashing_salt, $row->hashing_algorithm, $row->hashing_iterations);

        $row = $this->db->select('*')
            ->where('user_id', $row->user_id)
            ->where('password', $password_encoded)
            ->where('status > 0')
            ->where('is_locked', 0)
            ->limit(1)
            ->get($this->getTable())
            ->row();

        if ($row) {
            // When the password was encoded older algorithm, then
            if ($row->hashing_algorithm != $this->getDefaultHashingAlgorithm()) {
                $this->changePasswordByUserId($row->user_id, $password);
            }

            return $row;
        }

        return false;
    }

    /**
     * Validates user by login
     *
     * @param string $user_login
     * @param string $password
     * @return User
     */
    public function validateByLogin($user_login, $password)
    {
        $row = $this->db->select('user_id, hashing_salt, hashing_algorithm, hashing_iterations')
            ->from($this->getTable())
            ->where('user_login', $user_login)
            ->where('status > 0')
            ->where('is_locked', 0)
            ->limit(1)
            ->get()
            ->row();

        if (!$row) {
            return false;
        }

        $password_encoded = $this->encodePassword($password, $row->hashing_salt, $row->hashing_algorithm, $row->hashing_iterations);

        // Selecting user using password
        $row = $this->db->select('*')
            ->where('user_id', $row->user_id)
            ->where('password', $password_encoded)
            ->where('status > 0')
            ->where('is_locked', 0)
            ->limit(1)
            ->get($this->getTable())
            ->row();

        if ($row) {
            // When the password was encoded older algorithm, then
            if ($row->hashing_algorithm != $this->getDefaultHashingAlgorithm()) {
                $this->changePasswordByUserId($row->user_id, $password);
            }

            return $row;
        }

        return false;
    }

    /**
     * Returns active user by user ID
     *
     * @param int $user_id
     * @return bool
     */
    public function getActiveById($user_id)
    {
        $row = $this->db->select('*')
            ->where('user_id', $user_id)
            ->where('status > 0')
            ->limit(1)
            ->get($this->getTable())
            ->row();

        if ($row) {
            return $row;
        }

        return false;
    }

    /**
     * Returns total number of users registered, used by CAS driver to determine whether registered user is the first user
     *
     * @return int
     */
    public function countAll()
    {
        return $this->db->count_all_results($this->getTable());
    }
}
