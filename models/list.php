<?php
	class list_model extends Banshee\model {
		public function get_privacy() {
			$query = "select a.*, b.name as owner from applications a left join ".
			         "business b on a.owner_id=b.id where a.privacy_law=%d and a.organisation_id=%d ".
			         "order by name";

			if (($result = $this->db->execute($query, YES, $this->user->organisation_id)) !== false) {
				foreach ($result as $i => $item) {
					$result[$i]["external"] = show_boolean($item["external"]);
				}
			}

			return $result;
		}

		public function get_protocols() {
			$query = "select c.*, ".
			         "(select name from applications where id=c.from_application_id) as from_app, ".
			         "(select name from applications where id=c.to_application_id) as to_app ".
			         "from connections c, applications a where c.from_application_id=a.id and a.organisation_id=%d ".
			         "order by protocol, from_app, to_app";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_input() {
			$query = "select l.input, b.id as bus_id, b.name as business, ".
			         "a.id as app_id, a.name as application ".
			         "from used_by l, business b, applications a ".
			         "where b.id=l.business_id and l.application_id=a.id and ".
			         "input!=%s and a.organisation_id=%d order by input";

			return $this->db->execute($query, "", $this->user->organisation_id);
		}
	}
?>
