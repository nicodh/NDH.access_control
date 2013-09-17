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
	 * @var \NDH\AccessControl\Security\ContextInterface;
	 */
	protected $securityContext;

	/**
	 * @var \NDH\AccessControl\Security\Authorization\Interceptor\PolicyEnforcementInterceptor
	 *
	 * @inject
	 */
	protected $policyEnforcementInterceptor;

	/**
	 * @var string
	 */
	protected $deniedActionMethodName;

	public function callActionMethod() {
		$controlPoint = new \NDH\AccessControl\Security\ControlPoint($this, get_class($this), $this->actionMethodName, (array)$this->arguments, $this->request);
		try {
			$this->policyEnforcementInterceptor->setControlPoint($controlPoint);
			$this->policyEnforcementInterceptor->invoke();
		} catch (\NDH\AccessControl\Security\Exception\AccessDeniedException $e) {
			$this->deniedActionMethodName = $this->actionMethodName;
			$this->actionMethodName = 'accessDeniedAction';
		}
		parent::callActionMethod();
	}

	public function accessDeniedAction	() {
		if(strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== FALSE) {
			header($_SERVER['SERVER_PROTOCOL'] . ' Internal Server Error', true, 500);
			header('content-type:  application/json');
			$responseJSON = json_encode(
				array(
					'messages' => array(
						array(
							'text' => 'Keine Rechte für die Aktion: ' . $this->deniedActionMethodName . '!',
							'type' => 'error'
						)
					) //
				)
			);
			die($responseJSON);
		} else {
			return 'Keine Rechte für die Aktion: ' . $this->deniedActionMethodName . '!';
		}
	}

	public function processRequest(\TYPO3\CMS\Extbase\Mvc\RequestInterface $request, \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response) {
		$this->processRequestStart = microtime(TRUE);
		$this->securityContext = $this->objectManager->get('NDH\\AccessControl\\Security\\Context\\Typo3FrontendContext');
		$this->securityContext->initialize();
		parent::processRequest($request, $response);
		ChromePhp::log('Request processed:',round(microtime(TRUE) - $this->processRequestStart,4) . ' sec');
	}

	/**
	 * @param \NDH\AccessControl\Security\ContextInterface $securityContext
	 */
	public function setSecurityContext($securityContext) {
		$this->securityContext = $securityContext;
	}

	/**
	 * @return \NDH\AccessControl\Security\ContextInterface
	 */
	public function getSecurityContext() {
		return $this->securityContext;
	}





}
