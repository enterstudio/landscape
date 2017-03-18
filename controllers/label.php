<?php
	class label_controller extends Banshee\controller {
		private function show_overview() {
			if (($cat_labels = $this->model->get_labels()) === false) {
				$this->view->add_tag("result", "Database error.");
				return;
			}

			$this->view->add_help_button();

			$this->view->open_tag("overview");

			foreach ($cat_labels as $category => $labels) {
				$this->view->open_tag("category", array("name" => $category));
				foreach ($labels as $label) {
					$this->view->add_tag("label", $label["name"], array(
						"id"    => $label["id"],
						"count" => $label["app_count"] + $label["bus_count"]));
				}
				$this->view->close_tag();
			}

			$this->view->close_tag();
		}

		private function show_label($label_id) {
			if (($label = $this->model->get_label($label_id)) == false) {
				$this->view->add_tag("result", "Label not found.");
				return;
			}

			if (($applications = $this->model->get_applications($label_id)) === false) {
				$this->view->add_tag("result", "Error getting applications.");
				return;
			}

			if (($business = $this->model->get_business($label_id)) === false) {
				$this->view->add_tag("result", "Error getting business entities.");
				return;
			}

			$this->view->title = $label["category"]." :: ".$label["label"];

			$this->view->open_tag("label", array("previous" => $this->page->previous));

			$this->view->open_tag("applications");
			foreach ($applications as $application) {
				$this->view->record($application, "application");
			}
			$this->view->close_tag();

			$this->view->open_tag("business");
			foreach ($business as $entity) {
				$this->view->record($entity, "entity");
			}
			$this->view->close_tag();

			$this->view->close_tag();
		}

		public function execute() {
			$this->view->title = "Labels";

			if (valid_input($this->page->pathinfo[1], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				$this->show_label($this->page->pathinfo[1]);
			} else {
				$this->show_overview();
			}
		}
	}
?>
