<?php

namespace Boomerang\TypeExpectations;

use Boomerang\Exceptions\InvalidStateException;
use Boomerang\ExpectationResults\FailingExpectationResult;
use Boomerang\ExpectationResults\PassingExpectationResult;
use Boomerang\Interfaces\ExpectationResultInterface;
use Boomerang\Interfaces\TypeExpectationInterface;
use Boomerang\Interfaces\ValidatorInterface;

/**
 * Structure Expectation
 *
 * Used to define rules about structure.
 */
class StructureEx implements TypeExpectationInterface {

	protected $structure;
	protected array $path = [];

	/** @var \Boomerang\Interfaces\ExpectationResultInterface[] */
	protected array $expectationResults = [];

	private ValidatorInterface $validator;

	/**
	 * @param callable|mixed|TypeExpectationInterface $structure
	 */
	public function __construct( $structure ) {
		$this->structure = $structure;
	}

	public function getValidator() : ValidatorInterface {
		if( !isset($this->validator) ) {
			throw new InvalidStateException("Validator not set");
		}

		return $this->validator;
	}

	/**
	 * @access private
	 */
	public function setValidator( ValidatorInterface $validator ) : void {
		$this->validator = $validator;
	}

	/**
	 * @access private
	 *
	 * @param array|float|int|string $data
	 */
	public function match( $data ) : bool {
		[ $pass, $expectations ] = $this->validate($data, $this->structure);
		$this->addExpectationResults($expectations);

		return $pass;
	}

	/**
	 * @param array|float|int|string                                               $data
	 * @param array|\Closure|float|int|string|StructureEx|TypeExpectationInterface $validation
	 * @throws \ReflectionException
	 */
	protected function validate( $data, $validation, ?array $path = null ) : array {
		/**
		 * @var \Boomerang\ExpectationResults\AbstractResult[] $expectations
		 */
		$expectations = [];

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
						[ $passing, $sub_expectations ] = $this->validate($data[$key], $value, array_merge($path, [ $key ]));
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

			$expectsArray = false;
			if( count($parameters) > 0 ) {
				$firstParameter     = $parameters[0];
				$firstParameterType = $firstParameter->getType();
				// @todo proper DNF check (union types, etc)
				if( $firstParameterType instanceof \ReflectionNamedType && $firstParameterType->getName() === 'array' ) {
					$expectsArray = true;
				}
			}

			if( $expectsArray && !is_array($data) ) {
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

		return [ $pass, $expectations ];
	}

	/**
	 * @param array<int, scalar> $path
	 */
	protected function makePathName( array $path ) : string {
		$s_path = '';
		foreach( $path as $loc ) {
			if( is_int($loc) ) {
				if( $s_path === '' ) {
					$s_path = '.';
				}

				$s_path .= "[$loc]";
			} elseif (preg_match('/^[a-z_][a-z\d_]*$/i', $loc, $regs)) {
				$s_path .= ".$loc";
			} else {
				$s_path .= '."' . $loc . '"';
			}
		}

		return $s_path;
	}

	/**
	 * @access private
	 */
	public function setPath( array $path ) : void {
		$this->path = $path;
	}

	/**
	 * @access private
	 * @return \Boomerang\Interfaces\ExpectationResultInterface[]
	 */
	public function getExpectationResults() : array {
		return $this->expectationResults;
	}

	/**
	 * @param \Boomerang\Interfaces\ExpectationResultInterface[] $expectations
	 */
	protected function addExpectationResults( array $expectations ) : void {
		foreach( $expectations as $expect ) {
			if( $expect instanceof ExpectationResultInterface ) {
				// @todo ideally I shouldn't need to do this
				$this->expectationResults[spl_object_hash($expect)] = $expect;
			} else {
				throw new \InvalidArgumentException('Expectation Results must implement ExpectationResultInterface');
			}
		}
	}

	public function getMatchingTypeName() : string {
		return 'structure';
	}

	/**
	 * @param array|float|int|string $data
	 */
	private function getScalarTypeName( $data ) : string {
		$typeName = gettype($data);
		if( $typeName === 'string' ) {
			$typeName .= "{" . strlen($data) . "}";
		}

		return $typeName;
	}

}
