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
use NDH\AccessControl\Security\Policy\PolicyService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Role extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {


	/**
	 * A unique identifier without spaces
	 *
	 * @var \string
	 */
	protected $identifier;

	/**
	 * description
	 *
	 * @var \string
	 */
	protected $description;

	/**
	 * serializedPrivileges
	 *
	 * @var \string
	 */
	protected $serializedPrivileges;

	/**
	 * parentRole
	 *
	 * @var \NDH\AccessControl\Domain\Model\Role
	 */
	protected $parentRole = NULL;

	/**
	 * Returns the identifier
	 *
	 * @return \string $identifier
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Sets the identifier
	 *
	 * @param \string $identifier
	 * @return void
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}

	/**
	 * Returns the description
	 *
	 * @return \string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param \string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Returns the privileges
	 *
	 * @return \array $privileges
	 */
	public function getPrivileges() {
		$privileges = array();
		if(is_string($this->serializedPrivileges)) {
			$decodedPrivileges = json_decode($this->serializedPrivileges, TRUE);
			if(isset($decodedPrivileges['methods'])) {
				$privileges['methods'] = array();
				foreach($decodedPrivileges['methods'] as $pluginKey => $classMethods) {
					$privileges = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule($privileges, array('methods' => $classMethods));
				}
			}
		}
		if($this->hasParentRole()) {
			$privileges = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule($privileges,$this->parentRole->getPrivileges());
		}
		return $privileges;
	}

	/**
	 * hasParentRole
	 *
	 * @return
	 */
	public function hasParentRole() {
		if($this->parentRole != NULL) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Returns the parentRole
	 *
	 * @return \NDH\AccessControl\Domain\Model\Role $parentRole
	 */
	public function getParentRole() {
		return $this->parentRole;
	}

	/**
	 * Sets the parentRole
	 *
	 * @param \NDH\AccessControl\Domain\Model\Role $parentRole
	 * @return void
	 */
	public function setParentRole(\NDH\AccessControl\Domain\Model\Role $parentRole) {
		$this->parentRole = $parentRole;
	}

	/**
	 * Sets the serializedPrivileges
	 *
	 * @param \string $serializedPrivileges
	 * @return
	 */
	public function setSerializedPrivileges($serializedPrivileges) {
		$this->serializedPrivileges = $serializedPrivileges;
	}

}
?>