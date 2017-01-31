<?php
	class connection_model extends Banshee\model {
		public function get_connection($id) {
			$query = "select c.*, ".
			         "(select name from applications where id=c.from_application_id) as from_name, ".
			         "(select name from applications where id=c.to_application_id) as to_name ".
			         "from connections c, applications a where c.id=%d and c.from_application_id=a.id and a.organisation_id=%d ".
			         "order by from_name, to_name";

			if (($result = $this->db->execute($query, $id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

	}
?>
