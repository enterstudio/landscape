<?php
	class cms_hardware_controller extends Banshee\controller {
		private function show_overview() {
			if (($hardware_count = $this->model->count_hardware()) === false) {
				$this->view->add_tag("result", "Database error.");
				return;
			}

			$paging = new Banshee\pagination($this->view, "hardware", $this->settings->admin_page_size, $hardware_count);

			if (($hardware = $this->model->get_hardware($paging->offset, $paging->size)) === false) {
				$this->view->add_tag("result", "Database error.");
				return;
			}

			$this->view->open_tag("overview");

			$this->view->open_tag("hardware");
			foreach ($hardware as $device) {
				$this->view->record($device, "device");
			}
			$this->view->close_tag();

			$paging->show_browse_links();

			$this->view->close_tag();
		}

		private function show_hardware_form($hardware) {
			$this->view->open_tag("edit");
			$this->view->record($hardware, "hardware");
			$this->view->close_tag();
		}

		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save hardware") {
					/* Save hardware
					 */
					if ($this->model->save_oke($_POST) == false) {
						$this->show_hardware_form($_POST);
					} else if (isset($_POST["id"]) === false) {
						/* Create hardware
						 */
						if ($this->model->create_hardware($_POST) === false) {
							$this->view->add_message("Error creating hardware.");
							$this->show_hardware_form($_POST);
						} else {
							$this->user->log_action("hardware created");
							$this->show_overview();
						}
					} else {
						/* Update hardware
						 */
						if ($this->model->update_hardware($_POST) === false) {
							$this->view->add_message("Error updating hardware.");
							$this->show_hardware_form($_POST);
						} else {
							$this->user->log_action("hardware updated");
							$this->show_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Delete hardware") {
					/* Delete hardware
					 */
					if ($this->model->delete_oke($_POST) == false) {
						$this->show_hardware_form($_POST);
					} else if ($this->model->delete_hardware($_POST["id"]) === false) {
						$this->view->add_message("Error deleting hardware.");
						$this->show_hardware_form($_POST);
					} else {
						$this->user->log_action("hardware deleted");
						$this->show_overview();
					}
				} else if ($_POST["submit_button"] == "search") {
					/* Search
					 */
					$_SESSION["hardware_search"] = $_POST["search"];
					$this->show_overview();
				} else {
					$this->show_overview();
				}
			} else if ($this->page->pathinfo[2] === "new") {
				/* New hardware
				 */
				$hardware = array();
				$this->show_hardware_form($hardware);
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit hardware
				 */
				if (($hardware = $this->model->get_device($this->page->pathinfo[2])) === false) {
					$this->view->add_tag("result", "Hardware not found.");
				} else {
					$this->show_hardware_form($hardware);
				}
			} else {
				/* Show overview
				 */
				$this->show_overview();
			}
		}
	}
?>
