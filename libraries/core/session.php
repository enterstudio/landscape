<?php
	/* libraries/core/session.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 *
	 * Don't change this file, unless you know what you are doing.
	 */

	namespace Banshee\Core;

	final class session {
		private $db = null;
		private $settings = null;
		private $id = null;
		private $session_id = null;
		private $user_id = null;
		private $denied = false;

		/* Constructor
		 *
		 * INPUT:  object database, objection settings
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($db, $settings) {
			$this->db = $db;
			$this->settings = $settings;

			if ($this->db->connected == false) {
				return;
			}

			$this->db->query("delete from sessions where expire<=now()");

			/* Don't write secure session cookie via HTTP
			 */
			if (is_true(ENFORCE_HTTPS) && ($_SERVER["HTTPS"] != "on")) {
				return;
			}

			$this->start();
		}

		/* Destructor
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __destruct() {
			if ($this->id === null) {
				return;
			} else if ($this->db->connected == false) {
				return;
			}

			$session_data = array("content" => json_encode($_SESSION));
			if ($this->settings->session_persistent == false) {
				$session_data["expire"] = date("Y-m-d H:i:s", time() + $this->settings->session_timeout);
			}

			$this->db->update("sessions", $this->id, $session_data);

			$_SESSION = array();
		}

		/* Magic method get
		 *
		 * INPUT:  string key
		 * OUTPUT: mixed value
		 * ERROR:  null
		 */
		public function __get($key) {
			switch ($key) {
				case "id": return $this->session_id;
				case "user_id": return $this->user_id;
				case "denied": return $this->denied;
			}

			return null;
		}

		/* Start session
		 *
		 * INPUT:  -
		 * OUTPUT: true
		 * ERROR:  false
		 */
		private function start() {
			if (isset($_COOKIE[SESSION_NAME]) == false) {
				/* New session
				 */
				return $this->new_session();
			}

			$query = "select * from sessions where session_id=%s";
			if (($sessions = $this->db->execute($query, $_COOKIE[SESSION_NAME])) == false) {
				/* Unknown session
				 */
				return $this->new_session();
			}

			/* Existing session
			 */
			$session = $sessions[0];

			if ($session["bind_to_ip"]) {
				if ($session["ip_address"] != $_SERVER["REMOTE_ADDR"]) {
					$this->deny_session($session["user_id"]);
					return false;
				}
			}

			if ($session["user_id"] !== null) {
				if ($_COOKIE[SESSION_LOGIN] != $session["login_id"]) {
					$this->deny_session($session["user_id"]);
					return false;
				}
				$this->user_id = (int)$session["user_id"];
			}

			$this->id = (int)$session["id"];
			$this->session_id = $_COOKIE[SESSION_NAME];
			$_SESSION = json_decode($session["content"], true);

			return true;
		}

		/* Start a new session stored in the database
		 *
		 * INPUT;  -
		 * OUTPUT: true
		 * ERROR:  false
		 */
		private function new_session() {
			$attempts = 3;

			$session_data = array(
				"id"         => null,
				"session_id" => null,
				"login_id"   => null,
				"content"    => null,
				"expire"     => date("Y-m-d H:i:s", time() + $this->settings->session_timeout),
				"user_id"    => null,
				"ip_address" => $_SERVER["REMOTE_ADDR"],
				"bind_to_ip" => false,
				"name"       => null);

			do {
				if ($attempts-- == 0) {
					print "session create error\n";
					return false;
				}

				if (($rnd_str = random_string(128)) !== false) {
					$session_data["session_id"] = hash("sha512", $rnd_str);

					$result = $this->db->insert("sessions", $session_data);
				} else {
					$result = false;
				}
			} while ($result == false);

			$this->id = $this->db->last_insert_id;
			$this->session_id = $session_data["session_id"];

			$timeout = is_true($this->settings->session_persistent) ? time() + $this->settings->session_timeout : null;
			setcookie(SESSION_NAME, $this->session_id, $timeout, "/", "", is_true(ENFORCE_HTTPS), true);
			$_COOKIE[SESSION_NAME] = $this->session_id;

			return true;
		}

		/* Deny access to current session
		 *
		 * INPUT:  -
		 * OUTPUT: -
		 * ERROR:  -
		 */
		private function deny_session($user_id) {
			$this->denied = true;

			foreach (array_keys($_COOKIE) as $cookie) {
				setcookie($cookie, null, 1);
			}

			$_SERVER["REQUEST_METHOD"] = "GET";
			$_GET = array();
			$_POST = array();
			$_COOKIE = array();

			$logfile = new \Banshee\logfile("actions");
			$logfile->user_id = $user_id;
			$logfile->add_entry("session hijack attempt");

			header("X-Hiawatha-Monitor: exploit_attempt");
		}

		/* Update user_id in session record
		 *
		 * INPUT:  int user id
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function set_user_id($user_id) {
			if ($this->id === null) {
				return false;
			} else if ($this->db->connected == false) {
				return false;
			}

			if (($login_id = hash("sha512", random_string(128))) == false) {
				return false;
			}

			$timeout = is_true($this->settings->session_persistent) ? time() + $this->settings->session_timeout : null;
			setcookie(SESSION_LOGIN, $login_id, $timeout, "/", "", is_true(ENFORCE_HTTPS), true);

			$user_data = array(
				"user_id"    => (int)$user_id,
				"login_id"   => $login_id,
				"ip_address" => $_SERVER["REMOTE_ADDR"]);

			return $this->db->update("sessions", $this->id, $user_data) !== false;
		}

		/* Bind session to IP
		 *
		 * INPUT:  -
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function bind_to_ip() {
			if ($this->id === null) {
				return false;
			} else if ($this->db->connected == false) {
				return false;
			}

			$data = array(
				"bind_to_ip" => true,
				"ip_address" => $_SERVER["REMOTE_ADDR"]);

			return $this->db->update("sessions", $this->id, $data) !== false;
		}

		/* Reset session
		 *
		 * INPUT:  -
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function reset() {
			if ($this->db->connected == false) {
				return false;
			}

			$this->db->query("delete from sessions where id=%d", $this->id);

			foreach (array_keys($_COOKIE) as $cookie) {
				setcookie($cookie, null, 1);
			}

			$_SESSION = array();
			$_COOKIE = array();

			$this->id = null;
			$this->session_id = null;

			return $this->start();
		}
	}
?>
