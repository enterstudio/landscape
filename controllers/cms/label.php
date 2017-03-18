<?php
	class cms_label_controller extends Banshee\controller {
		private function show_overview() {
			if (($labels = $this->model->get_labels($_SESSION["label_category"])) === false) {
				$this->view->add_tag("result", "Database error.");
				return;
			}

			$this->view->open_tag("overview");

			if (($categories = $this->model->get_categories()) != false) {
				$this->view->open_tag("categories", array("current" => $_SESSION["label_category"]));
				foreach ($categories as $category) {
					$this->view->record($category, "category");
				}
				$this->view->close_tag();
			}

			$this->view->open_tag("labels");
			foreach ($labels as $label) {
				$this->view->record($label, "label");
			}
			$this->view->close_tag();

			$this->view->close_tag();
		}

		private function show_label_form($label) {
			if ($this->model->get_categories() == false) {
				$this->view->add_tag("result", "You will be redirected to the Label administration page.", array("url" => "cms/label/category/new"));
				return;
			}

			$this->view->open_tag("edit");
			$this->view->record($label, "label");
			$this->view->close_tag();
		}

		public function execute() {
			if (($categories = $this->model->get_categories()) === false) {
				$this->view->add_tag("result", "Database error.", array("url" => ""));
			}


			if (isset($_SESSION["label_category"]) == false) {
				if (count($categories) == 0) {
					$this->view->add_system_message("Add a label category first.");
					$_SERVER["REQUEST_METHOD"] = "GET";
				} else {
					$_SESSION["label_category"] = $categories[0]["id"];
				}
			}
			
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save label") {
					/* Save label
					 */
					if ($this->model->save_oke($_POST) == false) {
						$this->show_label_form($_POST);
					} else if (isset($_POST["id"]) === false) {
						/* Create label
						 */
						if ($this->model->create_label($_POST) === false) {
							$this->view->add_message("Error creating label.");
							$this->show_label_form($_POST);
						} else {
							$this->user->log_action("label %d created", $this->db->last_insert_id);
							$this->show_overview();
						}
					} else {
						/* Update label
						 */
						if ($this->model->update_label($_POST) === false) {
							$this->view->add_message("Error updating label.");
							$this->show_label_form($_POST);
						} else {
							$this->user->log_action("label %d updated", $_POST["id"]);
							$this->show_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Delete label") {
					/* Delete label
					 */
					if ($this->model->delete_oke($_POST) == false) {
						$this->show_label_form($_POST);
					} else if ($this->model->delete_label($_POST["id"]) === false) {
						$this->view->add_message("Error deleting label.");
						$this->show_label_form($_POST);
					} else {
						$this->user->log_action("label %d deleted", $_POST["id"]);
						$this->show_overview();
					}
				} else if ($_POST["submit_button"] == "category") {
					/* Set category
					 */
					foreach ($categories as $category) {
						if ($_POST["category"] == $category["id"]) {
							$_SESSION["label_category"] = $_POST["category"];
							break;
						}
					}
					$this->show_overview();
				} else {
					$this->show_overview();
				}
			} else if ($this->page->pathinfo[2] === "new") {
				/* New label
				 */
				$label = array();
				$this->show_label_form($label);
			} else if (valid_input($this->page->pathinfo[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit label
				 */
				if (($label = $this->model->get_label($this->page->pathinfo[2])) === false) {
					$this->view->add_tag("result", "Label not found.");
				} else {
					$this->show_label_form($label);
				}
			} else {
				/* Show overview
				 */
				$this->show_overview();
			}
		}
	}
?>
