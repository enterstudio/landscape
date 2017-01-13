<?php
	class hardware_model extends Banshee\model {
		public function get_hardware_list() {
			$query = "select * from hardware where organisation_id=%d order by name";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_hardware($hardware_id) {
			$query = "select * from hardware where id=%d and organisation_id=%d";

			if (($result = $this->db->execute($query, $hardware_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function get_applications($hardware_id) {
			$query = "select a.* from applications a, runs_at r ".
			         "where a.id=r.application_id and r.hardware_id=%d and a.organisation_id=%d ".
			         "order by name";

			return $this->db->execute($query, $hardware_id, $this->user->organisation_id);
		}
	}
?>
