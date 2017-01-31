<?php
	class cms_label_model extends Banshee\model {
		public function get_categories() {
			static $categories = null;

			if ($categories === null) {
				$query = "select id,name from label_categories where organisation_id=%d order by name";
				$categories = $this->db->execute($query, $this->user->organisation_id);
			}

			return $categories;
		}

		public function get_labels($category_id) {
			$query = "select l.* from labels l, label_categories c ".
			         "where l.category_id=c.id and category_id=%d and c.organisation_id=%d ".
			         "order by name";

			return $this->db->execute($query, $category_id, $this->user->organisation_id);
		}

		public function get_label($label_id) {
			$query = "select l.* from labels l, label_categories c ".
			         "where l.id=%d and l.category_id=c.id";

			if (($result = $this->db->execute($query, $label_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function save_oke($label) {
			$result = true;

			if (isset($label["id"])) {
				if ($this->get_label($label["id"]) == false) {
					$this->view->add_message("Label not found.");
					$this->user->log_action("unauthorized update attempt of label %d", $label["id"]);
					$result = false;
				}
			}

			if (trim($label["name"]) == "") {
				$this->view->add_message("Enter the label name.");
				$result = false;
			} else {
				$query = "select count(*) as count from labels l, label_categories c ".
				         "where l.category_id=c.id and l.name=%s and category_id=%d";
				$args = array(trim($label["name"]), $_SESSION["label_category"]);

				if (isset($label["id"])) {
					$query .= " and l.id!=%d";
					array_push($args, $label["id"]);
				}

				if (($result = $this->db->execute($query, $args)) === false) {
					$result = false;
				} else if ($result[0]["count"] > 0) {
					$this->view->add_message("This label label already exists.");
					$result = false;
				}
			}

			return $result;
		}

		public function create_label($label) {
			$keys = array("id", "category_id", "name");

			$label["id"] = null;
			$label["name"] = trim($label["name"]);
			$label["category_id"] = $_SESSION["label_category"];

			return $this->db->insert("labels", $label, $keys);
		}

		public function update_label($label) {
			$keys = array("name");

			$label["name"] = trim($label["name"]);

			return $this->db->update("labels", $label["id"], $label, $keys);
		}

		public function delete_oke($label) {
			$result = true;

			if ($this->get_label($label["id"]) == false) {
				$this->view->add_message("Label not found.");
				$this->user->log_action("unauthorized delete attempt of label %d", $label["id"]);
				$result = false;
			}

			return $result;
		}

		public function delete_label($label_id) {
			$queries = array(
				array("delete from label_application where label_id=%d", $label_id),
				array("delete from label_business where label_id=%d", $label_id),
				array("delete from labels where id=%d", $label_id));

			return $this->db->transaction($queries) !== false;
		}
	}
?>
