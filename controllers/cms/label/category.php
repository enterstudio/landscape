<?php
	class cms_label_category_controller extends Banshee\controller {
		private function show_overview() {
			if (($categories = $this->model->get_categories()) === false) {
				$this->view->add_tag("result", "Database error.");
				return;
			}

			$this->view->add_help_button();

			$this->view->open_tag("overview");

			$this->view->open_tag("categories");
			foreach ($categories as $category) {
				$this->view->record($category, "category");
			}
			$this->view->close_tag();

			$this->view->close_tag();
		}

		private function show_category_form($category) {
			$this->view->open_tag("edit");
			$this->view->record($category, "category");
			$this->view->close_tag();
		}

		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "Save category") {
					/* Save category
					 */
					if ($this->model->save_oke($_POST) == false) {
						$this->show_category_form($_POST);
					} else if (isset($_POST["id"]) === false) {
						/* Create category
						 */
						if ($this->model->create_category($_POST) === false) {
							$this->view->add_message("Error creating category.");
							$this->show_category_form($_POST);
						} else {
							$_SESSION["label_category"] = $this->db->last_insert_id;
							$this->user->log_action("category %d created", $this->db->last_insert_id);
							$this->show_overview();
						}
					} else {
						/* Update category
						 */
						if ($this->model->update_category($_POST) === false) {
							$this->view->add_message("Error updating category.");
							$this->show_category_form($_POST);
						} else {
							$this->user->log_action("category %d updated", $_POST["id"]);
							$this->show_overview();
						}
					}
				} else if ($_POST["submit_button"] == "Delete category") {
					/* Delete category
					 */
					if ($this->model->delete_oke($_POST) == false) {
						$this->show_category_form($_POST);
					} else if ($this->model->delete_category($_POST["id"]) === false) {
						$this->view->add_message("Error deleting category.");
						$this->show_category_form($_POST);
					} else {
						unset($_SESSION["label_category"]);
						$this->user->log_action("category %d deleted", $_POST["id"]);
						$this->show_overview();
					}
				} else {
					$this->show_overview();
				}
			} else if ($this->page->pathinfo[3] === "new") {
				/* New category
				 */
				$category = array();
				$this->show_category_form($category);
			} else if (valid_input($this->page->pathinfo[3], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				/* Edit category
				 */
				if (($category = $this->model->get_category($this->page->pathinfo[3])) === false) {
					$this->view->add_tag("result", "Category not found.");
				} else {
					$this->show_category_form($category);
				}
			} else {
				/* Show overview
				 */
				$this->show_overview();
			}
		}
	}
?>
