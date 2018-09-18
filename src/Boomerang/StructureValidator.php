<?php

namespace Boomerang;

use Boomerang\Interfaces\ResponseInterface;
use Boomerang\Interfaces\TypeExpectationInterface;
use Boomerang\TypeExpectations\StructureEx;

class StructureValidator extends AbstractValidator {

	/**
	 * @var ResponseInterface
	 */
	protected $response;
	protected $data;

	public function __construct( ResponseInterface $response ) {
		$this->response = $response;
		$this->data     = $this->response->getBody();
	}

	/**
	 * Verify that the data matches the passed expected structure definition.
	 *
	 * @param callable|mixed|TypeExpectationInterface $structure A description of the expected structure.
	 * @return $this
	 */
	public function expectStructure( $structure ) {

		$sx = new StructureEx($structure);
		$sx->setValidator($this);

		$sx->match($this->data);
		$this->expectations = array_merge($this->expectations, $sx->getExpectationResults());

		return $this;
	}

	/**
	 * @return ResponseInterface
	 */
	public function getResponse() {
		return $this->response;
	}

}
