<?php

namespace Boomerang\TypeExpectations;

use Boomerang\ExpectationResults\FailingExpectationResult;
use Boomerang\ExpectationResults\PassingExpectationResult;
use Boomerang\Interfaces\ExpectationResultInterface;
use Boomerang\Interfaces\TypeExpectationInterface;
use Boomerang\Interfaces\ValidatorInterface;

/**
 * Structure Expectation
 *
 * Used to define rules about structure.
 *
 * @package Boomerang\TypeExpectations
 */
class StructureEx implements TypeExpectationInterface {

	protected $structure;
	protected $path = array();

	/**
	 * @var \Boomerang\Interfaces\ExpectationResultInterface[]
	 */
	protected $expectationResults = array();

	/**
	 * @var ValidatorInterface
	 */
	private $validator;

	/**
	 * @param TypeExpectationInterface|callable|mixed $structure
	 */
	public function __construct( $structure ) {
		$this->structure = $structure;
	}

	/**
	 * @return ValidatorInterface
	 */
	public function getValidator() {
		return $this->validator;
	}

	/**
	 * @access private
	 * @param ValidatorInterface $validator
	 */
	public function setValidator( $validator ) {
		$this->validator = $validator;
	}

	/**
	 * @access private
	 *
	 * @param array|int|float|string $data
	 * @return bool
	 */
	public function match( $data ) {
		list($pass, $expectations) = $this->__validate($data, $this->structure);
		$this->addExpectationResults($expectations);

		return $pass;
	}

	/**
	 * @param array|int|float|string                                               $data
	 * @param array|int|float|string|StructureEx|TypeExpectationInterface|\Closure $validation
	 * @param array                                                                $path
	 * @return array
	 */
	protected function __validate( $data, $validation, array $path = null ) {
		/**
		 * @var $expectations \Boomerang\ExpectationResults\AbstractResult[]
		 */
		$expectations = array();

		if( !$path ) {
			$path = $this->path;
		}

		$pathName = $this->makePathName($path);

		$pass = true;

		if( is_array($validation) ) {
			if( is_array($data) ) {
				reset($validation);
				$firstIsZero = key($validation) === 0;
				foreach( $validation as $key => $value ) {
					if( array_key_exists($key, $data) ) {
						list($passing, $sub_expectations) = $this->__validate($data[$key], $value, array_merge($path, array( $key )));
						$expectations = array_merge($expectations, $sub_expectations);
						$pass         = $passing && $pass;
					} else {
						$subPathName    = $this->makePathName(array_merge($path, [ $firstIsZero ? $key : (string)$key ]));
						$expectations[] = new FailingExpectationResult($this->validator, "Missing key\n { {$subPathName} } ", $key);
					}
				}
			} else {
				$expectations[] = new FailingExpectationResult($this->validator, "Unexpected scalar\n { {$pathName} } ", $validation, $data);
			}
		} elseif( $validation instanceof StructureEx ) {
			$validation->setPath($path);
			$validation->setValidator($this->validator);

			$pass         = $validation->match($data);
			$expectations = array_merge($expectations, $validation->getExpectationResults());
		} elseif( $validation instanceof TypeExpectationInterface ) {
			$typeName = $this->getScalarTypeName($data);

			if( !$pass = $validation->match($data) ) {
				$expectations[] = new FailingExpectationResult($this->validator, "Unexpected structure type check result\n { {$pathName} } ", $validation->getMatchingTypeName(), $typeName);
			} else {
				$expectations[] = new PassingExpectationResult($this->validator, "Expected structure type check result\n { {$pathName} } ", $typeName);
			}
		} elseif( $validation instanceof \Closure ) {
			$reflect    = new \ReflectionFunction($validation);
			$parameters = $reflect->getParameters();

			// todo: Replace shutup operator with a better way to check if the parameter is an array when PHP 7+ is required
			if( count($parameters) > 0 && (@$parameters[0]->isArray()) && !is_array($data) ) {
				$pass = false;

				$typeName       = $this->getScalarTypeName($data);
				$expectations[] = new FailingExpectationResult($this->validator, "Unexpected \\Closure parameter type\n { {$pathName} } ", 'array', $typeName);
			} else {
				$result = $validation($data);
				$pass   = $result === true;

				if( !$pass ) {
					$expectations[] = new FailingExpectationResult($this->validator, "Unexpected \\Closure structure validator result\n { {$pathName} } ", true, $result);
				} else {
					$expectations[] = new PassingExpectationResult($this->validator, "Expected \\Closure structure validator result\n { {$pathName} } ", $result);
				}
			}
		} elseif( is_scalar($validation) ) {
			if( !$pass = $validation == $data ) {
				$expectations[] = new FailingExpectationResult($this->validator, "Unexpected value\n { {$pathName} } ", $validation, $data);
			} else {
				$expectations[] = new PassingExpectationResult($this->validator, "Expected value\n { {$pathName} } ", $validation);
			}
		}

		return array( $pass, $expectations );
	}

	/**
	 * @param array $path
	 * @return string
	 */
	protected function makePathName( array $path ) {
		$s_path = "";
		foreach( $path as $loc ) {
			if( is_numeric($loc) ) {
				if( is_int($loc) ) {
					if( $s_path == "" ) {
						$s_path = ".";
					}
					$s_path .= "[$loc]";
				} else {
					$s_path .= '."' . $loc . '"';
				}
			} else {
				$s_path .= ".$loc";
			}
		}

		return $s_path;
	}

	/**
	 * @access private
	 * @param array $path
	 */
	public function setPath( array $path ) {
		$this->path = $path;
	}

	/**
	 * @access private
	 * @return \Boomerang\Interfaces\ExpectationResultInterface[]
	 */
	public function getExpectationResults() {
		return $this->expectationResults;
	}

	/**
	 * @param \Boomerang\Interfaces\ExpectationResultInterface[] $expectations
	 */
	protected function addExpectationResults( array $expectations ) {
		foreach( $expectations as $expect ) {
			if( $expect instanceof ExpectationResultInterface ) {
				// @todo ideally I shouldn't need to do this
				$this->expectationResults[spl_object_hash($expect)] = $expect;
			} else {
				throw new \InvalidArgumentException('Expectation Results must implement ExpectationResultInterface');
			}
		}
	}

	public function getMatchingTypeName() {
		return 'structure';
	}

	/**
	 * @param float|int|string|array $data
	 * @return string
	 */
	private function getScalarTypeName( $data ) {
		$typeName = gettype($data);
		if( $typeName == 'string' ) {
			$typeName .= "{" . strlen($data) . "}";
		}

		return $typeName;
	}

}
