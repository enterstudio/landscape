<?php
	class session_controller extends Banshee\controller {
		private function show_sessions() {
			if (($sessions = $this->model->get_sessions()) === false) {
				$this->view->add_tag("result", "Error fetching session information.");
				return;
			}

			$this->view->open_tag("sessions");
			foreach ($sessions as $session) {
				$owner = show_boolean($session["session_id"] == $_COOKIE[SESSION_NAME]);
				$session["expire"] = date("d F Y, H:i:s", $session["expire"]);
				$session["bind_to_ip"] = show_boolean($session["bind_to_ip"]);
				$this->view->record($session, "session", array("owner" => $owner));
			}
			$this->view->close_tag();
		}

		private function show_session_form($session) {
			$this->view->add_javascript("jquery/jquery-ui.js");
			$this->view->add_javascript("banshee/jquery.timepicker.js");
			$this->view->add_javascript("banshee/datetimepicker.js");
			$this->view->add_css("jquery/jquery-ui.css");
			$this->view->add_css("banshee/timepicker.css");

			$this->view->open_tag("edit", array("persistent" => show_boolean($this->settings->session_persistent)));
			$this->view->record($session, "session");
			$this->view->close_tag();
		}

		public function execute() {
			if ($this->user->logged_in == false) {
				$this->view->add_tag("result", "The session manager should not be accessible for non-authenticated visitors!");
				return;
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Update session") {
					/* Update session
				 	 */
					if ($this->model->session_oke($_POST) == false) {
						$this->show_session_form($_POST);
					} else if ($this->model->update_session($_POST) == false) {
						$this->view->add_tag("result", "Error while updateing session.");
					} else {
						$this->show_sessions();
					}
				} else if ($_POST["submit_button"] == "Delete session") {
					/* Delete session
					 */
					if ($this->model->delete_session($_POST["id"]) == false) {
						$this->view->add_tag("result", "Error while deleting session.");
					} else {
						$this->show_sessions();
					}
				} else {
					$this->show_sessions();
				}
			} else if (isset($this->page->pathinfo[1])) {
				/* Edit session
				 */
				if (($session = $this->model->get_session($this->page->pathinfo[1])) == false) {
					$this->view->add_tag("result", "Session not found.");
				} else {
					$session["expire"] = date("Y-m-d H:i:s", $session["expire"]);
					$this->show_session_form($session);
				}
			} else {
				/* Show overview
				 */
				$this->show_sessions();
			}
		}
	}
?>
