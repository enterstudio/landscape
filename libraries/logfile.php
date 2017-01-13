<?php
	/* libraries/logfile.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	namespace Banshee;

	class logfile {
		private $type = null;
		private $entries = array();
		private $user_id = null;

		public function __construct($type) {
			$this->type = $type;
		}

		public function __destruct() {
			$this->flush();
		}

		public function __set($key, $value) {
			switch ($key) {
				case "user_id": $this->user_id = ($value === null) ? "-" : $value;
			}
		}

		private function flush() {
			if (($fp = fopen(__DIR__."/../logfiles/".$this->type.".log", "a")) == false) {
				return;
			}

			$remote_addr = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "localhost";

			foreach ($this->entries as $entry) {
				$entry = sprintf("%s|%s\n", $remote_addr, $entry);
				fputs($fp, $entry);
			}

			fclose($fp);

			$this->entries = array();
		}

		public function add_entry($entry) {
			if (func_num_args() > 1) {
				$args = func_get_args();
				array_shift($args);
				$entry = vsprintf($entry, $args);
			}

			if ($this->user_id !== null) {
				$entry = sprintf("%s|%s", $this->user_id, $entry);
			}

			$entry = sprintf("%s|%s", date("D d M Y H:i:s"), $entry);

			array_push($this->entries, $entry);
		}

		public function add_variable($variable) {
			if (is_array($variable)) {
				foreach ($variable as $key => $value) {
					$info[$key] = "\t".$key." => ".$value;
				}
				$variable = "array:\n".implode("\n", $info);
			}

			$this->add_entry($variable);
		}
	}
?>
