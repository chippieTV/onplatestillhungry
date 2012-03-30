<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!class_exists('Ab_UpdBase')) {
    require_once PATH_THIRD.'simple_registration/libraries/ab/ab_updbase.php';
}

require_once PATH_THIRD.'simple_registration/const.simple_registration.php';

/**
 * Let's you put a registration form anywhere
 *
 * @package		Simple_registration
 * @subpackage	ThirdParty
 * @category	Modules
 * @author		bjorn
 * @link		http://www.addonbakery.com
 */
class Simple_registration_upd extends Ab_UpdBase
{
	var $version     = Const_Simple_Registration::CURRENT_VERSION;
	var $module_name = "Simple_registration";
	    
    function __construct( $switch = TRUE )
    { 
		parent::__construct();
    } 

    /**
     * Installer for the Simple_registration module
     */
    function install() 
	{
        $this->EE->load->dbforge();

        $data = array(
			'module_name' 	 => $this->module_name,
			'module_version' => $this->version,
			'has_cp_backend' => 'y'
		);

		$this->EE->db->insert('modules', $data);
        $this->EE->load->library('abprefs', array('module_name' => $this->module_name));
        $this->install_preferences();
        $this->install_signupkeys_tables();

		return TRUE;
	}

    /**
     * INSTALL preferences
     *
     * @param null $prefs_arr
     */
    private function install_preferences($prefs_arr=NULL)
    {
        if($prefs_arr == NULL)
        {
            $prefs_arr = Const_Simple_Registration::$default_preferences;
        }

        $this->EE->load->library('abprefs', array('module_name' => $this->module_name));
        $this->EE->abprefs->install($prefs_arr, 1);
        if($this->EE->config->item('site_id') != 1) // if we're not currently on site 1 also set settings on this site
        {
            $this->EE->abprefs->init_site($prefs_arr, $this->EE->config->item('site_id'));
        }
    }

    private function install_signupkeys_tables()
    {
        $this->EE->load->dbforge();
        $simple_registration_signup_keys_fields = array(
            'simple_registration_signup_key_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'unsigned' => TRUE,
                'auto_increment' => TRUE),
            'site_id' => array('type' => 'int',
                        'constraint'	=> '10',
                         'null' => FALSE),
            'signup_key' => array(
                'type' => 'varchar',
                'constraint' => '255',
                'null' => FALSE,),

            'trigger_event' => array(
                'type' => 'varchar',
                'constraint' => '255',
                'null' => FALSE),

            'member_group_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'null' => FALSE,),
        );

        $this->EE->dbforge->add_field($simple_registration_signup_keys_fields);
        $this->EE->dbforge->add_key('simple_registration_signup_key_id', TRUE);
        $this->EE->dbforge->create_table('simple_registration_signup_keys');

        $simple_registration_pending_events_fields = array(
            'simple_registration_pending_event_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,),
            'member_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'null' => FALSE,),
            'signup_key_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'null' => FALSE,),
        );

        $this->EE->dbforge->add_field($simple_registration_pending_events_fields);
        $this->EE->dbforge->add_key('simple_registration_pending_event_id', TRUE);
        $this->EE->dbforge->create_table('simple_registration_pending_events');
    }



	
	/**
	 * Uninstall the Simple_registration module
	 */
	function uninstall() 
	{
		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => $this->module_name));
		
		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');
		
		$this->EE->db->where('module_name', $this->module_name);
		$this->EE->db->delete('modules');
		
		$this->EE->db->where('class', $this->module_name);
		$this->EE->db->delete('actions');
		
		$this->EE->db->where('class', $this->module_name.'_mcp');
		$this->EE->db->delete('actions');

        $this->EE->load->library('abprefs', array('module_name' => $this->module_name));
        $this->EE->abprefs->uninstall();

        $this->EE->dbforge->drop_table('simple_registration_signup_keys');
        $this->EE->dbforge->drop_table('simple_registration_pending_events');
                        										
		return TRUE;
	}
	
	/**
	 * Update the Simple_registration module
	 * 
	 * @param $current current version number
	 * @return boolean indicating whether or not the module was updated 
	 */
	
	function update($current = '')
	{
        if ($current == $this->version)
        {
            return FALSE;
        }

        if ($current < '1.2')
        {
            $this->EE->load->library('abprefs', array('module_name' => $this->module_name));
            // we have old serialized preferences, MSN support was not avail. at that time so only need to get one
            $pq = $this->EE->db->get_where('simple_registration_prefs', array('site_id' => '1'));
            $pq_arr = $pq->result_array();
            $current_preferences = $this->_unserialize($pq_arr[0]['preferences']);

            $this->EE->load->dbforge();
            $this->EE->dbforge->drop_table('simple_registration_prefs');
            $prefs_arr = Const_Simple_Registration::$default_preferences;
            foreach($prefs_arr as $pref_key => $pref_value)
            {
                if(isset($current_preferences[$pref_key]))
                {
                    $pref_value['value'] = $current_preferences[$pref_key];
                }

                $prefs_arr[$pref_key] = $pref_value;
            }


            $this->EE->abprefs->install($prefs_arr, 1);


            // new table in this version as well
            $this->install_signupkeys_tables();


            // another hook was added in this version
            $this->EE->db->insert('extensions', array(
                'class' => 'Simple_registration_ext',
                'method' => 'on_member_register_validate_members',
                'hook' => 'member_register_validate_members',
                'settings' => '',
                'priority' => '10',
                'version' => Const_Simple_Registration::CURRENT_VERSION,
                'enabled' => 'y'));
        }

        if($current < '1.2.2')
        {
            $this->EE->db->query("ALTER TABLE `".$this->EE->db->dbprefix("simple_registration_signup_keys")."` ADD `trigger_event` VARCHAR( 255 ) NOT NULL");
        }

        return TRUE;
	}

    private function _unserialize($data)
	{
		$data = @unserialize(strip_slashes($data));

		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				$data[$key] = str_replace('{{slash}}', '\\', $val);
			}

			return $data;
		}

		return str_replace('{{slash}}', '\\', $data);
	}

}

/* End of file upd.simple_registration.php */ 
/* Location: ./system/expressionengine/third_party/simple_registration/upd.simple_registration.php */ 