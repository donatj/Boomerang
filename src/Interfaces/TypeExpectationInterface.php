<?php

namespace Boomerang\Interfaces;

interface TypeExpectationInterface {

	/**
	 * Method to pass data to compare against
	 *
	 * @access private
	 * @param mixed $data The data to compare against
	 */
	public function match( $data ) : bool;

	/**
	 * The pretty name of the type for error messages
	 *
	 * @access private
	 */
	public function getMatchingTypeName() : string;

}
