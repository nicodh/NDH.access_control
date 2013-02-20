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
class FrontendUserGroup extends \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup {

	/**
	 * role
	 *
	 * @var \NDH\AccessControl\Domain\Model\Role
	 */
	protected $role;

	/**
	 * __construct
	 *
	 * @return FrontendUserGroup
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		// empty
	}

	/**
	 * Returns the role
	 *
	 * @return \NDH\AccessControl\Domain\Model\Role $role
	 */
	public function getRole() {
		return $this->role;
	}

	/**
	 * Sets the role
	 *
	 * @param \NDH\AccessControl\Domain\Model\Role $role
	 * @return void
	 */
	public function setRole(\NDH\AccessControl\Domain\Model\Role $role) {
		$this->role = $role;
	}

}
?>