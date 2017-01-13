<?php
	class cms_business_controller extends Banshee\controller {
		private function show_overview() {
			if (($business_count = $this->model->count_business()) === false) {
				$this->view->add_tag("result", "Database error.");
				return;
			}

			$paging = new Banshee\pagination($this->view, "business", $this->settings->admin_page_size, $business_count);

			if (($business = $this->model->get_business($paging->offset, $paging->size)) === false) {
				$this->view->add_tag("result", "Database error.");
				return;
			}

			$this->view->open_tag("overview");

			$this->view->open_tag("business");
			foreach ($business as $entity) {
				$this->view->record($entity, "entity");
			}
			$this->view->close_tag();

			$paging->show_browse_links();

			$this->view->close_tag();
		}

		private function show_business_form($business) {
			$this->view->open_tag("edit");
			$this->view->record($business, "business");
			$this->view->close_tag();
		}

		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save business") {
					/* Save business
					 */
					if ($this->model->save_oke($_POST) == false) {
						$this->show_business_form($_POST);
					} else if (isset($_POST["id"]) === false) {
						/* Create business
						 */
						if ($this->model->create_business($_POST) === false) {
							$this->view->add_message("Error creating business.");
							$this->show_business_form($_POST);
						} else {
							$this->user->log_action("business created");
							$this->show_overview();
						}
					} else {
						/* Update business
						 */
						if ($this->model->update_business($_POST) === false) {
							$this->view->add_message("Error updating business.");
							$this->show_business_form($_POST);
						} else {
							$this->user->log_action("business updated");
							$this->show_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Delete business") {
					/* Delete business
					 */
					if ($this->model->delete_oke($_POST) == false) {
						$this->show_business_form($_POST);
					} else if ($this->model->delete_business($_POST["id"]) === false) {
						$this->view->add_message("Error deleting business.");
						$this->show_business_form($_POST);
					} else {
						$this->user->log_action("business deleted");
						$this->show_overview();
					}
				} else if ($_POST["submit_button"] == "search") {
					/* Search
					 */
					$_SESSION["business_search"] = $_POST["search"];
					$this->show_overview();
				} else {
					$this->show_overview();
				}
			} else if ($this->page->pathinfo[2] === "new") {
				/* New business
				 */
				$business = array();
				$this->show_business_form($business);
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit business
				 */
				if (($business = $this->model->get_entity($this->page->pathinfo[2])) === false) {
					$this->view->add_tag("result", "Business not found.");
				} else {
					$this->show_business_form($business);
				}
			} else {
				/* Show overview
				 */
				$this->show_overview();
			}
		}
	}
?>
