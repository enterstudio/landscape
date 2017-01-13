<?php
	/* libraries/core/banshee.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 *
	 * Don't change this file, unless you know what you are doing.
	 */

	define("BANSHEE_VERSION", "6.0");
	define("ADMIN_ROLE_ID", 1);
	define("USER_ROLE_ID", 2);
	define("YES", 1);
	define("NO", 0);
	define("USER_STATUS_DISABLED", 0);
	define("USER_STATUS_CHANGEPWD", 1);
	define("USER_STATUS_ACTIVE", 2);
	define("PASSWORD_HASH", "sha256");
	define("PASSWORD_ITERATIONS", 100000);
	define("PASSWORD_MIN_LENGTH", 8);
	define("PASSWORD_MAX_LENGTH", 1000);
	define("SESSION_NAME", "banshee_session_id");
	define("SESSION_LOGIN", "banshee_login_id");
	define("HOUR", 3600);
	define("DAY", 86400);
	define("LOG_DAYS", 60);
	define("PAGE_MODULE", "banshee/page");
	define("ERROR_MODULE", "banshee/error");
	define("LOGIN_MODULE", "banshee/login");
	define("LOGOUT_MODULE", "logout");
	define("PROFILE_MODULE", "profile");
	define("FPDF_FONTPATH", "../extra/fpdf_fonts/");
	define("K_PATH_FONTS", "../extra/tcpdf_fonts/");
	define("PHOTO_PATH", "photos");
	define("FILES_PATH", "files");
	define("TLS_CERT_SERIAL_VAR", "TLS_CERT_SERIAL");

	/* Auto class loader
	 *
	 * INPUT:  string class name
	 * OUTPUT: -
	 * ERROR:  -
	 */
	function __autoload($class_name) {
		$parts = explode("\\", $class_name);
		$class = array_pop($parts);

		if (strtolower($parts[0]) == "banshee") {
			/* Load Banshee library
			 */
			array_shift($parts);
			$class = strtolower($class);
			$path = __DIR__."/../".strtolower(implode("/", $parts));

			$rename = array(
				"https"      => "http",
				"jpeg_image" => "image",
				"png_image"  => "image",
				"gif_image"  => "image",
				"pop3s"      => "pop3");

			if (isset($rename[$class])) {
				$class = $rename[$class];
			}
		} else {
			/* Load third party library
			 */
			$path = null;

			$composer = "../libraries/thirdparty/composer/autoload_psr4.php";
			if (file_exists($composer)) {
				$classes = require($composer);
				$key = implode("\\", $parts)."\\";
				if ($classes[$key][0] != null) {
					$path = $classes[$key][0];
				}
			}

			if ($path == null) {
				array_unshift($parts, "thirdparty");
				while (count($parts) > 0) {
					$path = __DIR__."/../".strtolower(implode("/", $parts));
					if (file_exists($path)) {
						break;
					}

					$part = array_pop($parts);
					$class = $part."/".$class;
				}
			}
		}

		if (file_exists($file = $path."/".strtolower($class).".php")) {
			include_once($file);
		} else if (file_exists($file = $path."/".$class.".php")) {
			include_once($file);
		}
	}

	/* Convert mixed to boolean
	 *
	 * INPUT:  mixed
	 * OUTPUT: boolean
	 * ERROR:  -
	 */
	function is_true($bool) {
		if (is_string($bool)) {
			$bool = strtolower($bool);
		}

		return in_array($bool, array(true, YES, "1", "yes", "true", "on"), true);
	}

	/* Convert mixed to boolean
	 *
	 * INPUT:  mixed
	 * OUTPUT: boolean
	 * ERROR:  -
	 */
	function is_false($bool) {
		return (is_true($bool) === false);
	}

	/* Convert boolean to string
	 *
	 * INPUT:  boolean
	 * OUTPUT: string "yes"|"no"
	 * ERROR:  -
	 */
	function show_boolean($bool) {
		return (is_true($bool) ? "yes" : "no");
	}

	/* Convert a page path to a module path
	 *
	 * INPUT:  array / string page path
	 * OUTPUT: array / string module path
	 * ERROR:  -
	 */
	function page_to_module($page) {
		if (is_array($page) == false) {
			if (($pos = strrpos($page, ".")) !== false) {
				$page = substr($page, 0, $pos);
			}
		} else foreach ($page as $i => $item) {
			$page[$i] = page_to_module($item);
		}

		return $page;
	}

	/* Convert a page path to a page type
	 *
	 * INPUT:  array / string page path
	 * OUTPUT: array / string page type
	 * ERROR:  -
	 */
	function page_to_type($page) {
		if (is_array($page) == false) {
			if (($pos = strrpos($page, ".")) !== false) {
				$page = substr($page, $pos);
			} else {
				$page = "";
			}
		} else foreach ($page as $i => $item) {
			$page[$i] = page_to_type($item);
		}

		return $page;
	}

	/* Check for module existence
	 *
	 * INPUT:  string module
	 * OUTPUT: bool module exists
	 * ERROR:  -
	 */
	function module_exists($module, $warn = false) {
		foreach (array("public", "private") as $type) {
			if (in_array($module, config_file($type."_modules"))) {
				if ($warn) {
					printf("There already exists a %s module '%s'.\n", $type, $module);
				}
				return true;
			}
		}

		return false;
	}

	/* Check for library existence
	 *
	 * INPUT:  string library
	 * OUTPUT: bool library exists
	 * ERROR:  -
	 */
	function library_exists($library) {
		return file_exists(__DIR__."/../".$library.".php");
	}

	/* Check for table existence
	 *
	 * INPUT:  db database object, string table name
	 * OUTPUT: bool table exists
	 * ERROR:  -
	 */
	function table_exists($db, $table) {
		if (($result = $db->execute("show tables like %s", $table)) == false) {
			return false;
		}

		return count($result[0]) > 0;
	}

	/* Handle table sort
	 */
	function handle_table_sort($key, $columns, $default) {
		if (isset($_SESSION[$key]) == false) {
			$_SESSION[$key] = $default;
		}

		if (isset($_GET["order"]) == false) {
			return;
		}

		if (in_array($_GET["order"], $columns) == false) {
			return;
		}

		if (is_array($default) == false) {
			$_SESSION[$key] = $_GET["order"];
			return;
		}

		$max = count($default) - 1;
		for ($i = 0; $i < $max; $i++) {
			if ($_SESSION[$key][$i] == $_GET["order"]) {
				return;
			}
		}

		array_pop($_SESSION[$key]);
		array_unshift($_SESSION[$key], $_GET["order"]);
	}

	/* Log debug information
	 *
	 * INPUT:  string format[, mixed arg...]
	 * OUTPUT: true
	 * ERROR:  false
	 */
	function debug_log($info) {
		if (func_num_args() > 1) {
			$args = func_get_args();
			array_shift($args);
			$info = vsprintf($action, $args);
		} else if (is_array($info)) {
			foreach ($info as $key => $value) {
				$info[$key] = "\t".$key." => ".chop($value);
			}
			$info = "array:\n".implode("\n", $info);
		}

		$logfile = new logfile("debug");
		$logfile->add_entry("%s|%s|%s|%s", $_SERVER["REMOTE_ADDR"], date("D d M Y H:i:s"), $_SERVER["REQUEST_URI"], $info);

		return true;
	}

	/* Flatten array to new array with depth 1
	 *
	 * INPUT:  array data
	 * OUTPUT: array data
	 * ERROR:  -
	 */
	function array_flatten($data) {
		$result = array();
		foreach ($data as $item) {
			if (is_array($item)) {
				$result = array_merge($result, array_flatten($item));
			} else {
				array_push($result, $item);
			}
		}

		return $result;
	}

	/* Localized date string
	 *
	 * INPUT:  string format[, integer timestamp]
	 * OUTPUT: string date
	 * ERROR:  -
	 */
	function date_string($format, $timestamp = null) {
		if ($timestamp === null) {
			$timestamp = time();
		}

		$days_of_week = config_array(DAYS_OF_WEEK);
		$months_of_year = config_array(MONTHS_OF_YEAR);

		$format = strtr($format, "lDFM", "#$%&");
		$result = date($format, $timestamp);

		$day = $days_of_week[(int)date("N", $timestamp) - 1];
		$result = str_replace("#", $day, $result);

		$day = substr($days_of_week[(int)date("N", $timestamp) - 1], 0, 3);
		$result = str_replace("$", $day, $result);

		$month = $months_of_year[(int)date("n", $timestamp) - 1];
		$result = str_replace("%", $month, $result);

		$month = substr($months_of_year[(int)date("n", $timestamp) - 1], 0, 3);
		$result = str_replace("&", $month, $result);

		return $result;
	}

	/* Load configuration file
	 *
	 * INPUT:  string configuration file[, bool remove comments]
	 * OUTPUT: array( key => value[, ...] )
	 * ERROR:  -
	 */
	function config_file($config_file, $remove_comments = true) {
		static $cache = array();

		if (isset($cache[$config_file])) {
			return $cache[$config_file];
		}

		$first_char = substr($config_file, 0, 1);
		if (($first_char != "/") && ($first_char != ".")) {
			$config_file = __DIR__."/../../settings/".$config_file.".conf";
		}
		if (file_exists($config_file) == false) {
			return array();
		}

		$config = array();
		foreach (file($config_file) as $line) {
			if ($remove_comments) {
				$line = trim(preg_replace("/(^|\s)#.*/", "", $line));
			}
			$line = rtrim($line);

			if ($line === "") {
				continue;
			}

			if (($prev = count($config) - 1) == -1) {
				array_push($config, $line);
			} else if (substr($config[$prev], -1) == "\\") {
				$config[$prev] = rtrim(substr($config[$prev], 0, strlen($config[$prev]) - 1)) . ltrim($line);
			} else {
				array_push($config, $line);
			}
		}

		$cache[$config_file] = $config;

		return $config;
	}

	/* Convert configuration line to array
	 *
	 * INPUT:  string config line[, bool look for key-value
	 * OUTPUT: array config line
	 * ERROR:  -
	 */
	function config_array($line, $key_value = true) {
		$items = explode("|", $line);

		if ($key_value == false) {
			return $items;
		}

		$result = array();
		foreach ($items as $item) {
			list($key, $value) = explode(":", $item, 2);
			if ($value === null) {
				array_push($result, $key);
			} else {
				$result[$key] = $value;
			}
		}

		return $result;
	}

	/* Website configuration
	 */
	if (isset($_ENV["banshee_config_file"])) {
		$config_file = $_ENV["banshee_config_file"];
	} else {
		$config_file = "website";
	}

	foreach (config_file($config_file) as $line) {
		list($key, $value) = explode("=", chop($line), 2);
		define(trim($key), trim($value));
	}

	/* Check PHP version and settings
	 */
	if (version_compare(PHP_VERSION, "7") < 0) {
		exit("This system uses an unsupported PHP version. Use at least PHP 7.");
	}
	ini_set("zlib.output_compression", "Off");
	if (ini_get("allow_url_include") != 0) {
		exit("Set 'allow_url_include' to 0.");
	}
?>
