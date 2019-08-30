<?php

namespace TournamentGenerator;

/**
 *
 */
abstract class Base
{

	protected $name = '';
	protected $id = 'null';

	public function __toString() {
		return $this->name;
	}

	public function setName(string $name) {
		$this->name = $name;
		return $this;
	}
	public function getName() {
		return $this->name;
	}
	public function setId($id) {
		if (!is_string($id) && !is_int($id)) {
			$this->id = uniqid();
			throw new \Exception('Unsupported id type ('.gettype($id).') - expected type of string or int');
		}
		$this->id = $id;
		return $this;
	}
	public function getId() {
		return $this->id;
	}

}
