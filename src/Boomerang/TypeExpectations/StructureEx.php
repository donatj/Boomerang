<?php

/**
 * @todo: Convert this into a Structure Validator or something, this feels goofy
 * @todo: Then make JSON Validator extend this
 */

namespace Boomerang\TypeExpectations;

use Boomerang\ExpectationResults\FailingExpectationResult;
use Boomerang\ExpectationResults\FailingResult;
use Boomerang\ExpectationResults\PassingExpectationResult;
use Boomerang\ExpectationResults\PassingResult;
use Boomerang\Interfaces\TypeExpectation;
use Boomerang\Interfaces\Validator;

class StructureEx implements TypeExpectation, Validator {

	protected $structure;
	protected $path = array();
	private $expectations = array();
	private $response;

	function __construct( $structure ) {
		$this->structure = $structure;
	}

	/**
	 * @return mixed
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * @param mixed $response
	 */
	public function setResponse( $response ) {
		$this->response = $response;
	}

	public function match( $data ) {
		return $this->__validate($data, $this->structure);
	}

	protected function __validate( $data, $validation, array $path = null ) {

		if( !$path ) {
			$path = $this->path;
		}

		$pathName = $this->makePathName($path);

		$pass = true;

		if( is_array($validation) ) {
			foreach( $validation as $key => $value ) {
				if( array_key_exists($key, $data) ) {
					$pass = $this->__validate($data[$key], $value, array_merge($path, array( $key ))) && $pass;
				} else {
					var_export($data);
					$this->expectations[] = new FailingExpectationResult($this, "Missing Key\n { {$pathName} } ", $key);
				}
			}
		} elseif( $validation instanceof StructureEx ) {
			$validation->setPath($path);
			$validation->setResponse($this->response);

			$pass               = $validation->match($data);
			$this->expectations = array_merge($this->expectations, $validation->getExpectationResults());
		} elseif( $validation instanceof TypeExpectation ) {
			if( !$pass = $validation->match($data) ) {
				//@todo we can do better than this
				$this->expectations[] = new FailingExpectationResult($this, "Unexpected structure type check result\n { {$pathName} } ", get_class($validation), gettype($data));
			} else {
				$this->expectations[] = new PassingExpectationResult($this, "Expected structure type check result\n { {$pathName} } ", $validation);
			}

		} elseif( $validation instanceof \Closure ) {
			if( !$pass = $validation($data) ) {
				$this->expectations[] = new FailingResult($this, "Unexpected \Closure structure validator result\n { {$pathName} } ");
			} else {
				$this->expectations[] = new PassingResult($this, "Expected \Closure structure validator result\n { {$pathName} } ");
			}

		} elseif( is_scalar($validation) ) {
			if( !$pass = $validation == $data ) {
				$this->expectations[] = new FailingExpectationResult($this, "Unexpected value\n { {$pathName} } ", $validation, $data);
			} else {
				$this->expectations[] = new PassingExpectationResult($this, "Expected value\n { {$pathName} } ", $validation);
			}

		}

		return $pass;
	}

	private function makePathName( array $path ) {
		$s_path = "root";
		foreach( $path as $loc ) {
			if( is_numeric($loc) ) {
				$s_path .= "[$loc]";
			} else {
				$s_path .= ".$loc";
			}
		}

		return $s_path;
	}

	/**
	 * @param array $path
	 * @return mixed
	 */
	public function setPath( array $path ) {
		$this->path = $path;
	}

	/**
	 * @return array
	 */
	public function getExpectationResults() {
		return $this->expectations;
	}


}