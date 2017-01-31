<?php
	class cms_link_model extends Banshee\model {
		/* Application
		 */
		public function get_applications() {
			$query = "select * from applications where organisation_id=%d order by name";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		public function get_application($application_id) {
			$query = "select a.*, b.name as owner ".
			         "from applications a left join business b on a.owner_id=b.id ".
			         "where a.id=%d and a.organisation_id=%d";

			if (($result = $this->db->execute($query, $application_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		/* Business
		 */
		public function get_business() {
			$query = "select * from business where organisation_id=%d order by name";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		private function get_entity($business_id) {
			$query = "select * from business where id=%d and organisation_id=%d";

			if (($result = $this->db->execute($query, $business_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		/* Hardware
		 */
		public function get_hardware() {
			$query = "select * from hardware where organisation_id=%d order by name";

			return $this->db->execute($query, $this->user->organisation_id);
		}

		private function get_device($hardware_id) {
			$query = "select * from hardware where id=%d and organisation_id=%d";

			if (($result = $this->db->execute($query, $hardware_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		/* Connection
		 */
		public function get_connection_list($application_id) {
			$query = "select *, ".
			         "(select name from applications where id=c.from_application_id and organisation_id=%d) as from_name, ".
			         "(select name from applications where id=c.to_application_id and organisation_id=%d) as to_name ".
			         "from connections c where from_application_id=%d or to_application_id=%d order by from_name, to_name";

			return $this->db->execute($query, $this->user->organisation_id, $this->user->organisation_id, $application_id, $application_id);
		}

		public function get_connection($connection_id) {
			$query = "select c.* from connections c, applications a ".
			         "where c.from_application_id=a.id and c.id=%d and a.organisation_id=%d";

			if (($result = $this->db->execute($query, $connection_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function connection_oke($connection) {
			if (isset($connection["id"])) {
				if ($this->get_connection($connection["id"]) == false) {
					$this->view->add_message("Connection not found.");
					$this->user->log_action("unauthorized update attempt of connection %d", $connection["id"]);
					return false;
				}
			}

			if ($this->get_application($connection["from_application_id"]) == false) {
				$this->view->add_message("Unknown from-application.");
				return false;
			}

			if ($this->get_application($connection["to_application_id"]) == false) {
				$this->view->add_message("Unknown to-application.");
				return false;
			}

			return true;
		}

		public function save_connection($connection) {
			$keys = array("from_application_id", "to_application_id", "protocol", "format", "frequency", "data_flow", "description");

			if (isset($connection["id"]) == false) {
				array_unshift($keys, "id");
				$connection["id"] = null;

				return $this->db->insert("connections", $connection, $keys) !== false;
			} else {
				return $this->db->update("connections", $connection["id"], $connection, $keys) !== false;
			}
		}

		public function delete_connection($connection_id) {
			if ($this->get_connection($connection_id) == false) {
				$this->user->log_action("unauthorized delete attempt of connection %d", $connection_id);
				return false;
			}

			return $this->db->delete("connections", $connection_id);
		}

		/* Used by
		 */
		public function get_usedby_list($application_id) {
			$query = "select u.*, b.name from business b, used_by u ".
			         "where b.id=u.business_id and u.application_id=%d and b.organisation_id=%d ".
			         "order by name";

			return $this->db->execute($query, $application_id, $this->user->organisation_id);
		}

		public function get_usedby($usedby_id) {
			$query = "select u.* from used_by u, applications a ".
			         "where u.application_id=a.id and u.id=%d and a.organisation_id=%d";

			if (($result = $this->db->execute($query, $usedby_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function usedby_oke($usedby) {
			if (isset($usedby["id"])) {
				if ($this->get_usedby($usedby["id"]) == false) {
					$this->view->add_message("Used-by not found.");
					$this->user->log_action("unauthorized update attempt of used-by %d", $usedby["id"]);
					return false;
				}
			}

			if ($this->get_application($usedby["application_id"]) == false) {
				$this->view->add_message("Unknown application.");
				return false;
			}

			if ($this->get_entity($usedby["business_id"]) == false) {
				$this->view->add_message("Unknown business entity.");
				return false;
			}

			$query = "select count(*) as count from used_by where application_id=%d and business_id=%d";
			$args = array($usedby["application_id"], $usedby["business_id"]);
			if (isset($usedby["id"])) {
				$query .= " and id!=%d";
				array_push($args, $usedby["id"]);
			}
			if (($result = $this->db->execute($query, $args)) === false) {
				$this->view->add_message("Database error.");
				return false;
			}
			if ($result[0]["count"] > 0) {
				$this->view->add_message("This business entity already uses this application.");
				return false;
			}

			return true;
		}

		public function save_usedby($usedby) {
			$keys = array("application_id", "business_id", "input", "description");

			if (isset($usedby["id"]) == false) {
				array_unshift($keys, "id");
				$usedby["id"] = null;

				return $this->db->insert("used_by", $usedby, $keys) !== false;
			} else {
				return $this->db->update("used_by", $usedby["id"], $usedby, $keys) !== false;
			}
		}

		public function delete_usedby($usedby_id) {
			if ($this->get_usedby($usedby_id) == false) {
				$this->user->log_action("unauthorized delete attempt of used-by %d", $userby_id);
				return false;
			}

			return $this->db->delete("used_by", $usedby_id);
		}

		/* Runs at
		 */
		public function get_runsat_list($application_id) {
			$query = "select r.*, h.name, h.os from hardware h, runs_at r ".
			         "where h.id=r.hardware_id and r.application_id=%d and h.organisation_id=%d ".
			         "order by name";

			return $this->db->execute($query, $application_id, $this->user->organisation_id);
		}

		public function get_runsat($usedby_id) {
			$query = "select r.* from runs_at r, applications a ".
			         "where r.application_id=a.id and r.id=%d and a.organisation_id=%d";

			if (($result = $this->db->execute($query, $usedby_id, $this->user->organisation_id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function runsat_oke($runsat) {
			if (isset($runsat["id"])) {
				if ($this->get_runsat($runsat["id"]) == false) {
					$this->view->add_message("Runs-at not found.");
					$this->user->log_action("unauthorized update attempt of runs-at %d", $runsat["id"]);
					return false;
				}
			}

			if ($this->get_application($runsat["application_id"]) == false) {
				$this->view->add_message("Unknown application.");
				return false;
			}

			if ($this->get_device($runsat["hardware_id"]) == false) {
				$this->view->add_message("Unknown device.");
				return false;
			}

			$query = "select count(*) as count from runs_at where application_id=%d and hardware_id=%d";
			$args = array($runsat["application_id"], $runsat["hardware_id"]);
			if (isset($runsat["id"])) {
				$query .= " and id!=%d";
				array_push($args, $runsat["id"]);
			}
			if (($result = $this->db->execute($query, $args)) === false) {
				$this->view->add_message("Database error.");
				return false;
			}
			if ($result[0]["count"] > 0) {
				$this->view->add_message("This application already runs at the assigned server.");
				return false;
			}

			return true;
		}

		public function save_runsat($runsat) {
			$keys = array("application_id", "hardware_id");

			if (isset($runsat["id"]) == false) {
				array_unshift($keys, "id");
				$runsat["id"] = null;

				return $this->db->insert("runs_at", $runsat, $keys) !== false;
			} else {
				return $this->db->update("runs_at", $runsat["id"], $runsat, $keys) !== false;
			}
		}

		public function delete_runsat($runsat_id) {
			if ($this->get_runsat($runsat_id) == false) {
				$this->user->log_action("unauthorized delete attempt of runs-at %d", $runsat_id);
				return false;
			}

			return $this->db->delete("runs_at", $runsat_id);
		}
	}
?>
