<?php

namespace Boomerang\Interfaces;

interface TypeExpectation {

	/**
	 * @param $data
	 * @return bool
	 */
	public function match( $data );

	public function getMatchingTypeName();

}