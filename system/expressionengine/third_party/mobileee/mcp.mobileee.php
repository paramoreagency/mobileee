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
 * MobileEE Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Chris Lock
 * @link		http://bright.is/
 */

class Mobileee_mcp {
	
	/**
	 * @var object
	*/
	private $EE;
	
	/**
	 * Constructor
	 * @return void
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}
}
/* End of file mcp.mobileee.php */
/* Location: /system/expressionengine/third_party/mobileee/mcp.mobileee.php */