<?php

namespace Boomerang\Validation;

use Boomerang\Interfaces\TypeExpectation;

class HierarchyValidation {

	private $data;
	private $validation;

	function __construct( $data, $validation ) {
		$this->data       = $data;
		$this->validation = $validation;
	}

	public function validate() {
		return $this->__validate($this->data, $this->validation);
	}

	private function __validate( $data, $validation ) {

		see($validation);

		if( is_array($validation) ) {
			$pass = true;

			foreach( $validation as $key => $value ) {
				$pass = $pass && isset($data[$key]) && $this->__validate($data[$key], $value);
			}

			return $pass;
		} elseif( $validation instanceof TypeExpectation ) {
			return $validation->match($data);
		} elseif( $validation instanceof \Closure ) {
			return $validation($data);
		} elseif( is_scalar($validation) ) {
			return $validation == $data;
		}


	}

}