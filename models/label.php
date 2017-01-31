<?php
	class label_model extends Banshee\model {
		public function get_label_ids() {
			$query = "select l.id from labels l, label_categories c ".
			         "where l.category_id=c.id and c.organisation_id=%d";

			if (($labels = $this->db->execute($query, $this->user->organisation_id)) === false) {
				return false;
			}

			$result = array();
			foreach ($labels as $label) {
				array_push($result, (int)$label["id"]);
			}

			return $result;
		}

		public function get_labels() {
			$query = "select * from label_categories where organisation_id=%d order by name";

			if (($categories = $this->db->execute($query, $this->user->organisation_id)) === false) {
				return false;
			}

			$result = array();

			$query = "select *,".
			         "(select count(*) from label_application where label_id=l.id) as app_count, ".
			         "(select count(*) from label_business where label_id=l.id) as bus_count ".
			         "from labels l where category_id=%d order by name";
			foreach ($categories as $category) {
				if (($labels = $this->db->execute($query, $category["id"])) === false) {
					return false;
				}

				$result[$category["name"]] = $labels;
			}

			return $result;
		}

		public function get_label($label_id) {
			$query = "select l.name as label, c.name as category from labels l, label_categories c ".
			         "where l.category_id=c.id and l.id=%d and c.organisation_id=%d";

			if (($result = $this->db->execute($query, $label_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function get_applications($label_id) {
			$query = "select a.* from applications a, label_application l ".
			         "where a.id=l.application_id and l.label_id=%d and a.organisation_id=%d";

			return $this->db->execute($query, $label_id, $this->user->organisation_id);
		}

		public function get_business($label_id) {
			$query = "select a.* from business a, label_business l ".
			         "where a.id=l.business_id and l.label_id=%d and a.organisation_id=%d";

			return $this->db->execute($query, $label_id, $this->user->organisation_id);
		}
	}
?>
