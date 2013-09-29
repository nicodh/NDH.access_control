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
 * Test case for class Tx_Access_control_Controller_RoleController.
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
class AccessListServiceTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {
	/**
	 * @var \TOOOL\AccessControl\Service\AccessListService
	 */
	protected $accessListService;

	/**
	 * @var \NDH\AccessControl\Domain\Model\Role
	 */
	protected $roleFixture;

	/**
	 * @var string
	 */
	protected $accessListFixturesPath;

	public function setUp() {
		$this->accessListFixturesPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('access_control') . 'Tests/Service/Fixtures/AccessLists/';
		$this->accessListService = new \TOOOL\AccessControl\Service\AccessListService();
		$this->roleFixture = new \NDH\AccessControl\Domain\Model\Role();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 * @expectedException \NDH\AccessControl\Security\Exception\UnauthorizedAccessException
	 */
	public function accessListServiceThrowsExceptionIfNoAccessListForObjectFound() {
		$this->accessListService->getAccessListFromPhpFiles('NonExistingObjectName', $this->roleFixture, $this->accessListFixturesPath);
	}

	/**
	 * @test
	 * @expectedException \NDH\AccessControl\Security\Exception\UnauthorizedAccessException
	 */
	public function accessListServiceThrowsExceptionIfNoAccessListForRoleFound() {
		$roleFixture = new \NDH\AccessControl\Domain\Model\Role();
		$roleFixture->setIdentifier('NonExistingRoleIdentifier');
		$this->accessListService->getAccessListFromPhpFiles('Object1', $roleFixture, $this->accessListFixturesPath);
	}

	/**
	 * @test
	 */
	public function accessListServiceReturnsCorrectAccessList() {
		$roleFixture = new \NDH\AccessControl\Domain\Model\Role();
		$roleFixture->setIdentifier('Administrator');
		$accessList = $this->accessListService->getAccessListFromPhpFiles('Object1', $roleFixture, $this->accessListFixturesPath);
		$this->assertEquals($accessList, array( 'name' => 'Object1', 'adminProperty1' =>true, 'prop2' => 123));

			// accessList with includes
		$nestedAccessList = $this->accessListService->getAccessListFromPhpFiles('Object3', $roleFixture, $this->accessListFixturesPath);
		$this->assertEquals($nestedAccessList, array(
					'name' => 'Object3',
					'object2' => array(
						'name' => 'Object2',
						'prop2' => 123,
						'adminProperty1' => TRUE
					)
			)
		);
			// other role
		$roleFixture->setIdentifier('BasicUser');
		$accessList = $this->accessListService->getAccessListFromPhpFiles('Object1', $roleFixture, $this->accessListFixturesPath);
		$this->assertEquals($accessList, array( 'name' => 'Object1', 'propertyFromBasicUser' => 123));
	}

	/**
	 * @test
	 */
	public function accessListServiceReturnsCorrectAccessListIncludingParentAccessList() {
		$roleFixture = new \NDH\AccessControl\Domain\Model\Role();
		$roleFixture->setIdentifier('BasicUser');
		$parentRole = new \NDH\AccessControl\Domain\Model\Role();
		$parentRole->setIdentifier('Anonymous');
		$roleFixture->setParentRole($parentRole);
		$accessList = $this->accessListService->getAccessListFromPhpFiles('Object1', $roleFixture, $this->accessListFixturesPath);
		$this->assertEquals($accessList, array( 'name' => 'Object1','propertyFromBasicUser' => 123, 'propertyFromAnonymous' => 123));

		$roleFixtureAdmin = new \NDH\AccessControl\Domain\Model\Role();
		$roleFixtureAdmin->setIdentifier('Administrator');
		$parentRole->setParentRole($roleFixtureAdmin);
		$roleFixture->setParentRole($parentRole);
		$accessList = $this->accessListService->getAccessListFromPhpFiles('Object1', $roleFixture, $this->accessListFixturesPath);
		$this->assertEquals($accessList, array(
			'name' => 'Object1',
			'propertyFromBasicUser' => 123,
			'propertyFromAnonymous' => 123,
			'adminProperty1' => TRUE,
			'prop2' => 123
		));

	}

}
?>