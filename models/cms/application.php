<?php
	class cms_application_model extends Banshee\model {
		private $columns = array("name", "owner_id", "description", "location", "privacy_law");

		public function count_applications() {
			$query = "select count(*) as count from applications where organisation_id=%d";

			if (($result = $this->db->execute($query, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0]["count"];
		}

		public function get_applications($offset, $limit) {
			unset($_SESSION["application_order"]);
			if (isset($_SESSION["application_order"]) == false) {
				$_SESSION["application_order"] = array("name", "description");
			}

			if ((in_array($_GET["order"], $this->columns)) && ($_GET["order"] != $_SESSION["application_order"][0])) {
				$_SESSION["application_order"] = array($_GET["order"], $_SESSION["application_order"][0]);
			}

			$query = "select a.*, b.name as owner from applications a left join business b on a.owner_id=b.id where a.organisation_id=%d";

			$search = array();
			if ($_SESSION["application_search"] != null) {
				foreach ($this->columns as $i => $column) {
					$this->columns[$i] = $column." like %s";
					array_push($search, "%".$_SESSION["application_search"]."%");
				}
				$query .= " having (".implode(" or ", $this->columns).")";
			}

			$query .= " order by a.%S,a.%S limit %d,%d";

			return $this->db->execute($query, $this->user->organisation_id, $search, $_SESSION["application_order"], $offset, $limit);
		}

		public function get_application($application_id) {
			$query = "select * from applications where id=%d and organisation_id=%d";

			if (($result = $this->db->execute($query, $application_id, $this->user->organisation_id)) == false) {
				return false;
			}
			$application = $result[0];

			$query = "select l.* from label_application l, applications a ".
			         "where l.application_id=a.id and application_id=%d and a.organisation_id=%d";
			if (($labels = $this->db->execute($query, $application_id, $this->user->organisation_id)) === false) {
				return false;
			}

			$application["labels"] = array();
			foreach ($labels as $label) {
				array_push($application["labels"], (int)$label["label_id"]);
			}

			return $application;
		}

		public function get_business() {
			$query = "select * from business where organisation_id=%d order by name";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_labels() {
			return $this->borrow("label")->get_labels();
		}

		private function get_owner_id($application) {
			if ($application["owner_type"] == "new") {
				if ($application["owner_name"] == "") {
					return null;
				}

				if (($business = $this->db->entry("business", $application["owner_name"], "name")) === false) {
					return false;
				} else if ($business !== null) {
					return (int)$business["id"];
				}

				$owner = array(
					"organisation_id" => $this->user->organisation_id,
					"name"            => $application["owner_name"],
					"description"     => "");
				if ($this->db->insert("business", $owner) === false) {
					return false;
				}

				return $this->db->last_insert_id;
			}

			if ($application["owner_type"] == "existing") {
				if ($application["owner_id"] == 0) {
					return null;
				}

				return (int)$application["owner_id"];
			}

			return null;
		}

		public function save_oke($application) {
			$result = true;

			if (isset($application["id"])) {
				if ($this->get_application($application["id"]) == false) {
					$this->view->add_message("Application not found.");
					$this->user->log_action("unauthorized update attempt of application %d", $application["id"]);
					return false;
				}
			}

			$application["name"] = trim($application["name"]);

			if ($application["name"] == "") {
				$this->view->add_message("Enter the application name.");
				$result = false;
			} else {
				$query = "select count(*) as count from applications where name=%s and organisation_id=%d";
				$args = array($application["name"], $this->user->organisation_id);
				if (isset($application["id"])) {
					$query .= " and id!=%d";
					array_push($args, $application["id"]);
				}

				if (($result = $this->db->execute($query, $args)) === false) {
					$this->view->add_message("Database error.");
					return false;
				}
				if ($result[0]["count"] > 0) {
					$this->view->add_message("This name already exists.");
					$result = false;
				}
			}

			if (($application["owner_type"] == "existing") && ($application["owner_id"] != 0)) {
				$query = "select * from business where id=%d and organisation_id=%d";
				if ($this->db->execute($query, $application["owner_id"], $this->user->organisation_id) == false) {
					$this->view->add_message("Owner does not exist.");
					$result = false;
				}
			}

			return $result;
		}

		private function save_labels($labels, $application_id) {
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
					"label_id"       => $label_id,
					"application_id" => $application_id);
				if ($this->db->insert("label_application", $value) === false) {
					return false;
				}
			}

			return true;
		}

		public function create_application($application) {
			$keys = array("id", "organisation_id", "name", "type", "description", "owner_id", "confidentiality", "integrity", "availability", "location", "privacy_law");

			$application["id"] = null;
			$application["name"] = trim($application["name"]);
			$application["organisation_id"] = $this->user->organisation_id;
			$application["privacy_law"] = is_true($application["privacy_law"]) ? YES : NO;

			if ($this->db->query("begin") === false) {
				return false;
			}

			if (($application["owner_id"] = $this->get_owner_id($application)) === false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->db->insert("applications", $application, $keys) === false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->save_labels($application["labels"], $this->db->last_insert_id) == false) {
				$this->db->query("rollback");
				return false;
			}

			return $this->db->query("commit") !== false;
		}

		public function update_application($application) {
			$keys = array("name", "type", "description", "owner_id", "confidentiality", "integrity", "availability", "location", "privacy_law");

			$application["name"] = trim($application["name"]);
			$application["privacy_law"] = is_true($application["privacy_law"]) ? YES : NO;

			if ($this->db->query("begin") === false) {
				return false;
			}

			if (($application["owner_id"] = $this->get_owner_id($application)) === false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->db->update("applications", $application["id"], $application, $keys) === false) {
				$this->db->query("rollback");
				return false;
			}

			$query = "delete from label_application where application_id=%d";
			if ($this->db->query($query, $application["id"]) === false) {
				$this->db->query("rollback");
				return false;
			}

			if ($this->save_labels($application["labels"], $application["id"]) == false) {
				$this->db->query("rollback");
				return false;
			}

			return $this->db->query("commit") !== false;
		}

		public function delete_oke($application) {
			$result = true;

			if ($this->get_application($application["id"]) == false) {
				$this->view->add_message("Application not found.");
				$this->user->log_action("unauthorized delete attempt of application %d", $application["id"]);
				$result = false;
			}

			return $result;
		}

		public function delete_application($application_id) {
			$queries = array(
				array("delete from label_application where application_id=%d", $application_id),
				array("delete from used_by where application_id=%d", $application_id),
				array("delete from connections where from_application_id=%d or to_application_id=%d", $application_id, $application_id),
				array("delete from runs_at where application_id=%d", $application_id),
				array("delete from applications where id=%d", $application_id));

			return $this->db->transaction($queries) !== false;
		}
	}
?>
