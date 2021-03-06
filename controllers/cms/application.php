<?php
	class cms_application_controller extends Banshee\controller {
		private function show_overview() {
			if (($application_count = $this->model->count_applications()) === false) {
				$this->view->add_tag("result", "Database error.");
				return;
			}

			$paging = new Banshee\pagination($this->view, "applications", $this->settings->admin_page_size, $application_count);

			if (($applications = $this->model->get_applications($paging->offset, $paging->size)) === false) {
				$this->view->add_tag("result", "Database error.");
				return;
			}

			$locations = config_array(LOCATION);

			$this->view->open_tag("overview");

			$this->view->open_tag("applications");
			foreach ($applications as $application) {
				$application["location"] = $locations[$application["location"]];
				$application["privacy_law"] = show_boolean($application["privacy_law"]);
				$this->view->record($application, "application");
			}
			$this->view->close_tag();

			$paging->show_browse_links();

			$this->view->close_tag();
		}

		private function add_list($label, $data) {
			$this->view->open_tag($label);
			foreach (config_array($data) as $text) {
				$this->view->add_tag("value", $text);
			}
			$this->view->close_tag();
		}

		private function show_application_form($application) {
			if (($business = $this->model->get_business()) === false) {
				$this->view->add_tag("result", "Error fetching business items.");
				return false;
			}

			if (($cat_labels = $this->model->get_labels()) === false) {
				$this->view->add_tag("result", "Error getting labels.");
				return false;
			}

			if (is_array($application["labels"]) == false) {
				$application["labels"] = array();
			}

			$this->view->add_javascript("cms/application.js");
			$this->view->add_css("includes/labels.css");
			$this->view->add_help_button();

			$this->view->open_tag("edit");
			$application["privacy_law"] = show_boolean($application["privacy_law"]);
			$this->view->record($application, "application");

			$this->view->open_tag("business", array("owner" => $application["owner_type"]));
			$this->view->add_tag("item", "(none)", array("id" => 0));
			foreach ($business as $item) {
				$this->view->add_tag("item", $item["name"], array("id" => $item["id"]));
			}
			$this->view->close_tag();

			$this->add_list("confidentiality", CONFIDENTIALITY);
			$this->add_list("integrity", INTEGRITY);
			$this->add_list("availability", AVAILABILITY);
			$this->add_list("locations", LOCATION);

			$this->view->open_tag("labels");
			foreach ($cat_labels as $category => $labels) {
				$this->view->open_tag("category", array("name" => $category));
				foreach ($labels as $label) {
					$param = array(
						"id"      => $label["id"],
						"checked" => show_boolean(in_array($label["id"], $application["labels"])));
					$this->view->add_tag("label", $label["name"], $param);
				}
				$this->view->close_tag();
			}
			$this->view->close_tag();

			$this->view->close_tag();
		}

		public function execute() {
			if ($_GET["order"] == null) {
				$_SESSION["application_search"] = null;
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save application") {
					/* Save application
					 */
					if ($this->model->save_oke($_POST) == false) {
						$this->show_application_form($_POST);
					} else if (isset($_POST["id"]) === false) {
						/* Create application
						 */
						if ($this->model->create_application($_POST) === false) {
							$this->view->add_message("Error creating application.");
							$this->show_application_form($_POST);
						} else {
							$this->user->log_action("application %d created", $this->db->last_insert_id);
							$this->show_overview();
						}
					} else {
						/* Update application
						 */
						if ($this->model->update_application($_POST) === false) {
							$this->view->add_message("Error updating application.");
							$this->show_application_form($_POST);
						} else {
							$this->user->log_action("application %d updated", $_POST["id"]);
							$this->show_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Delete application") {
					/* Delete application
					 */
					if ($this->model->delete_oke($_POST) == false) {
						$this->show_application_form($_POST);
					} else if ($this->model->delete_application($_POST["id"]) === false) {
						$this->view->add_message("Error deleting application.");
						$this->show_application_form($_POST);
					} else {
						$this->user->log_action("application %d deleted", $_POST["id"]);
						$this->show_overview();
					}
				} else if ($_POST["submit_button"] == "search") {
					/* Search
					 */
					$_SESSION["application_search"] = $_POST["search"];
					$this->show_overview();
				} else {
					$this->show_overview();
				}
			} else if ($this->page->pathinfo[2] === "new") {
				/* New application
				 */
				$application = array("owner_type" => "existing");
				$this->show_application_form($application);
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit application
				 */
				if (($application = $this->model->get_application($this->page->pathinfo[2])) === false) {
					$this->view->add_tag("result", "Application not found.");
				} else {
					$application["owner_type"] = "existing";
					$this->show_application_form($application);
				}
			} else {
				/* Show overview
				 */
				$this->show_overview();
			}
		}
	}
?>
