<?php

namespace Boomerang\Interfaces;

interface TypeExpectation {

	/**
	 * Method to pass data to compare against
	 *
	 * @param $data
	 * @return bool
	 */
	public function match( $data );

	/**
	 * The pretty name of the type for error messages
	 *
	 * @return string
	 */
	public function getMatchingTypeName();

}