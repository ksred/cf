<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$log = '';
		$path = APPPATH .'logs/';
		$last_created_file = scandir($path, SCANDIR_SORT_DESCENDING);
		$logfile = $path.$last_created_file[0]; 

		// Get latest lines from log file
		$file = file($logfile);
		if (count($file) < 30)
		{
			$log = "--- Log file under 10 lines ---";
		}
		for ($i = count($file)-31; $i < count($file); $i++) {
			if ((strpos($file[$i], 'Settings') > 0) || (strpos($file[$i], 'notifications') > 0)|| (strpos($file[$i], 'Notification') > 0))
			{
				$log .= "<strong>".$file[$i] . "</strong><br />";
			}
			else
			{
				$log .= $file[$i] . "<br />";
			}
		}

		$data['log'] = $log;
		$data['response'] = $this->session->flashdata('response');

		// Get current user data
		$this->load->model('Model_account');
		$settings = $this->Model_account->get_settings('bb44bf07cf9a2db0554bba63a03d822c927deae77df101874496df5a6a3e896d');
		if (!empty($settings))
		{
			$data['notify_send'] = (int) $settings->notify_send;
			$data['notify_receive'] = (int) $settings->notify_receive;
			$data['notify_deliver'] = (int) $settings->notify_deliver;
		}
		$this->load->view('welcome_message', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/Welcome.php */
