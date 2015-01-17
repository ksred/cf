<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Http extends CI_Controller {

	function __construct ()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function create ()
	{
		$response = $this->curl->simple_post(API_URL.'/account/settings', $_POST);
		$this->session->set_flashdata('response', $response);
		redirect('/', 'refresh');
	}

	public function update ()
	{
		$response = $this->curl->simple_put(API_URL.'/account/settings/'.$_POST['uid'].'/'.$_POST['notify_send'].'/'.$_POST['notify_receive'].'/'.$_POST['notify_deliver']);
		$this->session->set_flashdata('response', $response);
		redirect('/', 'refresh');
	}

	public function delete ()
	{
		$response = $this->curl->simple_delete(API_URL.'/account/settings/'.$_POST['uid']);
		$this->session->set_flashdata('response', $response);
		redirect('/', 'refresh');
	}

	public function message ()
	{
		$response = $this->curl->simple_post(API_URL.'/message', $_POST);
		$this->session->set_flashdata('response', $response);
		redirect('/', 'refresh');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/Welcome.php */
