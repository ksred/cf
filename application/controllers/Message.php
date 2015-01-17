<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'libraries/REST_Controller.php'); 

class Message extends REST_Controller {

	public function __construct () {
		parent::__construct();
		$this->load->model('Model_messages');
		$this->load->model('Model_account');
		$this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
	}

	/*
	* Send a new message 
	*
	* @param	string		from	-		Sender uid (In production this would be set using header or verified to prevent spoofing)
	* @param	string		to		-		Recipient uid
	* @param	string		message	-		Message to send
	*
	* @return	response	json	-		json response success/error
	*
	*/
	public function index_post ()
	{
		$from = $this->input->post('from');
		$to = $this->input->post('to');
		$message = $this->input->post('message');

		if (empty($to) || empty($from) || empty($message))
		{
			$this->response(array('success' => 0, 'message' => 'Not all required fields present'), 400);
		}

		$data = array(
			'to'		=>		$to,
			'from'		=>		$from,
			'body'		=>		$message,
		);

		$save = $this->Model_messages->save($data);
		if (!$save)
		{
			$this->response(array('success' => 0, 'message' => 'Message could not be saved'), 400);
		}
		
		// Run notifications
		$this->notify('send_message', $from, $to);

		$this->response(array('success' => 1, 'message' => 'Message sent'), 200);
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

	/*
	* Parse settings
	* NOTE: This will be abstracted into a library in production
	*
	* @param	string		map		-		map to parse in format (int)(int)(int)
	* @param	string		type	-		type of notification to check for
	*
	* @return	response	json	-		json response success/error
	*
	*/
	private function parse_settings ($map, $type)
	{
		/*
			Currently the map is as follows:
				notify_send
				notify_receive
				notify_deliver
		*/

		switch ($type)
		{
			case 'send':
				if (isset($map[0]))
				{
					if ($map[0] == 1)
					{
						log_message('debug', 'User receives SEND notifications');
						return TRUE;
					}
					log_message('debug', 'User DOES NOT receive SEND notifications');
					return FALSE;
				}
				else
				{
					log_message('debug', 'User DEFAULT: receives SEND notifications');
					return TRUE;
				}
			break;
			case 'receive':
				if (isset($map[1]))
				{
					if ($map[1] == 1)
					{
						log_message('debug', 'User receives RECEIVE notifications');
						return TRUE;
					}
					log_message('debug', 'User DOES NOT receive RECEIVE notifications');
					return FALSE;
				}
				else
				{
					log_message('debug', 'User DOES NOT receive RECEIVE notifications');
					return TRUE;
				}
			break;
			case 'deliver':
				if (isset($map[2]))
				{
					if ($map[2] == 1)
					{
						log_message('debug', 'User receives DELIVER notifications');
						return TRUE;
					}
					log_message('debug', 'User DOES NOT receive DELIVER notifications');
					return FALSE;
				}
				else
				{
					log_message('debug', 'User DEFAULT receives DELIVER notifications');
					return TRUE;
				}
			break;
		}

	}

	/*
	* Check notification settings and send notifications
	* NOTE: This will be abstracted into a library in production
	*
	* @param	string		type	-		type of action
	* @param	string		from	-		from uid
	* @param	string		to		-		to uid
	*
	* @return	response	json	-		json response success/error
	*
	*/
	private function notify ($type, $from, $to)
	{
		// Switch on type to get relevant settings for specified action
		// Only doing send message for demo
		switch ($type)
		{
			case 'send_message':
				$to_map = 'receive';
				$from_map = 'send';
			break;
		}

		// Check for receipient settings in cache
		$map = $this->get_user_map($to);
		if ($map)
		{
			log_message('debug', 'Settings for recipient '.$to.' retrieved: '.$map);
			// Check settings against map
			if ($this->parse_settings($map, $to_map))
			{
				$this->send_notification($to, $to_map);
			}
		}
		else
		{
			$map = $this->get_and_set_settings($to);
			if ($this->parse_settings($map, $to_map))
			{
				$this->send_notification($to, $to_map);
			}
		}

		// Check for sender settings in cache
		$map = $this->get_user_map($from);
		if ($map)
		{
			log_message('debug', 'Settings for sender '.$from.' retrieved: '.$map);
			// Check settings against map
			if ($this->parse_settings($map, $from_map))
			{
				$this->send_notification($from, $from_map);
			}
		}
		else
		{
			$map = $this->get_and_set_settings($from);
			if ($this->parse_settings($map, $from_map))
			{
				$this->send_notification($from, $from_map);
			}
		}
	}

	/*
	* Do the send notifications
	* NOTE: This will be abstracted into a library in production
	*
	* @param	string		user	-		user uid
	* @param	string		type	-		type of notification 
	*
	* @return	response	json	-		json response success/error
	*
	*/
	private function send_notification ($user, $type)
	{
		/*
			Here we will have a library or other method of sending the notifications to the user
			We will also include the type so that they can be sent differently - alert, push, background, etc

			For now we just log that a notification has been sent
		*/
		log_message('debug', 'Notification sent: User: '.$user.' | type: '.$type);
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

	private function get_and_set_settings ($uid)
	{
		// Set defaults
		$map = '111'; // This will be astracted into constant or pulled from main DB as editable config

		// Get settings and set memcache
		$settings = $this->Model_account->get_settings($uid);
		if (empty($settings))
		{
			log_message('debug', 'Settings do not exist for user '.$uid);
			$map_set = $this->set_user_map($uid, $map);
			if (!$map_set)
			{
				// We do not want to return a hard error here, but we need to log
				log_message('error', 'Settings could not be saved in cache');
			}
			return $map;
		}

		// Add to memcached/redis
		$map = $settings->notify_send.$settings->notify_receive.$settings->notify_deliver;
		$map_set = $this->set_user_map($uid, $map);
		if (!$map_set)
		{
			// We do not want to return a hard error here, but we need to log
			log_message('error', 'Settings could not be saved in cache');
			return $map;
		}

		log_message('debug', 'Settings for user '.$uid.' retrieved: '.$map);
		return $map;
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/Welcome.php */
