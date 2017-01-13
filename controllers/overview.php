<?php
	class overview_controller extends Banshee\controller {
		public function execute() {
			if (($applications = $this->model->get_applications()) === false) {
				return false;
			}

			if (($business = $this->model->get_business()) === false) {
				return false;
			}

			if (($hardware = $this->model->get_hardware()) === false) {
				return false;
			}

			$this->view->add_javascript("overview.js");

			$this->view->open_tag("overview");

			/* Applications
			 */
			$this->view->open_tag("applications");
			foreach ($applications as $application) {
				$this->view->record($application, "application");
			}
			$this->view->close_tag();

			/* Business
			 */
			$this->view->open_tag("business");
			foreach ($business as $item) {
				$this->view->record($item, "item");
			}
			$this->view->close_tag();

			/* Hardware
			 */
			$this->view->open_tag("hardware");
			foreach ($hardware as $item) {
				$this->view->record($item, "item");
			}
			$this->view->close_tag();

			$this->view->close_tag();
		}
	}
?>
