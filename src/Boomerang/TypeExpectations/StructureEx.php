<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Interfaces\TypeExpectation;

class StructureEx implements TypeExpectation {

	protected $structure;

	function __construct( $structure ) {
		$this->structure = $structure;
	}

	public function match( $data ) {
		return $this->__validate($data, $this->structure);
	}

	protected function __validate( $data, $validation ) {

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