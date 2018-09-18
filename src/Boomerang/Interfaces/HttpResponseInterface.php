<?php

namespace Boomerang\Interfaces;

interface HttpResponseInterface extends ResponseInterface {

	public function getHeader( $header, $hop = null );

	public function getHeaders( $hop = null );

	public function getAllHeaders();

	public function getRawHeaders();

	public function getRequest();

	public function getHopCount();

	public function getStatus( $hop = null );

}
