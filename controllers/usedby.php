<?php
	class usedby_controller extends Banshee\controller {
		private $url = array("url" => "overview");

		private function show_usedby($id) {
			if (($usedby = $this->model->get_usedby($id)) == false) {
				$this->view->add_tag("result", "Used-by not found.", $this->url);
				return;
			}

			$this->view->record($usedby, "usedby");
		}

		public function execute() {
			if (valid_input($this->page->pathinfo[1], VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
				$this->view->add_tag("result", "No used-by specified.", $this->url);
				return;
			}

			$this->show_usedby($this->page->pathinfo[1]);
		}
	}
?>
