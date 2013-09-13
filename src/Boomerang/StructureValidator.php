<?php

namespace Boomerang;

use Boomerang\Interfaces\ExpectationResult;
use Boomerang\Interfaces\Validator;
use Boomerang\TypeExpectations\StructureEx;

class StructureValidator implements Validator {

	protected $data;
	/**
	 * @var ExpectationResult[]
	 */
	protected $expectations = array();

	public function __construct( $data ) {
		$this->data = $data;
	}

	/**
	 * @return ExpectationResult[]
	 */
	public function getExpectationResults() {
		return $this->expectations;
	}

	public function expectStructure( $structure ) {

		$sx = new StructureEx($structure);
		$sx->setValidator($this);

		$sx->match($this->data);
		$this->expectations = array_merge($this->expectations, $sx->getExpectations());

		return $this;
	}

}