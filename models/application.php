<?php
	class application_model extends Banshee\model {
		public function get_applications() {
			$query = "select a.*, b.name as owner ".
			         "from applications a left join business b on a.owner_id=b.id ".
			         "where a.organisation_id=%d order by a.name";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_application($application_id) {
			$query = "select a.*, b.name as owner ".
			         "from applications a left join business b on a.owner_id=b.id ".
			         "where a.id=%d and a.organisation_id=%d";

			if (($application = $this->db->execute($query, $application_id, $this->user->organisation_id)) == false) {	
				return false;
			}

			return $application[0];
		}

		public function get_connections($application_id) {
			$query = "select *, ".
			         "(select name from applications where id=c.from_application_id and organisation_id=%d) as from_name, ".
			         "(select name from applications where id=c.to_application_id and organisation_id=%d) as to_name ".
			         "from connections c where from_application_id=%d or to_application_id=%d ".
			         "order by from_name, to_name";

			return $this->db->execute($query, $this->user->organisation_id, $this->user->organisation_id, $application_id, $application_id);
		}

		public function get_used_by($application_id) {
			$query = "select b.id, b.name, u.input, u.description from business b, used_by u ".
			         "where b.id=u.business_id and u.application_id=%d and b.organisation_id=%d ".
			         "order by name";

			return $this->db->execute($query, $application_id, $this->user->organisation_id);
		}

		public function get_runs_at($application_id) {
			$query = "select h.* from hardware h, runs_at r ".
			         "where h.id=r.hardware_id and r.application_id=%d and h.organisation_id=%d ".
			         "order by name";

			return $this->db->execute($query, $application_id, $this->user->organisation_id);
		}
	}
?>
