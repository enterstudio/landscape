<?php
	class cms_hardware_model extends Banshee\model {
		private $columns = array("name", "description");

		public function count_hardware() {
			$query = "select count(*) as count from hardware where organisation_id=%d";

			if (($result = $this->db->execute($query, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0]["count"];
		}

		public function get_hardware($offset, $limit) {
			$query = "select * from hardware where organisation_id=%d";

			$search = array();
			if ($_SESSION["hardware_search"] != null) {
				foreach ($this->columns as $i => $column) {
					$this->columns[$i] = $column." like %s";
					array_push($search, "%".$_SESSION["hardware_search"]."%");
				}
				$query .= " having (".implode(" or ", $this->columns).")";
			}

			$query .= " order by name limit %d,%d";

			return $this->db->execute($query, $this->user->organisation_id, $search, $offset, $limit);
		}

		public function get_device($hardware_id) {
			$query = "select * from hardware where id=%d and organisation_id=%d";

			if (($result = $this->db->execute($query, $hardware_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function save_oke($hardware) {
			$result = true;

			if (isset($hardware["id"])) {
				if ($this->get_device($hardware["id"]) == false) {
					$this->view->add_message("Hardware not found.");
					return false;
				}
			}

			$hardware["name"] = trim($hardware["name"]);

			if ($hardware["name"] == "") {
				$this->view->add_message("Enter the hardware name.");
				$result = false;
			} else {
				$query = "select count(*) as count from hardware where name=%s and organisation_id=%d";
				$args = array($hardware["name"], $this->user->organisation_id);
				if (isset($hardware["id"])) {
					$query .= " and id!=%d";
					array_push($args, $hardware["id"]);
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

		public function create_hardware($hardware) {
			$keys = array("id", "organisation_id", "name", "description");

			$hardware["id"] = null;
			$hardware["name"] = trim($hardware["name"]);
			$hardware["organisation_id"] = $this->user->organisation_id;

			return $this->db->insert("hardware", $hardware, $keys);
		}

		public function update_hardware($hardware) {
			$keys = array("name", "description");

			$hardware["name"] = trim($hardware["name"]);

			return $this->db->update("hardware", $hardware["id"], $hardware, $keys);
		}

		public function delete_oke($hardware) {
			$result = true;

			if ($this->get_device($hardware["id"]) == false) {
				$this->view->add_message("Hardware not found.");
				$result = false;
			}

			return $result;
		}

		public function delete_hardware($hardware_id) {
			$queries = array(
				array("delete from runs_at where hardware_id=%d", $hardware_id),
				array("delete from hardware where id=%d", $hardware_id));

			return $this->db->transaction($queries) !== false;
		}
	}
?>
