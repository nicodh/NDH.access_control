<?php
namespace NDH\AccessControl\Security\Authorization\Interceptor;

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
 * This is the main security interceptor, which enforces the current security policy and is usually called by the central security aspect:
 *
 * 1. If authentication has not been performed (flag is set in the security context) the configured authentication manager is called to authenticate its tokens
 * 2. If a AuthenticationRequired exception has been thrown we look for an authentication entry point in the active tokens to redirect to authentication
 * 3. Then the configured AccessDecisionManager is called to authorize the request/action
 *
 *
 */
class PolicyEnforcementInterceptor implements \NDH\AccessControl\Security\Authorization\InterceptorInterface, \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * The authentication manager
	 * @var \NDH\AccessControl\Security\Authentication\AuthenticationManagerInterface
	 */
	protected $authenticationManager;

	/**
	 * The access decision manager
	 * @var \NDH\AccessControl\Security\Authorization\AccessDecisionManagerInterface
	 */
	protected $accessDecisionManager;

	/**
	 * The current control point
	 * @var \NDH\AccessControl\Security\ControlPointInterface
	 */
	protected $controlPoint;

	/**
	 * Constructor.
	 *
	 * @param \NDH\AccessControl\Security\Authentication\AuthenticationManagerInterface $authenticationManager The authentication manager
	 * @param \NDH\AccessControl\Security\Authorization\AccessDecisionManagerInterface $accessDecisionManager The access decision manager
	 */
	public function __construct(\NDH\AccessControl\Security\Authentication\AuthenticationManagerInterface $authenticationManager, \NDH\AccessControl\Security\Authorization\AccessDecisionManagerInterface $accessDecisionManager) {
		$this->authenticationManager = $authenticationManager;
		$this->accessDecisionManager = $accessDecisionManager;
	}

	/**
	 * Sets the current control point for this interception
	 *
	 * @param \NDH\AccessControl\Security\ControlPointInterface $controlPoint The current controlpoint
	 * @return void
	 */
	public function setControlPoint(\NDH\AccessControl\Security\ControlPointInterface $controlPoint) {
		$this->controlPoint = $controlPoint;
	}

	/**
	 * Invokes the security interception
	 *
	 * @return boolean TRUE if the security checks was passed
	 * @throws \NDH\AccessControl\Security\Exception\AccessDeniedException
	 */
	public function invoke() {
		$this->authenticationManager->authenticate();
		$this->accessDecisionManager->decideOnControlPoint($this->controlPoint);
	}
}

?>