<?php
	class banshee_page_controller extends Banshee\controller {
		public function execute() {
			if (($page = $this->model->get_page($this->page->url)) == false) {
				$this->view->add_tag("website_error", 500);
				return;
			}

			/* Page header
			 */
			if (trim($page["description"]) != "") {
				$this->view->description = $page["description"];
			}
			if (trim($page["keywords"]) != "") {
				$this->view->keywords = $page["keywords"];
			}
			$this->view->title = $page["title"];
			if ($page["style"] != null) {
				$this->view->inline_css = $page["style"];
			}
			$this->view->language = $page["language"];

			$this->view->set_layout($page["layout"]);

			$this->view->allow_hiawatha_cache();

			/* Page content
			 */
			$this->view->open_tag("page");

			$this->view->add_tag("title", $page["title"]);
			if (is_true(SECURE_XML_DATA)) {
				$page["content"] = $this->view->secure_string($page["content"]);
			}
			$this->view->add_tag("content", $page["content"]);

			if (is_true($page["back"])) {
				$parts = explode("/", $this->page->page);
				array_pop($parts);
				$this->view->add_tag("back", implode("/", $parts));
			}

			$this->view->close_tag();
		}
	}
?>
