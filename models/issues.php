<?php
	class issues_model extends Banshee\model {
		public function get_no_owners() {
			$query = "select * from applications where owner_id is null and organisation_id=%d";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_crowded_servers() {
			$query = "select *, (select count(*) from runs_at where hardware_id=h.id) as applications ".
			         "from hardware h having applications>%d and organisation_id=%d order by applications desc";

			return $this->db->execute($query, $this->settings->max_apps_per_server, $this->user->organisation_id);
		}

		public function get_no_hardware() {
			$query = "select *, (select count(*) from runs_at where application_id=a.id) as hardware ".
			         "from applications a having hardware=%d and external=%d and organisation_id=%d";

			return $this->db->execute($query, 0, NO, $this->user->organisation_id);
		}

		public function get_isolated_business() {
			$query = "select *, (select count(*) from used_by where business_id=b.id) as usedby, ".
			         "(select count(*) from applications where owner_id=b.id) as owning ".
			         "from business b where b.organisation_id=%d having usedby=%d and owning=%d";

			return $this->db->execute($query, $this->user->organisation_id, 0, 0);
		}

		public function get_isolated_hardware() {
			$query = "select *, (select count(*) from runs_at where hardware_id=h.id) as runsat ".
			         "from hardware h where h.organisation_id=%d having runsat=%d";

			return $this->db->execute($query, $this->user->organisation_id, 0);
		}
	}
?>
