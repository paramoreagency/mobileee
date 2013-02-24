<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

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
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Chris Lock
 * @link		http://bright.is/
 */

class Mobileee_ext {

	public $settings = array();
	public $description = 'Creates global variables for is_mobile, is_phone, is_iphone, is_tablet, & is_ipad and overriders them with cookies based on user prefernces set through the module.';
	public $docs_url = 'https://github.com/chris-lock/mobileee';
	public $name = 'MobileEE';
	public $settings_exist = 'y';
	public $version	= '1.0';
	
	/**
	 * @var object
	 */
	private $EE;

	/**
	 * @var int
	 */
	private $site_id;

	/**
	 * @var array
	 */
	private $mobileee_variables;

	/**
	 * @var string
	 */
	private $site_id_cookie_name;

	/**
	 * @var string
	 */
	private $mobileee_site_cookie_prefix;

	/**
	 * @var	int
	 */
	const COOKIE_TIMEOUT_DEFAULT = 604800;
	
	/**
	 * Constructor
	 * @param mixed	Settings array or empty string if none exist.
	 * @return void
	 */
	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;

		$this->mobileee_variables = array(
			'is_mobile',
			'is_phone',
			'is_iphone',
			'is_tablet',
			'is_ipad'
		);
		$this->site_id = $this->EE->config->item('site_id');
		$this->site_id_cookie_name = 'site_' . $this->site_id;
		$this->mobileee_site_cookie_prefix = 
			'mobileee_' . 
			$this->site_id . 
			'_';
	}
	
	/**
	 * Activate Extension
	 * This function enters the extension into the exp_extensions table
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 * @return void
	 */
	public function activate_extension()
	{
		$hooks = array(
			'sessions_start' => 'sessions_start',
			'set_mobileee_variable' => 'set_mobileee_variable'
		);

		foreach ($hooks AS $hook => $method) {
			$data = array(
				'class' => __CLASS__,
				'method' => $method,
				'hook' => $hook,
				'priority' => 10,
				'version' => $this->version,
				'enabled' => 'y',
				'settings' => serialize($this->settings)
			);

			$this->EE->db->insert('exp_extensions', $data);
		}			
	}

	/**
	 * Disable Extension
	 * This method removes information from the exp_extensions table
	 * @return void
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

	/**
	 * Update Extension
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 * @return mixed void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
			return true;
	}

	/**
	 * Settings Form
	 * @param array $current_settings
	 * @return void
	 */
	function settings_form($current_settings)
	{
		$this->EE->load->helper('form');
		$this->EE->load->library('table');

		$vars = array();
		$cookie_timeout = $this->get_cookie_timeout($current_settings);
		$vars['settings'] = array(
			'cookie_timeout' => form_input(
				$this->site_id_cookie_name.'[cookie_timeout]',
				$cookie_timeout
			)
		);

		return $this->EE->load->view('settings_form', $vars, TRUE);
	}

	/**
	 * Gets cookie timeout
	 * @param array $settings_array
	 * @return int
	 */
	private function get_cookie_timeout($settings_array)
	{
		return (isset($settings_array[$this->site_id_cookie_name]['cookie_timeout']))
			? $settings_array[$this->site_id_cookie_name]['cookie_timeout'] 
			: self::COOKIE_TIMEOUT_DEFAULT;
	}

	/**
	 * Save Settings
	 * @return void
	 */
	function save_settings()
	{
		if (empty($_POST))
			show_error($this->EE->lang->line('unauthorized_access'));

		unset($_POST['submit']);

		$this->EE->lang->loadfile('mobileee');
		$cookie_timeout = $_POST[$this->site_id_cookie_name]['cookie_timeout'];
		$flashdata_type = 'message_failure';
		$flashdata_message = $this->EE->lang->line('cookie_timeout_error');

		if (is_numeric($cookie_timeout) && $cookie_timeout >= 0) {
			$flashdata_type = 'message_success';
			$flashdata_message = $this->EE->lang->line('settings_updated');

			$this->EE->db->where('class', __CLASS__);
			$this->EE->db->update(
				'extensions',
				array(
					'settings' => serialize($_POST)
				)
			);
		}

		$this->EE->session->set_flashdata(
			$flashdata_type,
			$flashdata_message
		);
		$this->EE->functions->redirect(
			BASE . 
			AMP . 
			'C=addons_extensions' . 
			AMP . 
			'M=extension_settings' . 
			AMP . 
			'file=mobileee'
		);
	}

	/**
	 * @param object $session
	 * @return void
	 */
	function sessions_start($session)
	{
		if (defined('REQ') AND REQ == 'CP') return;

		foreach($this->mobileee_variables as $mobileee_variable)
			$this->EE->config->_global_vars[$mobileee_variable] =
				$this->get_mobileee_variable($mobileee_variable);
	}

	/**
	 * @param string $variable
	 * @throws mobileee_exception If [this condition is met]
	 * @return bool
	 */
	protected function get_mobileee_variable($variable)
	{	
		$variable_cookie = $this->mobileee_site_cookie_prefix . $variable;

		if (isset($_COOKIE[$variable_cookie]))
			return $this->convert_cookie_value_to_boolean(
				$_COOKIE[$variable_cookie]
			);

		$this->EE->load->library('mobilee_detect_library');
		$detect_library = $this->EE->mobilee_detect_library;

		if ( ! method_exists($detect_library, $variable)) {
			$this->log_exception(
				'mobilee_detect_library does not have a method named ' . $variable
			);

			return false;
		}

		return $detect_library->$variable($_SERVER['HTTP_USER_AGENT']);
	}

	/**
	 * Converts a cookie value to a boolean
	 * @param string $param Value to convert
	 * @return boolean
	 */
	private function convert_cookie_value_to_boolean($value)
	{
		$value = strtolower($value);

		return ($value == 'false' 
			OR $value == 'no' 
			OR $value == '0' 
			OR $value == ''
		)
			? FALSE : TRUE;
	}

	/**
	 * Logs the exception to codebase or the developer log
	 * @param string $message
	 * @return void
	 */
	private function log_exception($message)
	{
		if (true === $this->EE->extensions->active_hook('eeexception_send_string')) {
			$this->EE->extensions->call('eeexception_send_string', $message);
		} else {
			$this->EE->load->library('logger');
			$this->EE->logger->developer($message);
		}
	}

	/**
	 * Sets MobileEE variable
	 * @param string $variable
	 * @param string $value
	 * @return void
	 */
	function set_mobileee_variable($variable, $value)
	{
		setcookie(
			$this->mobileee_site_cookie_prefix . $variable,
			$value,
			time() + $this->get_cookie_timeout($this->settings)
		);
	}
}
/* End of file ext.mobileee.php */
/* Location: /system/expressionengine/third_party/mobileee/ext.mobileee.php */