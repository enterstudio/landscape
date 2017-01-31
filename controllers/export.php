<?php
	class export_controller extends Banshee\controller {
		/* Applications
		 */
		private function add_applications($xml) {
			if (($applications = $this->model->get_applications()) === false) {
				return false;
			}

			$xml->open_tag("folder", array(
				"name" => "Application",
				"id"   => "application",
				"type" => "application"));

			foreach ($applications as $application) {
				$xml->open_tag("element", array(
					"xsi:type" => "archimate:ApplicationComponent",
					"id"       => "app_".$application["id"],
					"name"     => $application["name"]));
				if ($application["description"] != "") {
					$xml->add_tag("documentation", $application["description"]);
				}
				$xml->add_tag("property", null, array(
					"key"   => "Type",
					"value" => $application["type"]));
				$xml->close_tag();
			}

			$xml->close_tag();
		}

		/* Business
		 */
		private function add_business($xml) {
			if (($business = $this->model->get_business()) === false) {
				return false;
			}

			if (($applications = $this->model->get_applications()) === false) {
				return false;
			}

			$owners = array();
			foreach ($applications as $application) {
				array_push($owners, $application["owner_id"]);
			}
			$owners = array_unique($owners);

			$xml->open_tag("folder", array(
				"name" => "Business",
				"id"   => "business",
				"type" => "business"));

			foreach ($business as $entity) {
				$type = in_array($entity["id"], $owners) ? "Function" : "Actor";

				$xml->open_tag("element", array(
					"xsi:type" => "archimate:Business".$type,
					"id"       => "bus_".$entity["id"],
					"name"     => $entity["name"]));
				if ($entity["description"] != "") {
					$xml->add_tag("documentation", $entity["description"]);
				}
				$xml->close_tag();
			}

			$xml->close_tag();
		}

		/* Hardware
		 */
		private function add_hardware($xml) {
			if (($hardware = $this->model->get_hardware()) === false) {
				return false;
			}

			$xml->open_tag("folder", array(
				"name" => "Technology",
				"id"   => "technology",
				"type" => "technology"));

			foreach ($hardware as $device) {
				$xml->open_tag("element", array(
					"xsi:type" => "archimate:Device",
					"id"       => "hdw_".$device["id"],
					"name"     => $device["name"]));
				if ($device["description"] != "") {
					$xml->add_tag("documentation", $device["description"]);
				}
				$xml->add_tag("property", null, array(
					"key"   => "OS",
					"value" => $device["os"]));
				$xml->close_tag();
			}

			$xml->close_tag();
		}

		/* Links
		 */
		private function add_links($xml) {
			$xml->open_tag("folder", array(
				"name" => "Relations",
				"id"   => "relations",
				"type" => "relations"));

			/* Connections
			 */
			if (($links = $this->model->get_connections()) != false) {
				$data_flow = config_array(DATA_FLOW);
				$properties = array("data_flow", "protocol", "format", "frequency");
				foreach ($links as $link) {
					$xml->open_tag("element", array(
						"xsi:type" => "archimate:FlowRelationship",
						"name"     => $link["protocol"],
						"id"       => "con_".$link["id"],
						"source"   => "app_".$link["from_application_id"],
						"target"   => "app_".$link["to_application_id"]));
					if ($link["description"] != "") {
						$xml->add_tag("documentation", $link["description"]);
					}

					$link["data_flow"] = $data_flow[$link["data_flow"]];
					foreach ($properties as $property) {
						if ($link[$property] != null) {
							$xml->add_tag("property", null, array(
								"key"   => $property,
								"value" => $link[$property]));
						}
					}
					$xml->close_tag();
				}
			}

			/* Used-by
			 */
			if (($links = $this->model->get_usedby()) != false) {
				foreach ($links as $link) {
					$xml->open_tag("element", array(
						"xsi:type" => "archimate:UsedByRelationship",
						"id"       => "use_".$link["id"],
						"source"   => "app_".$link["application_id"],
						"target"   => "bus_".$link["business_id"]));
					if ($link["description"] != "") {
						$xml->add_tag("documentation", $link["description"]);
					}
					$xml->add_tag("property", null, array(
						"key"   => "Input",
						"value" => $link["input"]));
					$xml->close_tag();
				}
			}

			/* Runs-at
			 */
			if (($links = $this->model->get_runsat()) != false) {
				foreach ($links as $link) {
					$xml->add_tag("element", null, array(
						"xsi:type" => "archimate:RealisationRelationship",
						"id"       => "run_".$link["id"],
						"source"   => "hdw_".$link["hardware_id"],
						"target"   => "app_".$link["application_id"]));
				}
			}

			/* Ownership
			 */
			if (($applications = $this->model->get_applications()) != false) {
				foreach ($applications as $application) {
					if ($application["owner_id"] != null) {
						$xml->add_tag("element", null, array(
							"xsi:type" => "archimate:AssignmentRelationship",
							"id"       => "own_".$application["id"],
							"source"   => "app_".$application["id"],
							"target"   => "bus_".$application["owner_id"]));
					}
				}
			}

			$xml->close_tag();
		}

		/* Views
		 */
		private function add_views($xml) {
			$xml->open_tag("folder", array(
				"name" => "Views",
				"id"   => "views",
				"type" => "diagrams"));

			$xml->add_tag("element", null, array(
				"xsi:type" => "archimate:ArchimateDiagramModel",
				"id"       => "view",
				"name"     => "Application landscape"));

			$xml->close_tag();
		}

		/* Export landscape
		 */
		private function export_landscape() {
			$xml = new Banshee\Core\XML;

			$xml->open_tag("archimate:model", array(
				"xmlns:xsi"       => "http://www.w3.org/2001/XMLSchema-instance",
				"xmlns:archimate" => "http://www.archimatetool.com/archimate",
				"name"            => "Application landscape",
				"id"              => "landscape",
				"version"         => "2.6.0"));

			$this->add_applications($xml);
			$this->add_business($xml);
			$this->add_hardware($xml);
			$this->add_links($xml);
			$this->add_views($xml);

			$xml->close_tag();

			$this->view->disable();
			header("Content-Type: application/xml");
			header("Content-Disposition: attachment; filename=\"Application landscape.archimate\"");
			print $xml->document;
		}

		/* Execute
		 */
		public function execute() {
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$this->export_landscape();
			}
		}
	}
?>
