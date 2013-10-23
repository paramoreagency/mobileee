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
 * Mobileee Module
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Chris Lock
 * @link		http://bright.is/
 */

class Mobileee {

	/**
	 * @var object
	*/
	private $EE;

	/**
	 * @var string
	 */
	private $set_variable_action_url_base;

	/**
	 * Constructor
	 * @return void
	 */
	public function __construct()
	{
		$this->EE =& get_instance();

		$this->set_variable_action_url_base = 
			$this->EE->functions->fetch_site_index(0, 0) . 
			'?ACT=' . 
			$this->EE->functions->fetch_action_id(
				'Mobileee',
				'set_variable'
			) . 
			'&variable=%s' . 
			'&value=%s' . 
			'&url=%s';
	}

	/**
	 * Returns URL to set MobileEE is_mobile to false
	 * then redirect to given or current url.
	 * @return string
	 */
	public function desktop_url()
	{
		return sprintf(
			$this->set_variable_action_url_base,
			'is_mobile',
			'false',
			$this->get_url_encoded_redirect_url()
		);
	}

	/**
	 * Gets redirect URL or if unset current URL.
	 * @return string
	 */
	private function get_url_encoded_redirect_url()
	{
		return urlencode(
			$this->EE->TMPL->fetch_param(
				'url',
				$this->EE->config->item('site_url') . $this->EE->uri->uri_string()
			)
		);
	}

	/**
	 * Returns URL to set MobileEE is_mobile to true
	 * then redirect to given or current url.
	 * @return string
	 */
	public function mobile_url()
	{
		return sprintf(
			$this->set_variable_action_url_base,
			'is_mobile',
			'true',
			$this->get_url_encoded_redirect_url()
		);
	}

	/**
	 * Returns URL to set MobileEE variable
	 * then redirect to given or current url.
	 * @return string
	 */
	public function set_variable_url()
	{
		return sprintf(
			$this->set_variable_action_url_base,
			$this->EE->TMPL->fetch_param('variable'),
			$this->EE->TMPL->fetch_param('value'),
			$this->get_url_encoded_redirect_url()
		);
	}

	/**
	 * Returns URL to unset MobileEE variable
	 * then redirect to given or current url.
	 * @return string
	 */
	public function unset_variable_url()
	{
		return sprintf(
			$this->set_variable_action_url_base,
			$this->EE->TMPL->fetch_param('variable'),
			'',
			$this->get_url_encoded_redirect_url()
		);
	}

	/**
	 * Sets MobileEE variable
	 * then redirects to given or current url.
	 * @return void
	 */
	public function set_variable()
	{
		if (true !== $this->EE->extensions->active_hook('set_mobileee_variable')) {
			$this->log_exception('Please enable MobileEE extension before using module');

			return;
		}

		$this->EE->extensions->call('set_mobileee_variable', $_GET['variable'], $_GET['value']);
		$this->EE->functions->redirect($_GET['url']);
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
}
/* End of file pi.mobileee.php */
/* Location: /system/expressionengine/third_party/mobileee/pi.mobileee.php */