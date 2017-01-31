<?php
	class business_model extends Banshee\model {
		public function get_business_list() {
			$query = "select * from business where organisation_id=%d order by name";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_business($business_id) {
			$query = "select * from business where id=%d and organisation_id=%d";

			if (($result = $this->db->execute($query, $business_id, $this->user->organisation_id)) == false) {
				return false;
			}

			$business = $result[0];

			$query = "select l.* from labels l, label_business b where l.id=b.label_id and b.business_id=%d order by name";
			$business["labels"] = $this->db->execute($query, $business_id);

			return $business;
		}

		public function get_application_ownership($business_id) {
			$query = "select * from applications where owner_id=%d and organisation_id=%d order by name";

			return $this->db->execute($query, $business_id, $this->user->organisation_id);
		}

		public function get_application_usage($business_id) {
			$query = "select a.id, a.name, u.input, u.description from applications a, used_by u ".
			         "where a.id=u.application_id and u.business_id=%d and a.organisation_id=%d order by name";

			return $this->db->execute($query, $business_id, $this->user->organisation_id);
		}
	}
?>
