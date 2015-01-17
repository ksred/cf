<?php

class Model_messages extends CI_Model
{
    function __construct()
    {
        /*
        --
        -- Table structure for table `messages`
        --

		CREATE TABLE IF NOT EXISTS `messages` (
			`id` int(11) NOT NULL,
			`from` char(64) NOT NULL,
			`to` char(64) NOT NULL,
			`body` int(11) NOT NULL,
			`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;

		--
		-- Indexes for dumped tables
		--

		--
		-- Indexes for table `messages`
		--
		ALTER TABLE `messages`
		ADD PRIMARY KEY (`id`);

		--
		-- AUTO_INCREMENT for dumped tables
		--

		--
		-- AUTO_INCREMENT for table `messages`
		--
		ALTER TABLE `messages`
		MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        */

        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
    }

    function get ($id) {
        $this->db->select('*');
        $this->db->from('messages');
        $this->db->where('id', $id);
        $result = $this->db->get();
        return $result->row();
    }

    function save ($data) {
        $this->db->insert('messages', $data);
        return $this->db->insert_id();
    }
    
    function update ($id, $data)
    {
		$this->db->where(array('id' => $id));
		$res = $this->db->update('messages', $data);
		return $res;
    }

    function delete ($id) {
        $data = array('id' => $id);
        $result = $this->db->delete('messages', $data);
        return $result;
    }

}
?>
