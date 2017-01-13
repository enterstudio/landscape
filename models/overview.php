<?php
	class overview_model extends Banshee\model {
		public function get_applications() {
			$query = "select *, ".
			         "(select count(*) from connections where from_application_id=a.id or to_application_id=a.id) + ".
					 "(select count(*) from runs_at where application_id=a.id) + ".
					 "(select count(*) from used_by where application_id=a.id) as links ".
			         "from applications a where organisation_id=%d order by name";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_business() {
			$query = "select *, ".
					 "(select count(*) from used_by where business_id=b.id) + ".
			         "(select count(*) from applications where owner_id=b.id) as links ".
			         "from business b where organisation_id=%d order by name";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_hardware() {
			$query = "select *, ".
					 "(select count(*) from runs_at where hardware_id=h.id) as links ".
			         "from hardware h where organisation_id=%d order by name";

			return $this->db->execute($query, $this->user->organisation_id);
		}
	}
?>
