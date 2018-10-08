<?php
namespace App\Annotation;


/**
 * @Annotation
 * @Target("CLASS")
 */
class Link {
	/**
	 * @Required
	 */
	public $name;
	/**
	 * @Required
	 */
	public $route;

	public $params = [];
}