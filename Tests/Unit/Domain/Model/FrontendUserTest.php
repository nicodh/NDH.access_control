<?php

namespace NDH\AccessControl\Tests;
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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \NDH\AccessControl\Domain\Model\FrontendUser.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Access Control
 *
 * @author Nico de Haen <mail@ndh-websolutions.de>
 */
class FrontendUserTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {
	/**
	 * @var \NDH\AccessControl\Domain\Model\FrontendUser
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new \NDH\AccessControl\Domain\Model\FrontendUser();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getRolesReturnsInitialValueForRole() { 
		$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\Generic\ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->fixture->getRoles()
		);
	}

	/**
	 * @test
	 */
	public function setRolesForObjectStorageContainingRoleSetsRoles() { 
		$role = new \NDH\AccessControl\Domain\Model\Role();
		$objectStorageHoldingExactlyOneRoles = new \TYPO3\CMS\Extbase\Persistence\Generic\ObjectStorage();
		$objectStorageHoldingExactlyOneRoles->attach($role);
		$this->fixture->setRoles($objectStorageHoldingExactlyOneRoles);

		$this->assertSame(
			$objectStorageHoldingExactlyOneRoles,
			$this->fixture->getRoles()
		);
	}
	
	/**
	 * @test
	 */
	public function addRoleToObjectStorageHoldingRoles() {
		$role = new \NDH\AccessControl\Domain\Model\Role();
		$objectStorageHoldingExactlyOneRole = new \TYPO3\CMS\Extbase\Persistence\Generic\ObjectStorage();
		$objectStorageHoldingExactlyOneRole->attach($role);
		$this->fixture->addRole($role);

		$this->assertEquals(
			$objectStorageHoldingExactlyOneRole,
			$this->fixture->getRoles()
		);
	}

	/**
	 * @test
	 */
	public function removeRoleFromObjectStorageHoldingRoles() {
		$role = new \NDH\AccessControl\Domain\Model\Role();
		$localObjectStorage = new \TYPO3\CMS\Extbase\Persistence\Generic\ObjectStorage();
		$localObjectStorage->attach($role);
		$localObjectStorage->detach($role);
		$this->fixture->addRole($role);
		$this->fixture->removeRole($role);

		$this->assertEquals(
			$localObjectStorage,
			$this->fixture->getRoles()
		);
	}
	
}
?>