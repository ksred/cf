<?php

class Model_account extends CI_Model
{
    function __construct()
    {
        /*
        --
        -- Table structure for table `account_settings`
        --

        CREATE TABLE IF NOT EXISTS `account_settings` (
			`id` int(11) NOT NULL,
			`uid` char(64) NOT NULL,
			`notify_send` int(11) NOT NULL DEFAULT '1',
			`notify_receive` int(11) NOT NULL DEFAULT '1',
			`notify_deliver` int(11) NOT NULL DEFAULT '1'
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;

		--
		-- Indexes for dumped tables
		--

		--
		-- Indexes for table `account_settings`
		--
		ALTER TABLE `account_settings`
		ADD PRIMARY KEY (`id`);

		--
		-- AUTO_INCREMENT for dumped tables
		--

		--
		-- AUTO_INCREMENT for table `account_settings`
		--
		ALTER TABLE `account_settings`
		MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        */

        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
    }

    function get_settings ($uid) {
        $this->db->select('*');
        $this->db->from('account_settings');
        $this->db->where('uid', $uid);
        $result = $this->db->get();
        return $result->row();
    }

    function save_settings ($data) {
        $this->db->insert('account_settings', $data);
        return $this->db->insert_id();
    }
    
    function update_settings ($uid, $data)
    {
		$this->db->where(array('uid' => $uid));
		$res = $this->db->update('account_settings', $data);
		return $res;
    }

    function delete_settings ($uid) {
        $data = array('uid' => $uid);
        $result = $this->db->delete('account_settings', $data);
        return $result;
    }

}
?>
