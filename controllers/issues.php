<?php
	class issues_controller extends Banshee\controller {
		private function show_issues() {
			if (($no_owners = $this->model->get_no_owners()) === false) {
				return false;
			}

			if (($crowded = $this->model->get_crowded_servers()) === false) {
				$this->view->add_tag("result", "Database error.");
				return false;
			}

			if (($no_hardware = $this->model->get_no_hardware()) === false) {
				return false;
			}

			if (($isolated_business = $this->model->get_isolated_business()) === false) {
				return false;
			}

			if (($isolated_hardware = $this->model->get_isolated_hardware()) === false) {
				return false;
			}

			$this->view->open_tag("overview");

			$this->view->open_tag("no_owner");
			foreach ($no_owners as $application) {
				$this->view->record($application, "application");
			}
			$this->view->close_tag();

			$this->view->open_tag("crowded");
			foreach ($crowded as $device) {
				$this->view->record($device, "device");
			}
			$this->view->close_tag();

			$this->view->open_tag("no_hardware");
			foreach ($no_hardware as $application) {
				$this->view->record($application, "application");
			}
			$this->view->close_tag();

			$this->view->open_tag("isolated_business");
			foreach ($isolated_business as $business) {
				$this->view->record($business, "business");
			}
			$this->view->close_tag();

			$this->view->open_tag("isolated_hardware");
			foreach ($isolated_hardware as $hardware) {
				$this->view->record($hardware, "hardware");
			}
			$this->view->close_tag();

			$this->view->close_tag();

			return true;
		}

		public function execute() {
			if ($this->show_issues() == false) {
				$this->view->add_tag("result", "Database error.");
			}
		}
	}
?>
