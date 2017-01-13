<?php
	class cms_link_controller extends Banshee\controller {
		/* Show overview
		 */
		private function show_overview() {
			if (($applications = $this->model->get_applications()) === false) {
				$this->view->add_tag("result", "Error retrieving application list.");
				return;
			} else if (count($applications) == 0) {
				$this->view->add_tag("result", "Add some applications first.", array("url" => "cms"));
				return;
			}

			if (isset($_SESSION["application_id"]) == false) {
				$_SESSION["application_id"] = (int)$applications[0]["id"];
			}

			if (($application = $this->model->get_application($_SESSION["application_id"])) == false) {
				unset($_SESSION["application_id"]);
				$this->view->add_tag("result", "Application not found.");
				return;
			}

			if (($connections = $this->model->get_connection_list($_SESSION["application_id"])) === false) {
				$this->view->add_tag("result", "Error while retrieving connections.");
				return;
			}

			if (($used_by = $this->model->get_usedby_list($_SESSION["application_id"])) === false) {
				$this->view->add_tag("result", "Error while retrieving application usage.");
				return;
			}

			if (($runs_at = $this->model->get_runsat_list($_SESSION["application_id"])) === false) {
				$this->view->add_tag("result", "Error while retrieving used hardware.");
				return;
			}

			$this->view->open_tag("overview");

			$this->view->record($application);

			$this->view->open_tag("applications", array("selected" => $_SESSION["application_id"]));
			foreach ($applications as $application) {
				$this->view->record($application, "application");
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

		private function show_application($application_id) {
			if (($application = $this->model->get_application($application_id)) == false) {
				return;
			}

			$this->view->add_tag("application", $application["name"]);
		}

		/* Show connection form
		 */
		private function show_connection_form($connection) {
			if (($applications = $this->model->get_applications()) == false) {
				$this->view->add_tag("result", "Error retrieving application list.");
				return;
			}

			$this->view->add_help_button();

			$attr = isset($connection["id"]) ? array("id" => $connection["id"]) : array();
			$this->view->open_tag("connection", $attr);

			$this->view->record($connection);
			$this->show_application($connection["from_application_id"]);

			$this->view->open_tag("applications");
			foreach ($applications as $application) {
				$this->view->record($application, "application");
			}
			$this->view->close_tag();

			$data_flow = config_array(DATA_FLOW);
			$this->view->open_tag("data_flow");
			foreach ($data_flow as $i => $direction) {
				$this->view->add_tag("direction", $direction, array("id" => $i));
			}
			$this->view->close_tag();

			$this->view->close_tag();
		}

		/* Show used-by form
		 */
		private function show_usedby_form($usedby) {
			if (($business = $this->model->get_business()) == false) {
				$this->view->add_tag("result", "No business entities present. Add some first.");
				return;
			}

			$attr = isset($usedby["id"]) ? array("id" => $usedby["id"]) : array();
			$this->view->open_tag("usedby", $attr);

			$this->view->record($usedby);
			$this->show_application($usedby["application_id"]);

			$this->view->open_tag("business");
			foreach ($business as $entity) {
				$this->view->record($entity, "entity");
			}
			$this->view->close_tag();

			$this->view->close_tag();
		}

		/* Show runs-at form
		 */
		private function show_runsat_form($runsat) {
			if (($hardware = $this->model->get_hardware()) == false) {
				$this->view->add_tag("result", "No hardware present. Add some first.");
				return;
			}

			$attr = isset($runsat["id"]) ? array("id" => $runsat["id"]) : array();
			$this->view->open_tag("runsat", $attr);

			$this->view->record($runsat);
			$this->show_application($runsat["application_id"]);

			$this->view->open_tag("hardware");
			foreach ($hardware as $device) {
				$this->view->record($device, "device");
			}
			$this->view->close_tag();

			$this->view->close_tag();
		}

		/* Execute
		 */
		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["submit_button"] == "set application") {
					$_SESSION["application_id"] = (int)$_POST["application_id"];
					$this->show_overview();
				} else if ($_POST["submit_button"] == "Save connection") {
					/* Save connection
					 */
					if ($this->model->connection_oke($_POST) == false) {
						$this->show_connection_form($_POST);
					} else if ($this->model->save_connection($_POST) == false) {
						$this->view->add_message("Error saving connection.");
						$this->show_connection_form($_POST);
					} else {
						$this->show_overview();
					}
				} else if ($_POST["submit_button"] == "Delete connection") {
					/* Delete connection
					 */
					if ($this->model->delete_connection($_POST["id"]) == false) {
						$this->view->add_message("Error deleting connection.");
						$this->show_connection_form($_POST);
					} else {
						$this->show_overview();
					}
				} else if ($_POST["submit_button"] == "Save used-by") {
					/* Save used-by
					 */
					if ($this->model->usedby_oke($_POST) == false) {
						$this->show_usedby_form($_POST);
					} else if ($this->model->save_usedby($_POST) == false) {
						$this->view->add_message("Error saving used-by.");
						$this->show_usedby_form($_POST);
					} else {
						$this->show_overview();
					}
				} else if ($_POST["submit_button"] == "Delete used-by") {
					/* Delete used-by
					 */
					if ($this->model->delete_usedby($_POST["id"]) == false) {
						$this->view->add_message("Error deleting used-by.");
						$this->show_usedby_form($_POST);
					} else {
						$this->show_overview();
					}
				} else if ($_POST["submit_button"] == "Save runs-at") {
					/* Save runs-at
					 */
					if ($this->model->runsat_oke($_POST) == false) {
						$this->show_runsat_form($_POST);
					} else if ($this->model->save_runsat($_POST) == false) {
						$this->view->add_message("Error saving runs-at.");
						$this->show_runsat_form($_POST);
					} else {
						$this->show_overview();
					}
				} else if ($_POST["submit_button"] == "Delete runs-at") {
					/* Delete runs-at
					 */
					if ($this->model->delete_runsat($_POST["id"]) == false) {
						$this->view->add_message("Error deleting runs-at.");
						$this->show_runsat_form($_POST);
					} else {
						$this->show_overview();
					}
				} else {
					$this->show_overview();
				}
			} else if ($this->page->pathinfo[2] == "connection") {
				/* Connection
				 */
				if (valid_input($this->page->pathinfo[3], VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
					$this->show_connection_form(array("from_application_id" => $_SESSION["application_id"]));
				} else if (($connection = $this->model->get_connection($this->page->pathinfo[3])) == false) {
					$this->view->add_tag("result", "Connection not found.");
				} else {
					$this->show_connection_form($connection);
				}
			} else if ($this->page->pathinfo[2] == "usedby") {
				/* Used by
				 */
				if (valid_input($this->page->pathinfo[3], VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
					$this->show_usedby_form(array("application_id" => $_SESSION["application_id"]));
				} else if (($connection = $this->model->get_usedby($this->page->pathinfo[3])) == false) {
					$this->view->add_tag("result", "Used-by not found.");
				} else {
					$this->show_usedby_form($connection);
				}
			} else if ($this->page->pathinfo[2] == "runsat") {
				/* Runs at
				 */
				if (valid_input($this->page->pathinfo[3], VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
					$this->show_runsat_form(array("application_id" => $_SESSION["application_id"]));
				} else if (($connection = $this->model->get_runsat($this->page->pathinfo[3])) == false) {
					$this->view->add_tag("result", "Runs-at not found.");
				} else {
					$this->show_runsat_form($connection);
				}
			} else {
				$this->show_overview();
			}
		}
	}
?>
