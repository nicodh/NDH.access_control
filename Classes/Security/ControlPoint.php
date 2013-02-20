<?php
namespace NDH\AccessControl\Security;
/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */


/**
 * In Flow the join point object contains context information when a point cut
 * matches and the registered advices are executed.
 *
 * @api
 */
class ControlPoint implements \NDH\AccessControl\Security\ControlPointInterface {

	/**
	 * A reference to the target class object
	 * @var object
	 */
	protected $targetClass;

	/**
	 * @param object $targetClass
	 */
	public function setTargetClass($targetClass) {
		$this->targetClass = $targetClass;
	}

	/**
	 * @return object
	 */
	public function getTargetClass() {
		return $this->targetClass;
	}

	/**
	 * Class name of the target class this join point refers to
	 * @var string
	 */
	protected $className;

	/**
	 * Method name of the target method which is about to or has been invoked
	 * @var string
	 */
	protected $methodName;

	/**
	 * Array of method arguments which have been passed to the target method
	 * @var array
	 */
	protected $methodArguments;


	/**
	 * The request of the method invocations
	 * @var \TYPO3\CMS\Extbase\Mvc\Web\Request
	 */
	protected $request = NULL;

	/**
	 * The exception thrown (only used for After Throwing advices)
	 * @var \Exception
	 */
	protected $exception = NULL;

	/**
	 * Constructor, creates the join point
	 *
	 * @param object $targetClass Reference to the target class
	 * @param string $className Class name of the target class this control point refers to
	 * @param string $methodName Method name of the target method which is about to or has been invoked
	 * @param array $methodArguments Array of method arguments which have been passed to the target method
	 * @param \TYPO3\CMS\Extbase\Mvc\Web\Request $request The request
	 */
	public function __construct($targetClass, $className, $methodName, array $methodArguments, \TYPO3\CMS\Extbase\Mvc\Web\Request $request) {
		$this->targetClass = $targetClass;
		$this->className = $className;
		$this->methodName = $methodName;
		$this->methodArguments = $methodArguments;
		$this->request = $request;
	}

	/**
	 * Returns the class name of the target class this join point refers to
	 *
	 * @return string The class name
	 * @api
	 */
	public function getClassName() {
		return $this->className;
	}

	/**
	 * Returns the method name of the method this join point refers to
	 *
	 * @return string The method name
	 * @api
	 */
	public function getMethodName() {
		return $this->methodName;
	}

	/**
	 * Returns an array of arguments which have been passed to the target method
	 *
	 * @return array Array of arguments
	 * @api
	 */
	public function getMethodArguments() {
		return $this->methodArguments;
	}

	/**
	 * Returns the value of the specified method argument
	 *
	 * @param  string $argumentName Name of the argument
	 * @return mixed Value of the argument
	 * @throws Exception\InvalidArgumentException
	 * @api
	 */
	public function getMethodArgument($argumentName) {
		if (!array_key_exists($argumentName, $this->methodArguments)) {
			throw new \NDH\AccessControl\Security\Exception\InvalidArgumentException('The argument "' . $argumentName . '" does not exist in method ' . $this->className . '->' . $this->methodName, 1172750905);
		}
		return $this->methodArguments[$argumentName];
	}

	/**
	 * Sets the value of the specified method argument
	 *
	 * @param string $argumentName Name of the argument
	 * @param mixed $argumentValue Value of the argument
	 * @return void
	 * @throws \NDH\AccessControl\Security\Exception\InvalidArgumentException
	 * @api
	 */
	public function setMethodArgument($argumentName, $argumentValue) {
		if (!array_key_exists($argumentName, $this->methodArguments)) {
			throw new \NDH\AccessControl\Security\Exception\InvalidArgumentException('The argument "' . $argumentName . '" does not exist in method ' . $this->className . '->' . $this->methodName, 1309260269);
		}
		$this->methodArguments[$argumentName] = $argumentValue;
	}

	/**
	 * Returns TRUE if the argument with the specified name exists in the
	 * method call this ControlPoint refers to.
	 *
	 * @param  string $argumentName Name of the argument to check
	 * @return boolean TRUE if the argument exists
	 * @api
	 */
	public function isMethodArgument($argumentName) {
		return isset($this->methodArguments[$argumentName]);
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Mvc\Web\Request $request
	 */
	public function setRequest($request) {
		$this->request = $request;
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Mvc\Web\Request
	 */
	public function getRequest() {
		return $this->request;
	}

}

?>