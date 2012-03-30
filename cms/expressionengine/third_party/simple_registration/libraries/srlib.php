<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
if(!class_exists('Ab_LibBase')) { require_once 'ab/ab_libbase.php';	}

/**
 * EE Library - Simple Registration Library
 *
 * Developer: Bjorn Borresen / AddonBakery
 * Date: 01.04.11
 * Time: 14:11
 *  
 */
 
class Srlib extends Ab_LibBase {


    /**
     * Get an array of signup keys for the current site
     *
     * @param $trigger string get signup keys for a specific trigger
     * @return array
     */
    public function get_signup_keys($trigger=FALSE)
    {
        $keys = array();
        $where_arr = array('site_id' => $this->EE->config->item('site_id'));
        if($trigger)
        {
            $where_arr['trigger_event'] = $trigger;
        }
        $q = $this->EE->db->get_where('simple_registration_signup_keys', $where_arr);
        foreach($q->result() as $signup_key)
        {
            $keys[$signup_key->signup_key] = array('signup_key_id' => $signup_key->simple_registration_signup_key_id, 'member_group_id' => $signup_key->member_group_id, 'trigger_event' => $signup_key->trigger_event);
        }
        return $keys;
    }

    /**
     * Get information about a signup key
     *
     * @param  $signup_key
     * @return array of info or false if signup key could not be found
     */
    public function get_signup_key_info($signup_key)
    {
        $this->EE->db->select('simple_registration_signup_key_id as signup_key_id, site_id, signup_key, trigger_event, member_group_id');
        $q = $this->EE->db->get_where('simple_registration_signup_keys', array('site_id' => $this->EE->config->item('site_id'), 'signup_key' => $signup_key));
        if($q->num_rows() > 0)
        {
            $arr = $q->result_array();
            return $arr[0];
        }
        else
        {
            return FALSE;
        }
    }
}