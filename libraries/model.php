<?php
	/* libraries/model.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	namespace Banshee;

	abstract class model {
		protected $db = null;
		protected $settings = null;
		protected $user = null;
		protected $page = null;
		protected $view = null;
		protected $language = null;

		/* Constructor
		 *
		 * INPUT: object database, object settings, object user, object page, object view[, object language]
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($database, $settings, $user, $page, $view, $language = null) {
			$this->db = $database;
			$this->settings = $settings;
			$this->user = $user;
			$this->page = $page;
			$this->view = $view;
			$this->language = $language;
		}

		/* Borrow function from other model
		 *
		 * INPUT:  string module name
		 * OUTPUT: object model
		 * ERROR:  null
		 */
		protected function borrow($module) {
			if (file_exists($file = "../models/".$module.".php") == false) {
				header("Content-Type: text/plain");
				printf("Can't borrow model '%s'.\n", $module);
				print Core\error_backtrace();
				exit();
			}

			require_once($file);

			$model_class = str_replace("/", "_", $module)."_model";
			if (class_exists($model_class) == false) {
				return null;
			} else if (is_subclass_of($model_class, "Banshee\\model") == false) {
				return null;
			}

			return new $model_class($this->db, $this->settings, $this->user, $this->page, $this->view, $this->language);
		}
	}
?>
