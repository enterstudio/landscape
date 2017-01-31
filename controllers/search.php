<?php
	require("../libraries/helpers/output.php");

	class search_controller extends Banshee\controller {
		const MIN_QUERY_LENGTH = 2;

		private $sections = array(
			"applications" => "Applications",
			"business"     => "Business",
			"hardware"     => "Hardware",
			"connections"  => "Connections",
			"usedby"       => "Used-by");
			//"runsat"       => "Runs-at");

		/* Search directly in database
		 */
		public function execute() {
			if ($this->user->logged_in == false) {
				unset($this->sections["mailbox"]);
			}

			if (isset($_SESSION["search"]) == false) {
				$_SESSION["search"] = array();
				foreach ($this->sections as $section => $label) {
					$_SESSION["search"][$section] = true;
				}
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$logfile = new Banshee\logfile("search");
				$logfile->add_entry($_POST["query"]);

				foreach ($this->sections as $section => $label) {
					$_SESSION["search"][$section] = is_true($_POST[$section]);
				}
			}

			$this->view->add_css("banshee/js_pagination.css");
			$this->view->add_javascript("banshee/pagination.js");
			$this->view->add_javascript("search.js");
			$this->view->run_javascript("document.getElementById('query').focus()");

			$this->view->add_tag("query", $_POST["query"]);
			$this->view->open_tag("sections");
			foreach ($this->sections as $section => $label) {
				$params = array(
					"label"   => $label,
					"checked" => show_boolean($_SESSION["search"][$section]));
				$this->view->add_tag("section", $section, $params);
			}
			$this->view->close_tag();

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (strlen(trim($_POST["query"])) < self::MIN_QUERY_LENGTH) {
					$this->view->add_tag("result", "Search query too short.");
				} else if (($result = $this->model->search($_POST, $this->sections)) === false) {
					/* Error
					 */
					$this->view->add_tag("result", "Search error.");
				} else if (count($result) == 0) {
					$this->view->add_tag("result", "No matches found.");
				} else {
					/* Results
					 */
					foreach ($result as $section => $hits) {
						$this->view->open_tag("section", array(
							"section" => $section,
							"label"   => $this->sections[$section]));
						foreach ($hits as $hit) {
							$hit["text"] = strip_tags($hit["text"]);
							$hit["content"] = strip_tags($hit["content"]);
							$hit["content"] = preg_replace('/\[.*?\]/', "", $hit["content"]);
							$hit["content"] = truncate_text($hit["content"], 400);
							$this->view->record($hit, "hit");
						}
						$this->view->close_tag();
					}
				}
			}
		}
	}
?>
