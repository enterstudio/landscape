<?php
	/* libraries/splitform_controller.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	namespace Banshee;

	abstract class splitform_controller extends controller {
		protected $button_previous = "<< prev";
		protected $button_next = "next >>";
		protected $button_submit = "Submit";
		protected $button_back = "Back";
		protected $back = null;

		/* Main function
		 *
		 * INPUT:  array( string key => string value, ... ) form data
		 * OUTPUT: true
		 * ERROR:  false
		 */
		protected function process_form_data($data) {
			print "Splitform controller has no process_form_data() function.\n";
			return false;
		}

		/* Main function
		 *
		 * INPUT:  -
		 * OUTPUT: true
		 * ERROR:  false
		 */
		public function execute() {
			if (is_a($this->model, "Banshee\\splitform_model") == false) {
				print "Splitform model has not been defined.\n";
				return false;
			}

			/* Check class settings
			 */
			if ($this->model->class_settings_oke() == false) {
				return false;
			}

			/* Start
			 */
			$this->view->add_css("banshee/splitform.css");

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($_POST["splitform_current"] != $this->model->current) {
					/* Refresh button pressed
					 */
					$this->model->load_form_data();
				} else if ($_POST["submit_button"] == $this->button_previous) {
					/* Previous button pressed
					 */
					if ($this->model->current > 0) {
						$this->model->save_post_data();
						$this->model->current--;
						$this->model->load_form_data();
					} else {
						return false;
					}
				} else if (($_POST["submit_button"] == $this->button_next) || ($_POST["submit_button"] == $this->button_submit)) {
					/* Next or submit button pressed
					 */
					$this->model->save_post_data();

					if ($this->model->form_data_oke($_POST)) {
						if ($this->model->current < $this->model->max) {
							/* Subform oke
							 */
							$this->model->current++;
							$this->model->load_form_data();
						} else if ($this->process_form_data($this->model->values) == false) {
							/* Submit error
							 */
							$this->model->load_form_data();
						} else {
							/* Submit oke
							 */
							$this->view->open_tag("submit");
							$this->view->add_tag("current", $this->model->max + 1, array("max" => $this->model->max, "percentage" => "100"));
							foreach ($this->model->values as $key => $value) {
								$this->view->add_tag("value", $value, array("key" => $key));
							}
							$this->view->close_tag();

							unset($_SESSION["splitform"][$this->page->module]);
							return true;
						}
					}
				}
			} else {
				$this->model->load_form_data();
			}

			$this->view->open_tag("splitforms");
			$percentage = round(100 * ($this->model->current + 1) / ($this->model->max + 2));
			$this->view->add_tag("current", $this->model->current, array("max" => $this->model->max, "percentage" => $percentage));

			/* Show the webform
			 */
			$template = $this->model->forms[$this->model->current]["template"];

			$this->view->open_tag("splitform");
			$this->view->open_tag($template);
			foreach ($_POST as $key => $value) {
				$this->view->add_tag($key, $value);
			}
			if (method_exists($this, "prepare_".$template)) {
				call_user_func(array($this, "prepare_".$template));
			}
			$this->view->close_tag();
			$this->view->close_tag();

			/* Show the button labels
			 */
			$this->view->open_tag("buttons");
			$this->view->add_tag("previous", $this->button_previous);
			$this->view->add_tag("next", $this->button_next);
			$this->view->add_tag("submit", $this->button_submit);
			if ($this->back !== null) {
				$this->view->add_tag("back", $this->button_back, array("link" => $this->back));
			}
			$this->view->close_tag();

			$this->view->close_tag();

			return true;
		}
	}
?>
