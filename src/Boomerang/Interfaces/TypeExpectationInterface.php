<?php

namespace Boomerang\Interfaces;

interface TypeExpectationInterface {

	/**
	 * Method to pass data to compare against
	 *
	 * @access private
	 * @param $data
	 * @return bool
	 */
	public function match( $data );

	/**
	 * The pretty name of the type for error messages
	 *
	 * @access private
	 * @return string
	 */
	public function getMatchingTypeName();

}
