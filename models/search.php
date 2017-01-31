<?php
	class search_model extends Banshee\model {
		private $text = null;
		private $add_or = null;

		public function __call($name, $args) {
			return false;
		}

		/* Add selection to query
		 */
		private function add_selection($column, &$query, &$args) {
			if ($this->add_or) {
				$query .= " or ";
			}
			$query .= "(".$column. " like %s)";
			array_push($args, "%".$this->text."%");

			$this->add_or = true;
		}

		/* Add boolean to query
		 */
		private function add_boolean($value, $column, &$query, &$args) {
			if (strtolower($this->text) != $value) {
				return;
			}

			if ($this->add_or) {
				$query .= " or ";
			}

			$query .= "(".$column."=%d)";
			array_push($args, YES);

			$this->add_or = true;
		}

		/* Add enum to query
		 */
		private function add_enum($enum, $column, &$query, &$args) {
			if (in_array($this->text, $enum) == false) {
				return;
			}

			$flipped = array_flip($enum);
			$value = $flipped[$this->text];

			if ($this->add_or) {
				$query .= " or ";
			}

			$query .= "(".$column."=%s)";
			array_push($args, $value);

			$this->add_or = true;
		}

		/* Search applications
		 */
		private function search_applications() {
			$query = "select concat(%s, id) as url, name as text, description as content from applications where organisation_id=%d and (";
			$args = array("/application/", $this->user->organisation_id);
			$this->add_selection("name", $query, $args);
			$this->add_selection("type", $query, $args);
			$this->add_selection("description", $query, $args);
			$this->add_boolean("privacy", "privacy_law", $query, $args);
			$this->add_enum(config_array(CONFIDENTIALITY), "confidentiality", $query, $args);
			$this->add_enum(config_array(INTEGRITY), "integrity", $query, $args);
			$this->add_enum(config_array(AVAILABILITY), "availability", $query, $args);
			$query .= ") order by name desc";

			return $this->db->execute($query, $args);
		}

		/* Search business
		 */
		private function search_business() {
			$query = "select concat(%s, id) as url, name as text, description as content from business where organisation_id=%d and (";
			$args = array("/business/", $this->user->organisation_id);
			$this->add_selection("name", $query, $args);
			$this->add_selection("description", $query, $args);
			$query .= ") order by name desc";

			return $this->db->execute($query, $args);
		}

		/* Search hardware
		 */
		private function search_hardware() {
			$query = "select concat(%s, id) as url, name as text, description as content from hardware where organisation_id=%d and (";
			$args = array("/hardware/", $this->user->organisation_id);
			$this->add_selection("name", $query, $args);
			$this->add_selection("os", $query, $args);
			$this->add_selection("description", $query, $args);
			$query .= ") order by name desc";

			return $this->db->execute($query, $args);
		}

		/* Search connections
		 */
		private function search_connections() {
			$query = "select concat(%s, c.id) as url, c.description as content, ".
			         "(select name from applications where id=c.from_application_id) as from_name, ".
			         "(select name from applications where id=c.to_application_id) as to_name ".
			         "from connections c, applications a where c.from_application_id=a.id and a.organisation_id=%d and (";
			$args = array("/connection/", $this->user->organisation_id);
			$this->add_selection("c.description", $query, $args);
			$this->add_selection("protocol", $query, $args);
			$this->add_selection("format", $query, $args);
			$this->add_selection("frequency", $query, $args);
			$this->add_enum(config_array(DATA_FLOW), "data_flow", $query, $args);
			$query .= ") order by c.description desc";

			if (($result = $this->db->execute($query, $args)) === false) {
				return false;
			}

			foreach ($result as $key => $value) {
				$result[$key]["text"] = $value["from_name"]." => ". $value["to_name"];
			}

			return $result;
		}

		/* Search used-by
		 */
		private function search_usedby() {
			$query = "select concat(%s, u.id) as url, u.description as content, ".
			         "(select name from applications where id=u.application_id) as application, ".
			         "(select name from business where id=u.business_id) as business ".
			         "from used_by u, applications a where u.application_id=a.id and a.organisation_id=%d and (";
			$args = array("/usedby/", $this->user->organisation_id);
			$this->add_selection("u.description", $query, $args);
			$this->add_selection("input", $query, $args);
			$query .= ") order by u.description desc";

			if (($result = $this->db->execute($query, $args)) === false) {
				return false;
			}

			foreach ($result as $key => $value) {
				$result[$key]["text"] = $value["business"]." => ". $value["application"];
			}

			return $result;
		}

		/* Search runs-at
		 */
		private function search_runsat() {
			$query = "select concat(%s, r.id) as url, description as content, ".
			         "(select name from applications where id=r.application_id) as application, ".
			         "(select name from business where id=r.hardware_id) as hardware ".
			         "from runs_at r, applications a where r.application_id=a.id and a.organisation_id=%d and (";
			$args = array("/runsat/", $this->user->organisation_id);
			$this->add_selection("r.description", $query, $args);
			$query .= ") order by r.description desc";

			if (($result = $this->db->execute($query, $args)) === false) {
				return false;
			}

			foreach ($result as $key => $value) {
				$result[$key]["text"] = $value["business"]." => ". $value["application"];
			}

			return $result;
		}

		/* Search the database
		 */
		public function search($post, $sections) {
			$this->text = $post["query"];
			$result = array();

			foreach ($sections as $section => $label) {
				if (is_true($post[$section])) {
					$this->add_or = false;
					$hits = call_user_func(array($this, "search_".$section));
					if ($hits != false) {
						$result[$section] = $hits;
					}
				}
			}

			return $result;
		}
	}
?>
