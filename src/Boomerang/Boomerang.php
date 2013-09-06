<?php

namespace Boomerang;

use Boomerang\Interfaces\Validator;
use Boomerang\Runner\TestRunner;
use Boomerang\Runner\UserInterface;
use GetOptionKit\GetOptionKit;

class Boomerang {

	const VERSION = ".0.1a";
	public static $boomerangPath;
	public static $pathInfo;
	public static $validators = array();

	static function main( $args ) {
		self::$boomerangPath = realpath($args[0]);
		self::$pathInfo      = pathinfo(self::$boomerangPath);

		$opt = new GetOptionKit();
		$opt->add('v|verbose', 'verbose message');
//		$opt->add('c|color:', 'enable/disable color output.');


		try {
			$cliOptions = $opt->parse($args);
		} catch(\Exception $e) {
			echo $e->getMessage();
			die(1);
		}


		$ui = new UserInterface(STDOUT, STDERR);


		if( count($args) < 2 ) {
			$ui->dumpOptions($opt->specs->outputOptions());
			die(1);
		}

		$ui->outputMsg("Boomerang! " . self::VERSION . " by Jesse G. Donat" . PHP_EOL);

		$runner = new TestRunner(end($cliOptions->getArguments()), $ui);

		$runner->runTests(function ( $file ) use ( $ui, $cliOptions ) {
			$validators = array();
			foreach( Boomerang::$validators as &$v_data ) {
				if( !$v_data['displayed'] ) {
					$v_data['displayed'] = true;
					$validators[]        = $v_data['validator'];
				}
			}

			$ui->updateExpectationDisplay($file, $validators, $cliOptions->has('verbose'));
		});

		$tests = 0;
		$total = 0;
		$fails = 0;
		foreach( Boomerang::$validators as $v_data ) {
			$tests++;
			$ex_res = $v_data['validator']->getExpectationResults();
			foreach( $ex_res as $ex ) {
				$total++;
				$fails += $ex->getFail();
			}
		}

		$ui->outputMsg(PHP_EOL);
		$ui->outputMsg("$tests tests, $total assertions, $fails failures");

		die($fails ? 2 : 0);
	}

	/**
	 * @param Validator $validator
	 */
	public static function addValidator( Validator $validator ) {
		self::$validators[] = array(
			'displayed' => false,
			'validator' => $validator,
		);
	}

}