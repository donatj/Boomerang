<?php

namespace Boomerang;

use Boomerang\Interfaces\ExpectationResultInterface;
use Boomerang\Interfaces\ResponseInterface;
use Boomerang\Interfaces\ValidatorInterface;
use Boomerang\TypeExpectations\StructureEx;

class StructureValidator implements ValidatorInterface {

	/**
	 * @var ResponseInterface
	 */
	protected $response;

	/**
	 * @var ExpectationResultInterface[]
	 */
	protected $expectations = array();

	public function __construct( ResponseInterface $response ) {
		$this->response = $response;
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

		$sx->match($this->response->getBody());
		$this->expectations = array_merge($this->expectations, $sx->getExpectations());

		return $this;
	}

	/**
	 * @return ResponseInterface
	 */
	public function getResponse() {
		return $this->response;
	}

}