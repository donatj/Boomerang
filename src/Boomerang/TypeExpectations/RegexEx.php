<?php

namespace Boomerang\TypeExpectations;

/**
 * Regex Match Expectation
 *
 * Define a regex matching placeholder
 */
class RegexEx extends StringEx {

	private $pattern;

	/**
	 * @param string $pattern The preg pattern to search for
	 */
	public function __construct( $pattern ) {
		$this->pattern = $pattern;
	}

	public function match( $data ) : bool {
		return parent::match($data) && preg_match($this->pattern, $data);
	}

	public function getMatchingTypeName() : string {
		return 'regex ' . $this->pattern;
	}

}
