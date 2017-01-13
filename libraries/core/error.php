<?php
	/* libraries/core/error.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 *
	 * Don't change this file, unless you know what you are doing.
	 */

	namespace Banshee\Core;

	/* Exception handler
	 *
	 * INPUT:  error object
	 * OUTPUT: -
	 * ERROR:  -
	 */
	function exception_handler($error) {
		print "<!DOCTYPE html><html><body>\n";
		print "<h1>Banshee exception</h1>\n";

		if (is_true(DEBUG_MODE)) {
			printf("<p style=\"white-space:pre-wrap\">%s</p>\n", $error->getMessage());
			printf("<p>line %d in %s.</p>\n",  $error->getLine(), $error->getFile());
		} else {
			printf("<p>Contact your website administrator to solve this issue.</p>\n");
			$message = sprintf("%s\nline %d in %s\n", $error->getMessage(), $error->getLine(), $error->getFile());
			$error_handler = new website_error_handler($GLOBALS["_view"], $GLOBALS["_settings"], $GLOBALS["_user"]);
			$error_handler->execute($message);
		}

		print "</body></html>\n";
	}

	/* Error handler
	 *
	 * INPUT:  int error number, string error string, string filename, int line number
	 * OUTPUT: -
	 * ERROR:  -
	 */
	function error_handler($errno, $errstr, $errfile, $errline) {
		printf("%s\nline %d in %s\n", $errstr, $errline, $errfile);

		return true;
	}

	/* Generate backtrace
	 *
	 * INPUT:  -
	 * OUTPUT: -
	 * ERROR:  -
	 */
	function error_backtrace() {
		$trace = debug_backtrace();
		array_shift($trace);

		$path_offset = strlen(__FILE__) - 24;

		$result = "\nBacktrace:\n";
		foreach ($trace as $step) {
			$result .= sprintf("- %s() at line %d in %s.\n", $step["function"], $step["line"], substr($step["file"], $path_offset));
		}

		return $result;
	}

	/* Website error handler class
	 */
	final class website_error_handler {
		private $view = null;
		private $settings = null;
		private $user = null;

		/* Constructor
		 *
		 * INPUT:  object view, object settings, object user
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function __construct($view, $settings, $user) {
			$this->view = $view;
			$this->settings = $settings;
			$this->user = $user;
		}

		/* Add errors to output
		 *
		 * INPUT:  string errors
		 * OUTPUT: -
		 * ERROR:  -
		 */
		private function add_to_output($errors) {
			$errors = htmlentities($errors);
			$errors = str_replace("\t", "    ", $errors);
			$errors = explode("\n", $errors);

			$result = "";
			foreach ($errors as $error) {
				$len = strlen($error);
				$error = ltrim($error);
				if (($len = $len - strlen($error)) > 0) {
					$result .= str_repeat("&nbsp;", $len);
				}
				$result .= $error."<br />\n";
			}

			$this->view->add_css("banshee/internal_error.css");
			$this->view->add_tag("internal_errors", $result);
		}

		/* Send errors via e-mail to webmaster
		 *
		 * INPUT:  string errors
		 * OUTPUT: -
		 * ERROR:  -
		 */
		private function send_via_email($errors) {
			$message =
				"Date, time: ".date("j F Y, H:i:s")."\n".
				"Used URL  : ".$_SERVER["REQUEST_URI"]."\n".
				"IP address: ".$_SERVER["REMOTE_ADDR"]."\n".
				"Username  : ".($this->user->username != null ? $this->user->username."\n" : "-\n").
				"User-Agent: ".$_SERVER["HTTP_USER_AGENT"]."\n".
				"\n".$errors;

			$email = new \Banshee\email("Internal error at ".$_SERVER["SERVER_NAME"], "no-reply@".$_SERVER["SERVER_NAME"]);
			$email->message($message);
			$email->send($this->settings->webmaster_email);
			unset($email);
		}

		/* Handle website errors
		 *
		 * INPUT:  string errors
		 * OUTPUT: -
		 * ERROR:  -
		 */
		public function execute($errors) {
			$errors = str_replace("<br />", "", trim($errors));

			if (is_false(DEBUG_MODE) || $this->view->disabled) {
				$this->send_via_email($errors);
			} else {
				$this->add_to_output($errors);
			}
		}
	}

	/* Error handling settings
	 */
	ini_set("display_errors", 1);
	error_reporting(E_ALL & ~E_NOTICE);
	set_exception_handler("Banshee\\Core\\exception_handler");
	set_error_handler("Banshee\\Core\\error_handler", E_ALL & ~E_NOTICE);
?>
