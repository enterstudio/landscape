<?php
	class connection_controller extends Banshee\controller {
		private $url = array("url" => "overview");

		private function show_connection($id) {
			if (($connection = $this->model->get_connection($id)) == false) {
				$this->view->add_tag("result", "Connection not found.", $this->url);
				return;
			}

			$flow = config_array(DATA_FLOW);
			$connection["data_flow"] = $flow[$connection["data_flow"]];
			$this->view->record($connection, "connection");
		}

		public function execute() {
			if (valid_input($this->page->pathinfo[1], VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
				$this->view->add_tag("result", "No connection specified.", $this->url);
				return;
			}

			$this->show_connection($this->page->pathinfo[1]);
		}
	}
?>
