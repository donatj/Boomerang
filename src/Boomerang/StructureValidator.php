<?php

namespace Boomerang;

use Boomerang\Interfaces\ExpectationResultInterface;
use Boomerang\Interfaces\ValidatorInterface;
use Boomerang\TypeExpectations\StructureEx;

class StructureValidator implements ValidatorInterface {

	protected $data;
	/**
	 * @var ExpectationResultInterface[]
	 */
	protected $expectations = array();

	public function __construct( $data ) {
		$this->data = $data;
	}

	/**
	 * @return ExpectationResultInterface[]
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