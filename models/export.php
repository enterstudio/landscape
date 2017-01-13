<?php
	class export_model extends Banshee\model {
		public function get_applications() {
			static $result = null;

			if ($result === null) {
				$query = "select * from applications where organisation_id=%d";

				$result = $this->db->execute($query, $this->user->organisation_id);
			}

			return $result;
		}

		public function get_business() {
			$query = "select * from business where organisation_id=%d";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_hardware() {
			$query = "select * from hardware where organisation_id=%d";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_connections() {
			$query = "select c.* from connections c, applications a ".
			         "where c.from_application_id=a.id and a.organisation_id=%d";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_usedby() {
			$query = "select u.* from used_by u, applications a ".
			         "where u.application_id=a.id and a.organisation_id=%d";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_runsat() {
			$query = "select r.* from runs_at r, applications a ".
			         "where r.application_id=a.id and a.organisation_id=%d";

			return $this->db->execute($query, $this->user->organisation_id);
		}
	}
?>
