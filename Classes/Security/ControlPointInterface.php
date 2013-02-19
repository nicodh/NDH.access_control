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
 * Contract for a join point
 *
 */
interface ControlPointInterface {

	/**
	 * Returns the reference to the proxy class instance
	 *
	 * @return object
	 */
	public function getTargetClass();

	/**
	 * Returns the class name of the target class this join point refers to
	 *
	 * @return string The class name
	 */
	public function getClassName();

	/**
	 * Returns the method name of the method this join point refers to
	 *
	 * @return string The method name
	 */
	public function getMethodName();

	/**
	 * Returns an array of arguments which have been passed to the target method
	 *
	 * @return array Array of arguments
	 */
	public function getMethodArguments();

	/**
	 * Returns the value of the specified method argument
	 *
	 * @param  string $argumentName Name of the argument
	 * @return mixed Value of the argument
	 */
	public function getMethodArgument($argumentName);

	/**
	 * Returns TRUE if the argument with the specified name exists in the
	 * method call this joinpoint refers to.
	 *
	 * @param string $argumentName Name of the argument to check
	 * @return boolean TRUE if the argument exists
	 */
	public function isMethodArgument($argumentName);

	/**
	 * Sets the value of the specified method argument
	 *
	 * @param string $argumentName Name of the argument
	 * @param mixed $argumentValue Value of the argument
	 * @return void
	 */
	public function setMethodArgument($argumentName, $argumentValue);


}

?>