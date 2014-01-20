<?php

namespace Boomerang;

use Boomerang\Interfaces\ExpectationResultInterface;
use Boomerang\Interfaces\ResponseInterface;
use Boomerang\Interfaces\TypeExpectationInterface;
use Boomerang\Interfaces\ValidatorInterface;
use Boomerang\TypeExpectations\StructureEx;

class StructureValidator implements ValidatorInterface {

	/**
	 * @var ResponseInterface
	 */
	protected $response;
	protected $data;
	/**
	 * @var ExpectationResultInterface[]
	 */
	protected $expectations = array();

	/**
	 * @param ResponseInterface $response
	 */
	public function __construct( ResponseInterface $response ) {
		$this->response = $response;
		$this->data     = $this->response->getBody();
	}

	/**
	 * @return ExpectationResultInterface[]
	 */
	public function getExpectationResults() {
		return $this->expectations;
	}

	/**
	 * Verify that the data matches the passed expected structure definition.
	 *
	 * @param TypeExpectationInterface|callable|mixed $structure A description of the expected structure.
	 * @return $this
	 */
	public function expectStructure( $structure ) {

		$sx = new StructureEx($structure);
		$sx->setValidator($this);

		$sx->match($this->data);
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