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
 * Password history model
 *
 * @since 1.0.0
 */
class Password_history_model extends Generic_model
{
    /**
     * Adds password history entry.
     *
     * @param $user_id
     * @param $password_encoded
     * @param $hashing_salt
     * @param $hashing_algorithm
     * @param $hasing_iterations
     * @return bool
     */
    public function registerChange($user_id, $password_encoded, $hashing_salt, $hashing_algorithm, $hasing_iterations)
    {
        $number_of_entries_to_store = 5;

        $this->db->trans_start();
        $success = $this->db->set('user_id', $user_id)
            ->set('password_encoded', $password_encoded)
            ->set('hashing_salt', $hashing_salt)
            ->set('hashing_algorithm', $hashing_algorithm)
            ->set('hashing_iterations', $hasing_iterations)
            ->set('changed_datetime', utc_timestamp())
            ->insert('cms_password_history');

        if ($success) {
            $subquery_item = $this->db->select('id')
                ->from('cms_password_history')
                ->where('user_id', $user_id)
                ->order_by('changed_datetime', 'DESC')
                ->limit(1, $number_of_entries_to_store)
                ->get()->row();

            if ($subquery_item) {
                $this->db->where('user_id', $user_id)
                    ->where('id <= ' . $subquery_item->id, NULL, FALSE)
                    ->delete('cms_password_history');
            }
        }

        return $this->db->trans_commit();
    }

    /**
     * Tells whether the given password was used for given user.
     *
     * @param $user_id
     * @param $password
     * @return bool
     */
    public function getPasswordLastUsedDatetime($user_id, $password)
    {
        $result = $this->db->select('*')
            ->from('cms_password_history')
            ->where('user_id', $user_id)
            ->get()
            ->result();

        foreach ($result as $row) {
            $password_encoded = $this->User_model->encodePassword($password, $row->hashing_salt,
                $row->hashing_algorithm, $row->hashing_iterations);

            if ($password_encoded == $row->password_encoded) {
                return $row->changed_datetime;
            }
        }

        return false;
    }
}