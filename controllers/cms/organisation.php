<?php
	class cms_organisation_controller extends Banshee\tablemanager_controller {
		protected $name = "Organisation";
		protected $back = "cms";
		protected $pathinfo_offset = 2;
		protected $icon = "organisations.png";

		public function show_item_form($item) {
			if (valid_input($item["id"], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)) {
				if (($users = $this->model->get_users($item["id"])) !== false) {
					$this->view->open_tag("users");
					foreach ($users as $user) {
						$this->view->record($user, "user");
					}
					$this->view->close_tag();
				}
			}

			parent::show_item_form($item);
		}
	}
?>
