<?php

namespace Boomerang\TypeExpectations;

/**
 * Regex Match Expectation
 *
 * Define a regex matching placeholder
 *
 * @package Boomerang\TypeExpectations
 */
class RegexEx extends StringEx {

	private string $pattern;

	/**
	 * @param string $pattern The preg pattern to search for
	 */
	public function __construct( $pattern ) {
		$this->pattern = $pattern;
	}

	/**
	 * @param mixed $data
	 * @return bool
	 */
	public function match( $data ) {
		return parent::match($data) && (bool)preg_match($this->pattern, $data);
	}

	/**
	 * @return string
	 */
	public function getMatchingTypeName() {
		return 'regex ' . $this->pattern;
	}

}