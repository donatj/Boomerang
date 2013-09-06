<?php

namespace Boomerang\TypeExpectations;

class RegexEx extends StringEx {

	private $pattern;

	function __construct( $pattern ) {
		$this->pattern = $pattern;
	}

	public function match( $data ) {
		return parent::match($data) && preg_match( $this->pattern, $data );
	}

	public function getMatchingTypeName() {
		return 'regex ' . $this->pattern;
	}

}