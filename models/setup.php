<?php
	class setup_model extends Banshee\model {
		private $required_php_extensions = array("libxml", "mysqli", "xsl");

		/* Determine next step
		 */
		public function step_to_take() {
			$missing = $this->missing_php_extensions();
			if (count($missing) > 0) {
				return "php_extensions";
			}

			exec("which mysql", $output, $result);
			if ($result != 0) {
				return "mysql_client";
			}

			if ($this->db->connected == false) {
				$db = new Banshee\Database\MySQLi_connection(DB_HOSTNAME, DB_DATABASE, DB_USERNAME, DB_PASSWORD);
			} else {
				$db = $this->db;
			}

			if ($db->connected == false) {
				/* No database connection
				 */
				if ((DB_HOSTNAME == "localhost") && (DB_DATABASE == "landscape") && (DB_USERNAME == "landscape") && (DB_PASSWORD == "landscape")) {
					return "db_settings";
				} else if (strpos(DB_PASSWORD, "'") !== false) {
					$this->view->add_system_message("A single quote is not allowed in the password!");
					return "db_settings";
				}

				return "create_db";
			}

			$result = $db->execute("show tables like %s", "settings");
			if (count($result) == 0) {
				return "import_sql";
			}

			if ($this->settings->database_version < $this->latest_database_version()) {
				return "update_db";
			}

			$result = $db->execute("select password from users where username=%s", "admin");
			if ($result[0]["password"] == "none") {
				return "credentials";
			}

			return "done";
		}

		/* Missing PHP extensions
		 */
		public function missing_php_extensions() {
			static $missing = null;

			if ($missing !== null) {
				return $missing;
			}

			$missing = array();
			foreach ($this->required_php_extensions as $extension) {
				if (extension_loaded($extension) == false) {
					array_push($missing, $extension);
				}
			}

			return $missing;
		}

		/* Remove datase related error messages
		 */
		public function remove_database_errors() {
			$errors = explode("\n", rtrim(ob_get_contents()));
			ob_clean();

			foreach ($errors as $error) {
				if (strpos(strtolower($error), "mysqli_connect") === false) {
					print $error."\n";
				}
			}
		}

		/* Create the MySQL database
		 */
		public function create_database($username, $password) {
			$db = new Banshee\Database\MySQLi_connection(DB_HOSTNAME, "mysql", $username, $password);

			if ($db->connected == false) {
				$this->view->add_message("Error connecting to database.");
				return false;
			}

			$db->query("begin");

			/* Create database
			 */
			$query = "create database if not exists %S character set utf8";
			if ($db->query($query, DB_DATABASE) == false) {
				$db->query("rollback");
				$this->view->add_message("Error creating database.");
				return false;
			}

			/* Create user
			 */
			$query = "select count(*) as count from user where User=%s";
			if (($users = $db->execute($query, DB_USERNAME)) === false) {
				$db->query("rollback");
				$this->view->add_message("Error checking for user.");
				return false;
			}

			if ($users[0]["count"] == 0) {
				$query = "create user %s@%s identified by %s";
				if ($db->query($query, DB_USERNAME, DB_HOSTNAME, DB_PASSWORD) == false) {
					$db->query("rollback");
					$this->view->add_message("Error creating user.");
					return false;
				}
			} else {
				$login_test = new Banshee\Database\MySQLi_connection(DB_HOSTNAME, DB_DATABASE, DB_USERNAME, DB_PASSWORD);
				if ($login_test->connected == false) {
					$db->query("rollback");
					$this->view->add_message("Invalid credentials in settings/website.conf.");
					return false;
				}
			}

			/* Set access rights
			 */
			$rights = array(
				"select", "insert", "update", "delete",
				"create", "drop", "alter", "index", "lock tables",
				"create view", "show view");

			$query = "grant ".implode(", ", $rights)." on %S.* to %s@%s";
			if ($db->query($query, DB_DATABASE, DB_USERNAME, DB_HOSTNAME) == false) {
				$db->query("rollback");
				$this->view->add_message("Error setting access rights.");
				return false;
			}

			/* Commit changes
			 */
			$db->query("commit");
			$db->query("flush privileges");
			unset($db);

			return true;
		}

		/* Import database tables from file
		 */
		public function import_sql() {
			$result = system("mysql --version");
			if (substr($result, 0, 5) != "mysql") {
				$this->view->add_message("The MySQL command line tool could not be found. Install it first.");
				return false;
			}

			exec("mysql -h '".DB_HOSTNAME."' -u '".DB_USERNAME."' --password='".DB_PASSWORD."' '".DB_DATABASE."' < ../database/mysql.sql", $output, $result);
			if ($result != 0) {
				$this->view->add_message("Error while importing database tables.");
				return false;
			}

			$this->db->query("update users set status=%d", USER_STATUS_CHANGEPWD);
			$this->settings->secret_website_code = random_string();

			return true;
		}

		/* Collect latest database version from update_database() function
		 */
		private function latest_database_version() {
			$old_db = $this->db;
			$old_settings = $this->settings;
			$this->db = new dummy_object();
			$this->settings = new dummy_object();
			$this->settings->database_version = 0;

			$this->update_database();
			$version = $this->settings->database_version;

			unset($this->db);
			unset($this->settings);
			$this->db = $old_db;
			$this->settings = $old_settings;

			return $version;
		}

		/* Add setting when missing
		 */
		private function ensure_setting($key, $type, $value) {
			if ($this->db->entry("settings", $key, "key") != false) {
				return true;
			}

			$entry = array(
				"key"   => $key,
				"type"  => $type,
				"value" => $value);
			return $this->db->insert("settings", $entry) !== false;
		}

		/* Update database
		 */
		public function update_database() {
			return true;
		}

		/* Set administrator password
		 */
		public function set_admin_credentials($username, $password, $repeat) {
			$result = true;

			if (valid_input($username, VALIDATE_LETTERS, VALIDATE_NONEMPTY) == false) {
				$this->view->add_message("The username must consist of lowercase letters.");
				$result = false;
			}

			if ($password != $repeat) {
				$this->view->add_message("The passwords do not match.");
				$result = false;
			}

			if (secure_password($password, $this->view) == false) {
				$result = false;
			}

			if ($result == false) {
				return false;
			}

			$password = hash_password($password, $username);
			$query = "update users set username=%s, password=%s, status=%d where username=%s";
			if ($this->db->query($query, $username, $password, USER_STATUS_ACTIVE, "admin") === false) {
				$this->view->add_message("Error while setting password.");
				return false;
			}

			return true;
		}
	}

	class dummy_object {
		private $cache = array();

		public function __set($key, $value) {
			$this->cache[$key] = $value;
		}

		public function __get($key) {
			return $this->cache[$key];
		}

		public function __call($func, $args) {
			 return false;
		}
	}
?>
