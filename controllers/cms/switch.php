<?php
	class cms_switch_controller extends Banshee\controller {
		public function execute() {
			if (isset($_SESSION["user_switch"])) {
				/* User switch already active
				 */
				$this->view->add_tag("result", "User switch already active.", array("url" => $this->settings->start_page));
			} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
				/* Switch user
				 */
				if ($_POST["user_id"] == $this->user->id) {
					$this->view->add_tag("result", "Can't change to yourself.");
				} else if (($_POST["user_id"] == "0") || ($this->model->get_user($_POST["user_id"]) === false)) {
					$this->view->add_tag("result", "User doesn't exist.");
				} else {
					$this->user->log_action("switched to user_id %d", $_POST["user_id"]);
					$_SESSION["user_switch"] = $this->user->id;
					$this->user->session->set_user_id((int)$_POST["user_id"]);
					$this->view->add_tag("result", "User switch successfull.", array("url" => $this->settings->start_page));
				}
			} else {
				/* Show user list
				 */
				if (($users = $this->model->get_users()) === false) {
					$this->view->add_tag("result", "Database error");
				} else {
					$this->view->open_tag("users");
					foreach ($users as $user) {
						$this->view->record($user, "user");
					}
					$this->view->close_tag();
				}
			}
		}
	}
?>
