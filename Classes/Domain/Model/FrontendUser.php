<?php
namespace NDH\AccessControl\Domain\Model;

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

/**
 *
 *
 * @package access_control
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class FrontendUser extends \TYPO3\CMS\Extbase\Domain\Model\FrontendUser  implements \NDH\AccessControl\Security\AccountInterface{

	/**
	 * roles
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\NDH\AccessControl\Domain\Model\Role>
	 */
	protected $roles;

	/**
	 * party
	 *
	 * @var \NDH\AccessControl\Domain\Model\Party
	 */
	protected $party;

	/**
	 * __construct
	 *
	 * @return FrontendUser
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
		parent::__construct();
	}

	/**
	 * Initializes all ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		
		/**
		 * Do not modify this method!
		 * It will be rewritten on each save in the extension builder
		 * You may modify the constructor of this class instead
		 */
		$this->roles = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * getAccountId
	 *
	 * @return
	 */
	public function getAccountId() {
		return $this->uid;
	}

	/**
	 * getMainRole
	 *
	 * @return \NDH\AccessControl\Domain\Model\Role|NULL
	 */
	public function getMainRole() {
		if($this->roles->count() > 0) {
			$this->roles->rewind();
			return $this->roles->current();
		} else {
			throw new \Excpetion('No ROLE!');
		}
		return NULL;
	}

	/**
	 * hasRole
	 *
	 * @param string $roleIdentifier
	 * @return bool
	 */
	public function hasRole($roleIdentifier) {
		foreach ($this->roles as $role) {
			if($role->getIdentifier() == $roleIdentifier) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * hasOrExtendsRole
	 *
	 * @param  $roleIdentifier
	 * @return
	 */
	public function hasOrExtendsRole($roleIdentifier) {
		foreach ($this->roles as $role) {
			if($role->hasOrExtendsRole($roleIdentifier)) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * isAdmin
	 *
	 * @return bool
	 */
	public function isAdmin() {
		return $this->hasRole('Administrator');
	}

	/**
	 * Adds a Role
	 *
	 * @param \NDH\AccessControl\Domain\Model\Role $role
	 * @return void
	 */
	public function addRole(\NDH\AccessControl\Domain\Model\Role $role) {
		$this->roles->attach($role);
	}

	/**
	 * Removes a Role
	 *
	 * @param \NDH\AccessControl\Domain\Model\Role $roleToRemove The Role to be removed
	 * @return void
	 */
	public function removeRole(\NDH\AccessControl\Domain\Model\Role $roleToRemove) {
		$this->roles->detach($roleToRemove);
	}

	/**
	 * Returns the roles
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\NDH\AccessControl\Domain\Model\Role> $roles
	 */
	public function getRoles() {
		return $this->roles;
	}

	/**
	 * Sets the roles
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\NDH\AccessControl\Domain\Model\Role> $roles
	 * @return void
	 */
	public function setRoles(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $roles) {
		$this->roles = $roles;
	}

	/**
	 * Returns the party
	 *
	 * @return \NDH\AccessControl\Domain\Model\Party $party
	 */
	public function getParty() {
		return $this->party;
	}

	/**
	 * Sets the party
	 *
	 * @param \NDH\AccessControl\Domain\Model\Party $party
	 * @return void
	 */
	public function setParty(\NDH\AccessControl\Domain\Model\Party $party) {
		$this->party = $party;
	}

}
?>