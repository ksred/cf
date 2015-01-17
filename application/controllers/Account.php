<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php'); 

class Account extends REST_Controller {

	public function __construct () {
		parent::__construct();
		$this->load->model('Model_account');
		$this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
	}

	/*
	* Retrieve user settings
	*
	* @param	string		uid		-		uid of user settings to get
	* @param	int			db		-		force retrieval of settings from database, default FALSE
	*
	* @return	response	json	-		json response success/error
	*
	*/
	public function settings_get($uid, $db = 0)
	{
		if ($db != 0)
		{
			// Check for settings in cache
			$map = $this->get_user_map($uid);
			if ($map)
			{
				$this->response(array('success' => 1, 'message' => $map), 200);
			}
		}

		$settings = $this->Model_account->get_settings($uid);
		if (empty($settings))
		{
			$this->response(array('success' => 0, 'message' => 'Account settings not found'), 400);
		}
		$this->response(array('success' => 1, 'message' => $settings), 200);
	}

	/*
	* Set user settings
	*
	* @param	string		uid				-		uid of user settings to get
	* @param	int			notify_send		-		notify message sent
	* @param	int			notify_receive	-		notify message received 
	* @param	int			notify_deliver	-		notify message delivered
	*
	* @return	response	json	-		json response success/error
	*
	*/
	public function settings_post()
	{
		$uid = $this->input->post('uid');
		$notify_send = $this->input->post('notify_send');
		$notify_receive = $this->input->post('notify_receive');
		$notify_deliver = $this->input->post('notify_deliver');

		if (!isset($uid) || !isset($notify_send) || !isset($notify_receive) || !isset($notify_deliver))
		{
			$this->response(array('success' => 0, 'message' => 'Not all required fields present'), 400);
		}

		// Check if settings already exist
		$settings = $this->Model_account->get_settings($uid);
		if (!empty($settings))
		{
			$this->response(array('success' => 0, 'message' => 'Settings already exist for user'), 400);
		}

		$data = array(
			'uid'				=>		$uid,
			'notify_send'		=>		$notify_send,
			'notify_receive'	=>		$notify_receive,
			'notify_deliver'	=>		$notify_deliver,
		);

		$saved = $this->Model_account->save_settings($data);
		if (!$saved)
		{
			$this->response(array('success' => 0, 'message' => 'Account settings could not be saved'), 400);
		}

		// Add to memcached/redis
		$map = $notify_send.$notify_receive.$notify_deliver;
		$map_set = $this->set_user_map($uid, $map);
		if (!$map_set)
		{
			// We do not want to return a hard error here, but the user must be notified
			$this->response(array('success' => 1, 'message' => 'Settings successfully saved, could not set user map'), 200);
		}

		$this->response(array('success' => 1, 'message' => 'Settings successfully saved'), 200);
	}

	/*
	* Update user settings
	*
	* @param	string		uid				-		uid of user settings to get
	* @param	int			notify_send		-		notify message sent
	* @param	int			notify_receive	-		notify message received 
	* @param	int			notify_deliver	-		notify message delivered
	*
	* @return	response	json	-		json response success/error
	*
	*/
	public function settings_put($uid, $notify_send, $notify_receive, $notify_deliver)
	{
		if (!isset($uid) || !isset($notify_send) || !isset($notify_receive) || !isset($notify_deliver))
		{
			$this->response(array('success' => 0, 'message' => 'Not all required fields present'), 400);
		}

		$data = array(
			'notify_send'		=>		$notify_send,
			'notify_receive'	=>		$notify_receive,
			'notify_deliver'	=>		$notify_deliver,
		);

		$settings = $this->Model_account->update_settings($uid, $data);
		if (empty($settings))
		{
			$this->response(array('success' => 0, 'message' => 'Account settings not found'), 400);
		}

		// Add to memcached/redis
		$map = $notify_send.$notify_receive.$notify_deliver;
		$map_set = $this->set_user_map($uid, $map);
		if (!$map_set)
		{
			// We do not want to return a hard error here, but the user must be notified
			$this->response(array('success' => 1, 'message' => 'Settings successfully saved, could not set user map'), 200);
		}

		$this->response(array('success' => 1, 'message' => $settings), 200);
	}

	/*
	* Delete user settings
	*
	* @param	string		uid				-		uid of user settings to get
	*
	* @return	response	json	-		json response success/error
	*
	*/
	public function settings_delete($uid)
	{
		$settings = $this->Model_account->get_settings($uid);
		if (empty($settings))
		{
			$this->response(array('success' => 0, 'message' => 'Account settings not found'), 400);
		}

		$delete = $this->Model_account->delete_settings($uid);
		if (!$settings)
		{
			$this->response(array('success' => 0, 'message' => 'Account settings not deleted'), 400);
		}

		$this->response(array('success' => 1, 'message' => 'Account settings deleted'), 200);
	}

	/*
	* Set user settings to map in memcached
	* NOTE: This will be abstracted into a library in production
	*
	* @param	string		key		-		key of settings, user uid
	* @param	string		value	-		value of settings, send.receive.deliver - 101
	*
	* @return	response	json	-		json response success/error
	*
	*/
	private function set_user_map ($key, $value)
	{
		return $this->cache->save($key, $value, (60 * 60 * 24 * 30)); // One month validity
	}

	/*
	* Retrieve user settings from map in memcache
	* NOTE: This will be abstracted into a library in production
	*
	* @param	string		key		-		key to get settings for, user uid
	*
	* @return	response	json	-		json response success/error
	*
	*/
	private function get_user_map ($key)
	{
		return $this->cache->get($key);
	}
}

/* End of file Account.php */
/* Location: ./application/controllers/Account.php */
