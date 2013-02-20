<?php
namespace NDH\AccessControl\Security\Context;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Nico de Haen <mail@ndh-websolutions.de>, ndh websolutions
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class Typo3FrontendContext implements \NDH\AccessControl\Security\ContextInterface, \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var \NDH\AccessControl\Security\AccountInterface
	 */
	protected $account = NULL;

	/**
	 * @var \NDH\AccessControl\Security\Policy\RoleInterface
	 */
	protected $roles;

	/**
	 * frontendUserRepository
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
	 * @inject
	 */
	protected $frontendUserRepository;

	/**
	 * __construct
	 *
	 */
	public function __construct() {
		$this->initStorageObjects();
	}

	/**
	 * Initializes all ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->roles = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	public function initialize() {
		if($GLOBALS['TSFE']->loginUser) {
			$this->account = $this->frontendUserRepository->findByIdentifier($GLOBALS["TSFE"]->fe_user->user['uid']);
		}
	}

	/**
	 * @param \NDH\AccessControl\Security\AccountInterface $account
	 */
	public function setAccount($account) {
		$this->account = $account;
	}

	/**
	 * @return \NDH\AccessControl\Security\AccountInterface
	 */
	public function getAccount() {
		return $this->account;
	}



	public function getRoles() {
		if(!$GLOBALS['TSFE']->loginUser) {
			$role = new \NDH\AccessControl\Domain\Model\Role();
			$role->setIdentifier('Everybody');
			return array($role);
		} else {
			return $this->account->getRoles();
		}
	}
}