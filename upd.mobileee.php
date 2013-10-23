<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * MobileEE Module Install/Update File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Chris Lock
 * @link		http://bright.is/
 */

class Mobileee_upd {

	/**
	 * @var object
	 */
	private $EE;

	/**
	 * @var string
	 */
	public $version = '1.0';

	/**
	 * @var string
	 */
	public $module = 'Mobileee';
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	/**
	 * Installation routine
	 * @return bool true
	 */
	public function install()
	{
		$this->EE->load->dbforge();

		$data = array(
			'module_name' => $this->module,
			'module_version' => $this->version,
			'has_cp_backend' => 'n',
			'has_publish_fields' => 'n'
		);

		$this->EE->db->insert('modules', $data);

		$actions = array(
			'set_variable',
			'unset_variable'
		);

		foreach ($actions as $action) {
			$data = array(
				'class' => $this->module,
				'method' => $action
			);

			$this->EE->db->insert('actions', $data);
		}
		
		return true;
	}
	
	/**
	 * Uninstallation routine
	 * @return bool true
	 */
	public function uninstall()
	{

		$this->EE->load->dbforge();

		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where(
			'modules',
			array('module_name' => $this->module)
		);

		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');

		$this->EE->db->where('module_name', $this->module);
		$this->EE->db->delete('modules');

		$this->EE->db->where('class', $this->module);
		$this->EE->db->delete('actions');

		return true;
	}
	
	/**
	 * Update routine
	 * @param string $current The version to upgrade to.
	 * @return bool true
	 */
	public function update($current = '')
	{
		return true;
	}
	
}
/* End of file upd.mobileee.php */
/* Location: /system/expressionengine/third_party/mobileee/upd.mobileee.php */