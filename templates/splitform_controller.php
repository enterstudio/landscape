<?php
	class XXX_controller extends Banshee\splitform_controller {
		protected $back = "demos";

		protected function process_form_data($data) {
			return true;
		}

		public function execute() {
			$this->model->default_value("key1", "Hello world");
			parent::execute();
		}
	}
?>
