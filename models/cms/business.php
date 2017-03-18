<?php
	class cms_business_model extends Banshee\model {
		private $columns = array("name", "description");

		public function count_business() {
			$query = "select count(*) as count from business where organisation_id=%d";

			if (($result = $this->db->execute($query, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0]["count"];
		}

		public function get_business($offset, $limit) {
			$query = "select * from business where organisation_id=%d";

			$search = array();
			if ($_SESSION["business_search"] != null) {
				foreach ($this->columns as $i => $column) {
					$this->columns[$i] = $column." like %s";
					array_push($search, "%".$_SESSION["business_search"]."%");
				}
				$query .= " having (".implode(" or ", $this->columns).")";
			}

			$query .= " order by name limit %d,%d";

			return $this->db->execute($query, $this->user->organisation_id, $search, $offset, $limit);
		}

		public function get_entity($business_id) {
			$query = "select * from business where id=%d and organisation_id=%d";

			if (($result = $this->db->execute($query, $business_id, $this->user->organisation_id)) == false) {
				return false;
			}
			$entity = $result[0];

			$query = "select l.* from label_business l, business b ".
			         "where l.business_id=b.id and business_id=%d and b.organisation_id=%d";
			if (($labels = $this->db->execute($query, $business_id, $this->user->organisation_id)) === false) {
				return false;
			}

			$entity["labels"] = array();
			foreach ($labels as $label) {
				array_push($entity["labels"], (int)$label["label_id"]);
			}

			return $entity;
		}

		public function get_labels() {
			return $this->borrow("label")->get_labels();
		}

		public function save_oke($business) {
			$result = true;

			if (isset($business["id"])) {
				if ($this->get_entity($business["id"]) == false) {
					$this->view->add_message("Business entity not found.");
					$this->user->log_action("unauthorized update attempt of business %d", $business["id"]);
					return false;
				}
			}

			$business["name"] = trim($business["name"]);

			if ($business["name"] == "") {
				$this->view->add_message("Enter the business name.");
				$result = false;
			} else {
				$query = "select count(*) as count from business where name=%s and organisation_id=%d";
				$args = array($business["name"], $this->user->organisation_id);
				if (isset($business["id"])) {
					$query .= " and id!=%d";
					array_push($args, $business["id"]);
				}

				if (($result = $this->db->execute($query, $args)) === false) {
					$this->view->add_message("Database error.");
					$result = false;
				} else if ($result[0]["count"] > 0) {
					$this->view->add_message("The name already exists.");
					$result = false;
				}
			}

			return $result;
		}

		private function save_labels($labels, $business_id) {
			if (is_array($labels) == false) {
				return true;
			}

			if (($label_ids = $this->borrow("label")->get_label_ids()) === false) {
				return false;
			}

			foreach ($labels as $label_id) {
				if (in_array($label_id, $label_ids) == false) {
					$this->user->log_action("unauthorized label assign attempt %d", $label_id);
					return false;
				}

				$value = array(
					"label_id"    => $label_id,
					"business_id" => $business_id);
				if ($this->db->insert("label_business", $value) === false) {
					return false;
				}
			}

			return true;
		}

		public function create_business($business) {
			$keys = array("id", "organisation_id", "name", "description");

			$business["id"] = null;
			$business["name"] = trim($business["name"]);
			$business["organisation_id"] = $this->user->organisation_id;

			if ($this->db->query("begin") === false) {
				return false;
			}

			if ($this->db->insert("business", $business, $keys) === false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->save_labels($business["labels"], $this->db->last_insert_id) == false) {
				$this->db->query("rollback");
				return false;
			}

			return $this->db->query("commit") !== false;
		}

		public function update_business($business) {
			$keys = array("name", "description");

			$business["name"] = trim($business["name"]);

			if ($this->db->query("begin") === false) {
				return false;
			}

			$query = "delete from label_business where business_id=%d";
			if ($this->db->query($query, $business["id"]) === false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->save_labels($business["labels"], $business["id"]) == false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->db->update("business", $business["id"], $business, $keys) === false) {
				$this->db->query("rollback");
				return false;
			}

			return $this->db->query("commit") !== false;
		}

		public function delete_oke($business) {
			$result = true;

			if ($this->get_entity($business["id"]) == false) {
				$this->view->add_message("Business entity not found.");
				$this->user->log_action("unauthorized delete attempt of business %d", $business["id"]);
				$result = false;
			}

			return $result;
		}

		public function delete_business($business_id) {
			$queries = array(
				array("update applications set owner_id=null where owner_id=%d", $business_id),
				array("delete from label_business where business_id=%d", $business_id),
				array("delete from used_by where business_id=%d", $business_id),
				array("delete from business where id=%d", $business_id));

			return $this->db->transaction($queries) !== false;
		}
	}
?>
