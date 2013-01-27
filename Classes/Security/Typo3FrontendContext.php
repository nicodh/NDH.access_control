<?php
namespace NDH\AccessControl\Security;

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
use TYPO3\CMS\Core\SingletonInterface;

class Typo3FrontendContext implements ContextInterface, SingletonInterface {

	/**
	 * @var \NDH\AccessControl\Security\AccountInterface
	 */
	protected $account;

	/**
	 * @var \NDH\AccessControl\Security\Policy\RoleInterface
	 */
	protected $roles;

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
			return new \Typo3FrontendRole('Anonymous');
		}
	}
}