<?php
	class list_controller extends Banshee\controller {
		private $list_types = array(
			"privacy"   => "Applications to which the privacy law is applicable",
			"protocols" => "Protocols used in connections",
			"input"     => "Manual input of information");

		private function show_list() {
			$function = "get_".$_SESSION["listtype"];
			if (method_exists($this->model, $function) == false) {
				$this->view->add_tag("result", "Internal error.");
				return;
			}

			if (($values = $this->model->$function()) === false) {
				$this->view->add_tag("result", "Database error.");
				return;
			}

			$this->view->open_tag("list");

			$this->view->open_tag("types", array("selected" => $_SESSION["listtype"]));
			foreach ($this->list_types as $type => $label) {
				$this->view->add_tag("option", $label, array("type" => $type));
			}
			$this->view->close_tag();

			$this->view->open_tag($_SESSION["listtype"]);
			foreach ($values as $value) {
				$this->view->record($value, "record");
			}
			$this->view->close_tag();

			$this->view->close_tag();
		}

		public function execute() {
			$types = array_keys($this->list_types);

			if (isset($_SESSION["listtype"]) == false) {
				$_SESSION["listtype"] = $types[0];
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (in_array($_POST["type"], $types)) {
					$_SESSION["listtype"] = $_POST["type"];
				}
			} else if (in_array($this->page->pathinfo[1], $types)) {
				$_SESSION["listtype"] = $this->page->pathinfo[1];
			}

			$this->show_list();
		}
	}
?>
