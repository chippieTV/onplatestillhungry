<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Images Local location
 *
 * @package			DevDemon_ChannelImages
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/channel_images/
 */
class CI_Location_local extends Image_Location
{

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct($settings=array())
	{
		parent::__construct();

		$this->lsettings = $settings;

	}

	// ********************************************************************************* //

	public function create_dir($dir)
	{
		// Did we store a location?
		if (isset($this->lsettings['location']) == FALSE OR $this->lsettings['location'] == FALSE)
		{
			return FALSE;
		}

		$loc = $this->get_location_prefs($this->lsettings['location']);

		// We have a correct location?
		if ($loc == FALSE)
		{
			return FALSE;
		}

		// Mkdir & Chmod
		@mkdir($loc['path'] . $dir);
		@chmod($loc['path'] . $dir, 0777);

		return TRUE;
	}

	// ********************************************************************************* //

	public function delete_dir($dir)
	{
		$this->EE->load->helper('file');

		// Did we store a location?
		if (isset($this->lsettings['location']) == FALSE OR $this->lsettings['location'] == FALSE)
		{
			return FALSE;
		}

		$loc = $this->get_location_prefs($this->lsettings['location']);

		// We have a correct location?
		if ($loc == FALSE)
		{
			return FALSE;
		}

		// Delete them all!
		@delete_files($loc['path'] . $dir, TRUE);
		@rmdir($loc['path'] . $dir);

		return TRUE;
	}

	// ********************************************************************************* //
	public function upload_file($source_file, $dest_filename, $dest_folder)
	{
		$loc = $this->get_location_prefs($this->lsettings['location']);

		// Move file
		if (copy($source_file, $loc['path'] . $dest_folder . '/' . $dest_filename) === FALSE)
    	{
    		$o['body'] = $this->EE->lang->line('ci:file_upload_error');
	   		exit( $this->EE->image_helper->generate_json($o) );
    	}
    	else
    	{
    		return TRUE;
    	}
	}

	// ********************************************************************************* //

	public function download_file($dir, $filename, $dest_folder)
	{
		$loc = $this->get_location_prefs($this->lsettings['location']);

		copy($loc['path'].$dir.'/'.$filename, $dest_folder.$filename);
		return TRUE;
	}

	// ********************************************************************************* //

	public function delete_file($dir, $filename)
	{
		$loc = $this->get_location_prefs($this->lsettings['location']);

		@unlink($loc['path'] . $dir . '/' . $filename);

		return FALSE;
	}

	// ********************************************************************************* //

	public function parse_image_url($dir, $filename)
	{
		$loc = $this->get_location_prefs($this->lsettings['location']);

		// Does it starts with / ?
		if (strpos($loc['url'], '/') === 0)
		{
			// This may fail if using MSM
			$loc['url'] = 'http://' .$_SERVER['HTTP_HOST'] . '/' . $loc['url'];
			$loc['url'] = $this->EE->functions->remove_double_slashes($loc['url']); // Remove double slashes
		}

		// Is SSL?
		if ($this->EE->image_helper->is_ssl() == TRUE)
		{
			$loc['url'] = str_replace('http://', 'https://', $loc['url']);
		}

		$final = $loc['url'] . $dir . '/' . $filename;

		// -----------------------------------------
		// Local Spefic Parameters?
		// -----------------------------------------
		if (isset($this->EE->TMPL) == TRUE)
		{
			// Kill the domain name?
			if ($this->EE->TMPL->fetch_param('local:remove_domain') == 'yes')
			{
				$url = parse_url($final);
				$final = $url['path'];
			}
		}

		return $final;
	}

	// ********************************************************************************* //

	public function test_location()
	{
		// What is our location path?
		$loc = $this->get_location_prefs($this->lsettings['location']);
		$dir = $loc['path'];

		$o = '<strong style="color:orange">PATH:</strong> ' . $dir . '<br />';

		// Check for Safe Mode?
		$safemode = strtolower(@ini_get('safe_mode'));
		if ($safemode == 'on' || $safemode == 'yes' || $safemode == 'true' ||  $safemode == 1)	$o .= "PHP Safe Mode (OFF): <span style='color:red'>Failed</span> <br>";
		else $o .= "PHP Safe Mode (OFF): <span style='color:green'>Passed</span> <br>";

		// Is DIR?
		if (is_dir($dir) === TRUE)	$o .= "Is Dir: <span style='color:green'>Passed</span> <br>";
		else $o .= "Is Dir: <span style='color:red'>Failed</span> <br>";

		// Is READABLE?
		if (is_readable($dir) === TRUE) $o .= "Is Readable: <span style='color:green'>Passed</span> <br>";
		else $o .= "Is Readable: Failed<br>";

		// Is WRITABLE
		if (is_writable($dir) === TRUE) $o .= "Is Writable: <span style='color:green'>Passed</span> <br>";
		else $o .= "Is Writable: <span style='color:red'>Failed</span> <br>";

		// CREATE TEST FILE
		$file = uniqid(mt_rand()).'.tmp';
		if (@touch($dir.$file) === TRUE) $o .= "Create Test File: <span style='color:green'>Passed</span> <br>";
		else $o .= "Create Test File: <span style='color:red'>Failed</span> <br>";

		// DELETE TEST FILE
		if (@unlink($dir.$file) === TRUE) $o .= "Delete Test File: <span style='color:green'>Passed</span> <br>";
		else $o .= "Delete Test File: <span style='color:red'>Failed</span> <br>";

		// CREATE TEST DIR
		$tempdir = 'temp_' . $this->EE->localize->now;
		if (@mkdir($dir.$tempdir) === TRUE) $o .= "Create Test DIR: <span style='color:green'>Passed</span> <br>";
		else $o .= "Create Test DIR: <span style='color:red'>Failed</span> <br>";

		// RENAME TEST DIR
		if (@rename($dir.$tempdir, $dir.$tempdir.'temp') === TRUE) $o .= "Rename Test DIR: <span style='color:green'>Passed</span> <br>";
		else $o .= "Rename Test DIR: <span style='color:red'>Failed</span> <br>";

		// DELETE TEST DIR
		if (@rmdir($dir.$tempdir.'temp') === TRUE) $o .= "Delete Test DIR: <span style='color:green'>Passed</span> <br>";
		else $o .= "Delete Test DIR: <span style='color:red'>Failed</span> <br>";

		$o .= "<br /> Even if all tests PASS, uploading can still<br /> fail due Apache/htaccess misconfiguration";

		return $o;
	}

	// ********************************************************************************* //

	/**
	 * Get Upload Prefs
	 *
	 * @param int $location_id
	 * @access public
	 * @return array - Location settings
	 */
	public function get_location_prefs($location_id)
	{
		$location = array();

		if (isset($this->EE->session->cache['UploadPrefs'][$location_id]) == FALSE)
		{
			$query = $this->EE->db->select('server_path, url')->from('exp_upload_prefs')->where('id', $location_id)->get();

			if ($query->num_rows() == 0)
			{
				$query->free_result();
				return FALSE;
			}

			$location = array('path' => $query->row('server_path'), 'url' => $query->row('url'));

			$this->EE->session->cache['UploadPrefs'][$location_id] = $location;

			$query->free_result();
		}
		else
		{
			$location = $this->EE->session->cache['UploadPrefs'][$location_id];
		}

		// Relative path?
		if (substr($location['path'], 0, 1) != "/")
		{
			if (REQ == 'CP')
			{
				// Store it
				$first = getcwd();



				$theme_path = $this->EE->config->item('theme_folder_path');
				if (realpath($theme_path) != FALSE)
				{
					// Resolve it
					$theme_path = realpath($theme_path);

					// Change to that dir
					chdir($theme_path);

					// Go on UP!
					chdir('../');

					$store = getcwd();
					//exit($store);

					$location['path'] = realpath($location['path']) . '/';
				}

				chdir($first);
			}
			else
			{
				// (try) to turn relative path into absolute path.
				if(realpath(FCPATH . $location['path']) != null) {
					$location['path'] = realpath(FCPATH .  $location['path']) . '/';
				} elseif(realpath(FCPATH . SYSDIR . '/' . $location['path']) != null) {
					$location['path'] = realpath(FCPATH . SYSDIR . '/' . $location['path']) . '/';
				}
			}
		}

		// Need last slash!
		if (substr($location['path'], -1, 1) != '/')
		{
			$location['path'] . '/';
		}

		return $location;
	}

	// ********************************************************************************* //
}

/* End of file local.php */
/* Location: ./system/expressionengine/third_party/channel_images/locations/local/local.php */