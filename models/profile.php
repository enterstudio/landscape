<?php
	class profile_model extends Banshee\model {
		public function last_account_logs() {
			if (($fp = fopen("../logfiles/actions.log", "r")) == false) {
				return false;
			}

			$result = array();

			while (($line = fgets($fp)) !== false) {
				list($ip, $timestamp, $user_id, $message) = explode("|", chop($line));

				if ($user_id == "-") {
					continue;
				} else if ($user_id != $this->user->id) {
					continue;
				}

				array_push($result, array(
					"ip"        => $ip,
					"timestamp" => $timestamp,
					"message"   => $message));
				if (count($result) > 15) {
					array_shift($result);
				}
			}

			fclose($fp);

			return array_reverse($result);
		}

		private function hash_password($password) {
			static $cache = array();

			if (isset($cache[$password]) == false) {
				$cache[$password] = hash_password($profile["password"], $this->user->username);
			}

			return $cache[$password];
		}

		public function profile_oke($profile) {
			$result = true;

			if (trim($profile["fullname"]) == "") {
				$this->view->add_message("Fill in your name.");
				$result = false;
			}

			if (valid_email($profile["email"]) == false) {
				$this->view->add_message("Invalid e-mail address.");
				$result = false;
			} else if (($check = $this->db->entry("users", $profile["email"], "email")) != false) {
				if ($check["id"] != $this->user->id) {
					$this->view->add_message("E-mail address already exists.");
					$result = false;
				}
			}

			if (hash_password($profile["current"], $this->user->username) != $this->user->password) {
				$this->view->add_message("Current password is incorrect.");
				$result = false;
			}

			if ($profile["password"] != "") {
				if (secure_password($profile["password"], $this->view) == false) {
					$result = false;
				} else if ($profile["password"] != $profile["repeat"]) {
					$this->view->add_message("New passwords do not match.");
					$result = false;
				} else if ($this->user->password == $this->hash_password($profile["password"])) {
					$this->view->add_message("New password must be different from current password.");
					$result = false;
				}
			}

			return $result;
		}

		public function update_profile($profile) {
			$keys = array("fullname", "email");

			if ($this->hashed !== null) {
				array_push($keys, "password");
				array_push($keys, "status");

				$profile["password"] = $this->hash_password($profile["password"]);
				$profile["status"] = USER_STATUS_ACTIVE;
			}

			return $this->db->update("users", $this->user->id, $profile, $keys) !== false;
		}
	}
?>
