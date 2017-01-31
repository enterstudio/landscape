<?php
	class usedby_model extends Banshee\model {
		public function get_usedby($id) {
			$query = "select u.*, ".
			         "(select name from applications where id=u.application_id) as application, ".
			         "(select name from business where id=u.business_id) as business ".
			         "from used_by u, applications a where u.id=%d and u.application_id=a.id and a.organisation_id=%d ".
			         "order by application, business";

			if (($result = $this->db->execute($query, $id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

	}
?>
