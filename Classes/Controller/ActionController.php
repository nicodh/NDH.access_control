<?php
namespace NDH\AccessControl\Controller;
/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Party".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
 
use TOOOL\Intranet\ChromePhp;

class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \NDH\AccessControl\Security\Authorization\Interceptor\PolicyEnforcementInterceptor
	 *
	 * @inject
	 */
	protected $policyEnforcementInterceptor;

	public function callActionMethod() {
		$controlPoint = new \NDH\AccessControl\Security\ControlPoint($this, get_class($this), $this->actionMethodName, (array)$this->arguments, $this->request);
		try {
			$this->policyEnforcementInterceptor->setControlPoint($controlPoint);
			$this->policyEnforcementInterceptor->invoke();
		} catch (\NDH\AccessControl\Security\Exception\AccessDeniedException $e) {
			$this->actionMethodName = 'accessDeniedAction';
		}
		parent::callActionMethod();
	}

	public function accessDeniedAction() {
		return 'Zugriff nicht erlaubt';
	}

	public function processRequest(\TYPO3\CMS\Extbase\Mvc\RequestInterface $request, \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response) {
		$context = $this->objectManager->get('NDH\\AccessControl\\Security\\Context\\Typo3FrontendContext');
		$context->initialize();
		parent::processRequest($request, $response);
	}

}
