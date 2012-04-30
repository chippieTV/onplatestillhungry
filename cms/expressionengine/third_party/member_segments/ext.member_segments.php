<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Member Segments Extension
 *
 * @package     ExpressionEngine
 * @subpackage  Addons
 * @category    Extension
 * @author      Isaac Raway
 * @version     0.1
 * @link        http://metasushi.com
 */

class Member_segments_ext {
    
    public $settings        = array();
    public $description     = 'Get member info from segments';
    public $docs_url        = '';
    public $name            = 'Member Segments';
    public $settings_exist  = 'n';
    public $version         = '0.1';
    
    private $EE;
    
    /**
     * Constructor
     *
     * @param   mixed   Settings array or empty string if none exist.
     */
    public function __construct($settings = '')
    {
        $this->EE =& get_instance();
        $this->settings = $settings;
    }// ----------------------------------------------------------------------
    
    /**
     * Activate Extension
     *
     * This function enters the extension into the exp_extensions table
     *
     * @see http://codeigniter.com/user_guide/database/index.html for
     * more information on the db class.
     *
     * @return void
     */
    public function activate_extension()
    {
        // Setup custom settings in this array.
        $this->settings = array();
        
        $data = array(
            'class'     => __CLASS__,
            'method'    => 'sessions_end',
            'hook'      => 'sessions_end',
            'settings'  => serialize($this->settings),
            'version'   => $this->version,
            'enabled'   => 'y'
        );

        $this->EE->db->insert('extensions', $data);         
        
    }   

    // ----------------------------------------------------------------------
    
    /**
     * sessions_end
     *
     * @param 
     * @return 
     */
    public function sessions_end()
    {
        $fields = array('member_id', 'group_id', 'username', 'screen_name', 'email', 'url', 'location', 'bio',
                        'avatar_filename', 'avatar_width', 'avatar_height', 'photo_filename',
                        'photo_height', 'photo_width');
        
        for($i = 1; $i <= 10; $i++)
        {
            foreach($fields as $field)
            {
                $member_variables['segment_'.$i.'_'.$field] = '';
            }
        }
        
        // Fetch segments
        $segments = $this->EE->uri->segment_array();
        
        if(count($segments) > 0)
        {
            // Lowercase everything
            $segments = array_map('strtolower', $segments);
            
            // Search for members that have this member_id, username, email, or screen_name
            $members = $this->EE->db->where_in('username', $segments)
                                    ->or_where_in('member_id', $segments)
                                    ->or_where_in('email', $segments)
                                    ->or_where_in('screen_name', $segments)
                                    ->get('members');
            
            foreach($members->result() as $member)
            {
                for($i = 1; $i <= 10; $i++)
                {
                    if(!isset($segments[$i])) continue;
                    
                    if($segments[$i] == $member->username)
                    {
                        foreach($fields as $field)
                        {
                            $member_variables['segment_'.$i.'_'.$field] = $member->$field;
                        }
                    }
                }
            }
        }
        
        $this->EE->config->_global_vars = array_merge($member_variables, $this->EE->config->_global_vars);
        
    }

    // ----------------------------------------------------------------------

    /**
     * Disable Extension
     *
     * This method removes information from the exp_extensions table
     *
     * @return void
     */
    function disable_extension()
    {
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->delete('extensions');
    }

    // ----------------------------------------------------------------------

    /**
     * Update Extension
     *
     * This function performs any necessary db updates when the extension
     * page is visited
     *
     * @return  mixed   void on update / false if none
     */
    function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version)
        {
            return FALSE;
        }
    }   
    
    // ----------------------------------------------------------------------
}

/* End of file ext.member_segments.php */
/* Location: /system/expressionengine/third_party/member_segments/ext.member_segments.php */