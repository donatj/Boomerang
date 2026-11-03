<?php

\Boomerang\Boomerang::addValidator(new class implements \Boomerang\Interfaces\ValidatorInterface {

	public function getExpectationResults() {
		return [];
	}

	public function getResponse() {
		return null;
	}

});
