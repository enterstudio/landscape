<?php
	class cms_action_controller extends Banshee\controller {
		public function execute() {
			if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
				$offset = 0;
			} else {
				$offset = $this->page->pathinfo[2];
			}

			if (isset($_SESSION["admin_actionlog_size"]) == false) {
				$_SESSION["admin_actionlog_size"] = $this->model->get_log_size();
			}

			$paging = new Banshee\pagination($this->view, "admin_actionlog", $this->settings->admin_page_size, $_SESSION["admin_actionlog_size"]);

			if (($log = $this->model->get_action_log($paging->offset, $paging->size)) === false) {
				$this->view->add_tag("result", "Error reading action log.");
				return;
			}

			$users = array($this->user->id => $this->user->username);

			$this->view->open_tag("log");

			$this->view->open_tag("list");
			foreach ($log as $entry) {
				$user_id = $entry["user_id"];

				list($user_id, $switch_id) = explode(":", $user_id);

				if (isset($users[$user_id]) == false) {
					if (($user = $this->model->get_user($user_id)) !== false) {
						$users[$user_id] = $user["username"];
					}
				}

				if (isset($users[$switch_id]) == false) {
					if (($switch = $this->model->get_user($switch_id)) !== false) {
						$users[$switch_id] = $switch["username"];
					}
				}

				$entry["username"] = isset($users[$user_id]) ? $users[$user_id] : "-";
				$entry["switch"] = isset($users[$switch_id]) ? $users[$switch_id] : "-";

				$this->view->record($entry, "entry");
			}
			$this->view->close_tag();

			$paging->show_browse_links();

			$this->view->close_tag();
		}
	}
?>
