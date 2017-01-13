<?php
	class cms_settings_controller extends Banshee\tablemanager_controller {
		protected $name = "Setting";
		protected $pathinfo_offset = 2;
		protected $back = "cms";
		protected $icon = "settings.png";
		protected $page_size = 25;
		protected $pagination_links = 7;
		protected $pagination_step = 1;
		protected $foreign_null = "---";

		protected function show_item_form($item) {
			if ((is_true(DEBUG_MODE) == false) && isset($item["id"])) {
				if (($current = $this->model->get_item($item["id"])) === false) {
					$this->view->add_tag("result", "Database error.");
					return false;
				}

				$this->view->add_javascript("cms/settings.js");

				$this->view->open_tag("label");
				$this->view->add_tag("key", $current["key"]);
				$this->view->add_tag("type", $current["type"]);
				$this->view->close_tag();
			}

			parent::show_item_form($item);
		}

		protected function handle_submit() {
			parent::handle_submit();

			$cache = new Banshee\Core\cache($this->db, "banshee_settings");
			$cache->store("last_updated", time(), 365 * DAY);
		}
	}
?>
