<?php
	class hardware_controller extends Banshee\controller {
		private $url = array("url" => "overview");

		private function show_overview() {
			if (($hardware = $this->model->get_hardware_list()) === false) {
				$this->view->add_tag("result", "Database error.", $this->url);
				return false;
			}

			$this->view->open_tag("overview");
			foreach ($hardware as $device) {
				$this->view->record($device, "device");
			}
			$this->view->close_tag();
		}

		private function show_hardware($hardware_id) {
			if (($hardware = $this->model->get_hardware($hardware_id)) === false) {
				$this->view->add_tag("result", "Hardware not found.", $this->url);
				return false;
			}

			if (($applications = $this->model->get_applications($hardware_id)) === false) {
				$this->view->add_tag("result", "Error getting applications for device.", $this->url);
				return false;
			}

			$this->view->title = $hardware["name"];

			$this->view->open_tag("hardware", array("id" => $hardware_id));

			$this->view->record($hardware);

			$this->view->open_tag("applications");
			foreach ($applications as $application) {
				$this->view->record($application, "application");
			}
			$this->view->close_tag();

			$this->view->close_tag();
		}

		public function execute() {
			$this->view->title = "Hardware";

			if (valid_input($this->page->pathinfo[1], VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
				$this->show_overview();
			} else {
				$this->show_hardware((int)$this->page->pathinfo[1]);
			}
		}
	}
?>
