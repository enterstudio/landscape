<?php
	class banshee_login_controller extends Banshee\controller {
		public function execute() {
			header("Status: 401");

			$this->view->description = "Login";
			$this->view->keywords = "login";
			$this->view->title = "Login";

			$this->view->add_javascript("banshee/login.js");

			$bind_ip = ($_SERVER["REQUEST_METHOD"] == "POST") ? $_POST["bind_ip"] : true;

			$this->view->open_tag("login", array(
				"authenticator" => show_boolean(USE_AUTHENTICATOR),
				"password"      => show_boolean(module_exists("password")),
				"register"      => show_boolean(module_exists("register")),
				"bind_ip"       => show_boolean($bind_ip)));

			$this->view->add_tag("url", $_SERVER["REQUEST_URI"]);

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$this->view->add_tag("username", $_POST["username"]);
			}

			$this->view->add_tag("remote_addr", $_SERVER["REMOTE_ADDR"]);

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (strpos($_POST["username"], "'") !== false) {
					$this->view->add_message("Sorry, this application does not support SQL injection.");
					header("X-Hiawatha-Monitor: exploit_attempt");
				} else {
					$this->view->add_message("Login incorrect");
				}
			}

			$this->view->close_tag();
		}
	}
?>
