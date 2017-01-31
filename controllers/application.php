<?php
	class application_controller extends Banshee\controller {
		private $url = array("url" => "overview");

		private function show_overview() {
			if (($applications = $this->model->get_applications()) === false) {
				$this->view->add_tag("result", "Database error.", $this->url);
				return;
			}

			$confidentiality = config_array(CONFIDENTIALITY);
			$integrity = config_array(INTEGRITY);
			$availability = config_array(AVAILABILITY);

			$this->view->open_tag("overview");
			foreach ($applications as $application) {
				$this->view->record($application, "application");
			}
			$this->view->close_tag();
		}

		private function show_application($application_id) {
			if (($application = $this->model->get_application($application_id)) == false) {
				$this->view->add_tag("result", "Application not found.", $this->url);
				return;
			}

			if (($connections = $this->model->get_connections($application_id)) === false) {
				$this->view->add_tag("result", "Error while retrieving connections.", $this->url);
				return;
			}

			if (($used_by = $this->model->get_used_by($application_id)) === false) {
				$this->view->add_tag("result", "Error while retrieving application usage.", $this->url);
				return;
			}

			if (($runs_at = $this->model->get_runs_at($application_id)) === false) {
				$this->view->add_tag("result", "Error while retrieving used hardware.", $this->url);
				return;
			}

			$this->view->add_javascript("jquery/jquery-ui.js");
			$this->view->add_css("jquery/jquery-ui.css");
			$this->view->add_javascript("dialog.js");

			$this->view->title = $application["name"];

			$this->view->open_tag("application", array("id" => $application_id, "previous" => $this->page->previous));

			$confidentiality = config_array(CONFIDENTIALITY);
			$application["confidentiality"] = $confidentiality[$application["confidentiality"]];
			$integrity = config_array(INTEGRITY);
			$application["integrity"] = $integrity[$application["integrity"]];
			$availability = config_array(AVAILABILITY);
			$application["availability"] = $availability[$application["availability"]];

			$application["external"] = show_boolean($application["external"]);
			$application["privacy_law"] = show_boolean($application["privacy_law"]);
			$this->view->record($application);

			$this->view->open_tag("labels");
			foreach ($application["labels"] as $label) {
				$this->view->add_tag("label", $label["name"], array(
					"id"  => $label["id"],
					"cid" => $label["category_id"]));
			}
			$this->view->close_tag();

			$data_flow = config_array(DATA_FLOW);
			$this->view->open_tag("connections");
			foreach ($connections as $connection) {
				$connection["data_flow"] = $data_flow[$connection["data_flow"]];
				$this->view->record($connection, "connection");
			}
			$this->view->close_tag();

			$this->view->open_tag("used_by");
			foreach ($used_by as $entity) {
				$this->view->record($entity, "entity");
			}
			$this->view->close_tag();

			$this->view->open_tag("runs_at");
			foreach ($runs_at as $device) {
				$this->view->record($device, "device");
			}
			$this->view->close_tag();

			$this->view->close_tag();
		}

		public function execute() {
			$this->view->title = "Application";

			if (valid_input($this->page->pathinfo[1], VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
				$this->show_overview();
			} else {
				$this->show_application((int)$this->page->pathinfo[1]);
			}
		}
	}
?>
