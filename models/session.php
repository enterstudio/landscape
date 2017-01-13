<?php
	class session_model extends Banshee\model {
		public function get_sessions() {
			$query = "select id, session_id, UNIX_TIMESTAMP(expire) as expire, ip_address, bind_to_ip, name from sessions ".
			         "where user_id=%d and expire>=now() order by name, ip_address";

			return $this->db->execute($query, $this->user->id);
		}

		public function get_session($id) {
			$query = "select id, UNIX_TIMESTAMP(expire) as expire, ip_address, name ".
			         "from sessions where id=%d and user_id=%d and expire>=now()";

			if (($result = $this->db->execute($query, $id, $this->user->id)) == false) {
				return false;
			}

			return $result[0];
		}

		public function session_oke($session) {
			$result = true;

			if ($this->settings->session_persistent) {
				if (strtotime($session["expire"]) < time()) {
					$this->view->add_message("The expire time lies in the past.");
					$result = false;
				}
			}

			return $result;
		}

		public function update_session($session) {
			$query = "update sessions set name=%s";
			$values = array("name" => $session["name"]);

			if ($this->settings->session_persistent) {
				$query .= ", expire=%s";
				$values["expire"] = $session["expire"];
			}

			$query .= " where id=%d and user_id=%d";

			return $this->db->execute($query, $values, $session["id"], $this->user->id);
		}

		public function delete_session($id) {
			$query = "delete from sessions where id=%d and user_id=%d";

			return $this->db->query($query, $id, $this->user->id) !== false;
		}
	}
?>
