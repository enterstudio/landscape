<?php
	class business_controller extends Banshee\controller {
		private $url = array("url" => "overview");

		private function show_overview() {
			if (($business = $this->model->get_business_list()) === false) {
				$this->view->add_tag("result", "Database error.", $this->url);
				return false;
			}

			$this->view->open_tag("overview");
			foreach ($business as $entity) {
				$this->view->record($entity, "entity");
			}
			$this->view->close_tag();
		}

		private function show_business($business_id) {
			if (($business = $this->model->get_business($business_id)) === false) {
				$this->view->add_tag("result", "Business entity not found.", $this->url);
				return false;
			}

			if (($ownership = $this->model->get_application_ownership($business_id)) === false) {
				$this->view->add_tag("result", "Error getting application ownership.", $this->url);
				return false;
			}

			if (($usage = $this->model->get_application_usage($business_id)) === false) {
				$this->view->add_tag("result", "Error getting application usage.", $this->url);
				return false;
			}

			$this->view->add_javascript("jquery/jquery-ui.js");
			$this->view->add_css("jquery/jquery-ui.css");
			$this->view->add_javascript("dialog.js");

			$this->view->title = $business["name"];

			$this->view->open_tag("business", array("id" => $business_id));

			$this->view->record($business);

			$this->view->open_tag("usage");
			foreach ($usage as $item) {
				$this->view->record($item, "item");
			}
			$this->view->close_tag();

			$this->view->open_tag("ownership");
			foreach ($ownership as $item) {
				$this->view->record($item, "item");
			}
			$this->view->close_tag();

			$this->view->close_tag();
		}

		public function execute() {
			$this->view->title = "Business";

			if (valid_input($this->page->pathinfo[1], VALIDATE_NUMBERS, VALIDATE_NONEMPTY) == false) {
				$this->show_overview();
			} else {
				$this->show_business((int)$this->page->pathinfo[1]);
			}
		}
	}
?>
