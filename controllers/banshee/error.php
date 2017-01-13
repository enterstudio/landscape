<?php
	class banshee_error_controller extends Banshee\controller {
		public function execute() {
			header("Status: ".$this->page->http_code);

			$this->view->add_tag("website_error", $this->page->http_code);
			$this->view->add_tag("webmaster_email", $this->settings->webmaster_email);
		}
	}
?>
