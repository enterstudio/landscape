<?php
	/* Get width in characters of current terminal
	 */
	function get_terminal_width() {
		$view = explode(";", exec("stty -a | grep columns"));

		foreach ($view as $line) {
			list($key, $value) = explode(" ", ltrim($line), 2);
			if ($key == "columns") {
				return (int)$value;
			}
		}

		return 80;
	}

	/* Copy file, but don't overwrite
	 */
	function safe_copy($source, $dest) {
		if (file_exists($source) == false) {
			return false;
		} else if (file_exists($dest)) {
			printf("Warning, destination file already exists: %s\n", $dest);
			return false;
		}

		copy($source, $dest);

		return true;
	}

	/* Add layout to website
	 */
	function add_layout_to_website($name) {
		$filename = __DIR__."/../../views/banshee/main.xslt";

		if (($file = file($filename)) === false) {
			return false;
		}

		if (($fp = fopen($filename, "w")) == false) {
			return false;
		}

		$include = false;
		foreach ($file as $i => $line) {
			$text = chop($line);

			if (substr($text, 0, 11) == "<xsl:import") {
				$include = true;
			} else if (($text == "") && $include) {
				fputs($fp, "<xsl:import href=\"layout_".$name.".xslt\" />\n");
				$include = false;
			} else if ($text == "</xsl:template>") {
				fputs($fp, "<xsl:apply-templates select=\"layout_".$name."\" />\n");
			}

			fputs($fp, $line);
		}

		fclose($fp);

		return true;
	}

	/* Activate new layout
	 */
	function activate_layout($name) {
		$filename = __DIR__."/../../settings/website.conf";

		if (($file = file($filename)) === false) {
			return false;
		}

		foreach ($file as $i => $line) {
			if (substr($line, 0, 11) == "LAYOUT_SITE") {
				$file[$i] = "LAYOUT_SITE = ".$name."\n";
			}
		}

		if (($fp = fopen($filename, "w")) == false) {
			return false;
		}

		fputs($fp, implode("", $file));
		fclose($fp);
	}
?>
