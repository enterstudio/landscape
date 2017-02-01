<?php
	class cms_label_category_model extends Banshee\model {
		private $columns = array();

		public function get_categories() {
			$query = "select * from label_categories where organisation_id=%d order by name";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_category($category_id) {
			$query = "select * from label_categories where id=%d and organisation_id=%d";

			if (($result = $this->db->execute($query, $category_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function save_oke($category) {
			$result = true;

			if (isset($category["id"])) {
				if ($this->get_category($category["id"]) == false) {
					$this->view->add_message("Category not found.");
					$this->user->log_action("unauthorized update attempt of label category %d", $category["id"]);
					$result = false;
				}
			}

			if (trim($category["name"]) == "") {
				$this->view->add_message("Enter the category name.");
				$result = false;
			} else {
				$query = "select count(*) as count from label_categories ".
				         "where organisation_id=%d and name=%s";
				$args = array($this->user->organisation_id, trim($category["name"]));

				if (isset($category["id"])) {
					$query .= " and id!=%d";
					array_push($args, $category["id"]);
				}

				if (($result = $this->db->execute($query, $args)) === false) {
					$result = false;
				} else if ($result[0]["count"] > 0) {
					$this->view->add_message("This label category already exists.");
					$result = false;
				}
			}

			return $result;
		}

		public function create_category($category) {
			$keys = array("id", "organisation_id", "name");

			$category["id"] = null;
			$category["name"] = trim($category["name"]);
			$category["organisation_id"] = $this->user->organisation_id;

			return $this->db->insert("label_categories", $category, $keys);
		}

		public function update_category($category) {
			$keys = array("name");

			$category["name"] = trim($category["name"]);

			return $this->db->update("label_categories", $category["id"], $category, $keys);
		}

		public function delete_oke($category) {
			$result = true;

			if ($this->get_category($category["id"]) == false) {
				$this->view->add_message("Category not found.");
				$this->user->log_action("unauthorized delete attempt of label category %d", $category["id"]);
				$result = false;
			}

			return $result;
		}

		public function delete_category($category_id) {
			unset($_SESSION["label_category"]);

			$queries = array(
				array("delete from label_application where label_id in (select id from labels where category_id=%d)", $category_id),
				array("delete from label_business where label_id in (select id from labels where category_id=%d)", $category_id),
				array("delete from labels where category_id=%d", $category_id),
				array("delete from label_categories where id=%d", $category_id));

			return $this->db->transaction($queries) !== false;
		}
	}
?>
