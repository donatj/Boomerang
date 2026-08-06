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
 */
class StructureEx implements TypeExpectationInterface {

	/** @var callable|mixed|TypeExpectationInterface */
	protected $structure;

	/** @var list<int|string> */
	protected $path = [];

	/** @var \Boomerang\Interfaces\ExpectationResultInterface[] */
	protected $expectationResults = [];

	private ValidatorInterface $validator;

	/**
	 * @param callable|mixed|TypeExpectationInterface $structure
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
	 * @param mixed $data
	 * @return bool
	 */
	public function match( $data ) {
		[$pass, $expectations] = $this->__validate($data, $this->structure);
		$this->addExpectationResults($expectations);

		return $pass;
	}

	/**
	 * @param mixed                                                                                 $data
	 * @param array<mixed>|bool|\Closure|float|int|string|StructureEx|TypeExpectationInterface|null $validation
	 * @param list<int|string>|null                                                                 $path
	 * @return array
	 */
	protected function __validate( $data, $validation, ?array $path = null ) {
		if( !$path ) {
			$path = $this->path;
		}

		$pathName = $this->makePathName($path);

		if( is_array($validation) ) {
			if( !is_array($data) ) {
				return [ false, [ new FailingExpectationResult($this->validator, "Unexpected scalar\n { {$pathName} } ", $validation, $data) ] ];
			}

			/** @var \Boomerang\ExpectationResults\AbstractResult[] $expectations */
			$expectations = [];
			$pass         = true;

			reset($validation);
			$firstIsZero = key($validation) === 0;
			foreach( $validation as $key => $value ) {
				if( array_key_exists($key, $data) ) {
					[$passing, $sub_expectations] = $this->__validate($data[$key], $value, array_merge($path, [ $key ]));
					$expectations = array_merge($expectations, $sub_expectations);
					$pass         = $passing && $pass;

					continue;
				}

				$subPathName    = $this->makePathName(array_merge($path, [ $firstIsZero ? $key : (string)$key ]));
				$expectations[] = new FailingExpectationResult($this->validator, "Missing key\n { {$subPathName} } ", $key);
			}

			return [ $pass, $expectations ];
		}

		if( $validation instanceof self ) {
			$validation->setPath($path);
			$validation->setValidator($this->validator);

			return [ $validation->match($data), $validation->getExpectationResults() ];
		}

		if( $validation instanceof TypeExpectationInterface ) {
			$typeName = $this->getScalarTypeName($data);

			if( !$validation->match($data) ) {
				return [ false, [ new FailingExpectationResult($this->validator, "Unexpected structure type check result\n { {$pathName} } ", $validation->getMatchingTypeName(), $typeName) ] ];
			}

			return [ true, [ new PassingExpectationResult($this->validator, "Expected structure type check result\n { {$pathName} } ", $typeName) ] ];
		}

		if( $validation instanceof \Closure ) {
			$reflect    = new \ReflectionFunction($validation);
			$parameters = $reflect->getParameters();
			$parameterType = count($parameters) > 0 ? $parameters[0]->getType() : null;

			if( $parameterType instanceof \ReflectionNamedType && $parameterType->getName() === 'array' && !is_array($data) ) {
				$typeName       = $this->getScalarTypeName($data);

				return [ false, [ new FailingExpectationResult($this->validator, "Unexpected \\Closure parameter type\n { {$pathName} } ", 'array', $typeName) ] ];
			}

			try {
				$result = $validation($data);
			} catch( \Throwable $throwable ) {
				return [ false, [ new FailingExpectationResult(
					$this->validator,
					"\\Closure threw " . get_class($throwable) . "\n { {$pathName} } ",
					'(no exception)',
					$throwable->getMessage()
				) ] ];
			}

			if( $result !== true ) {
				return [ false, [ new FailingExpectationResult($this->validator, "Unexpected \\Closure structure validator result\n { {$pathName} } ", true, $result) ] ];
			}

			return [ true, [ new PassingExpectationResult($this->validator, "Expected \\Closure structure validator result\n { {$pathName} } ", $result) ] ];
		}

		if( is_scalar($validation) ) {
			if( $validation != $data ) {
				return [ false, [ new FailingExpectationResult($this->validator, "Unexpected value\n { {$pathName} } ", $validation, $data) ] ];
			}

			return [ true, [ new PassingExpectationResult($this->validator, "Expected value\n { {$pathName} } ", $validation) ] ];
		}

		return [ true, [] ];
	}

	/**
	 * @param list<int|string> $path
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
	 * @param list<int|string> $path
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
	 * @param array<int, mixed> $expectations
	 */
	protected function addExpectationResults( array $expectations ) {
		foreach( $expectations as $expect ) {
			if( !$expect instanceof ExpectationResultInterface ) {
				throw new \InvalidArgumentException('Expectation Results must implement ExpectationResultInterface');
			}

			$this->expectationResults[spl_object_hash($expect)] = $expect;
		}
	}

	/**
	 * @return string
	 */
	public function getMatchingTypeName() {
		return 'structure';
	}

	/**
	 * @param mixed $data
	 */
	private function getScalarTypeName( $data ) : string {
		$typeName = gettype($data);
		if( is_string($data) ) {
			$typeName .= "{" . strlen($data) . "}";
		}

		return $typeName;
	}

}
