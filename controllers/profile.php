<?php
	class profile_controller extends Banshee\controller {
		private function show_profile_form($profile) {
			$this->view->open_tag("edit");

			$this->view->add_tag("username", $this->user->username);
			$this->view->add_tag("fullname", $profile["fullname"]);
			$this->view->add_tag("email", $profile["email"]);
			if ($this->user->status == USER_STATUS_CHANGEPWD) {
				$this->view->add_tag("cancel", "Logout", array("url" => LOGOUT_MODULE));
			} else {
				$this->view->add_tag("cancel", "Back", array("url" => ""));
			}

			/* Action log
			 */
			if (($actionlog = $this->model->last_account_logs()) !== false) {
				$this->view->open_tag("actionlog");
				foreach ($actionlog as $log) {
					$this->view->record($log, "log");
				}
				$this->view->close_tag();
			}

			$this->view->close_tag();
		}

		public function execute() {
			if ($this->user->logged_in == false) {
				$this->view->add_tag("result", "You are not logged in!", array("url" => ""));
				return;
			}

			$this->view->description = "Profile";
			$this->view->keywords = "profile";
			$this->view->title = "Profile";

			if ($this->user->status == USER_STATUS_CHANGEPWD) {
				$this->view->add_message("Please, change your password.");
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				/* Update profile
				 */
				if ($this->model->profile_oke($_POST) == false) {
					$this->show_profile_form($_POST);
				} else if ($this->model->update_profile($_POST) === false) {
					$this->view->add_tag("result", "Error while updating profile.", array("url" => "profile"));
				} else {
					$this->view->add_tag("result", "Profile has been updated.", array("url" => $this->settings->start_page));
					$this->user->log_action("profile updated");
				}
			} else {
				$user = array(
					"fullname" => $this->user->fullname,
					"email"    => $this->user->email);
				$this->show_profile_form($user);
			}
		}
	}
?>
