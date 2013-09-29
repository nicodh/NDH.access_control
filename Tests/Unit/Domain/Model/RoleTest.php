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
 * Test case for class \NDH\AccessControl\Domain\Model\Role.
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
class RoleTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {
	/**
	 * @var \NDH\AccessControl\Domain\Model\Role
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new \NDH\AccessControl\Domain\Model\Role();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function hasOrExtendsRoleReturnsCorrectValue() {
		$role1 = new \NDH\AccessControl\Domain\Model\Role();
		$role1->setIdentifier('Role1');
		$this->assertEquals(FALSE, $role1->hasOrExtendsRole('Role2'));
		$role2 = new \NDH\AccessControl\Domain\Model\Role();
		$role2->setIdentifier('Role2');
		$role1->setParentRole($role2);
		$this->assertEquals(TRUE, $role1->hasOrExtendsRole('Role1'));
		$this->assertEquals(TRUE, $role1->hasOrExtendsRole('Role2'));
	}

	/**
	 * @test
	 */
	public function getPrivilegesReturnsCorrectValue() {
		$privilegesFixture = array(
			'methods' => array(
				'pluginKey' => array(
					'TOOOL\\AccessControl\\Tests\\Controller\\FixtureController' => array(
						'showAction' => 1,
						'listAction' => 1,
						'newAction' => 1
					)
				)
			)
		);
		$flattenedPrivileges = array(
			'methods' => $privilegesFixture['methods']['pluginKey']
		);
		$role1 = new \NDH\AccessControl\Domain\Model\Role();
		$role1->setSerializedPrivileges(json_encode($privilegesFixture));
		$this->assertEquals($flattenedPrivileges, $role1->getPrivileges());
	}

	/**
		 * @test
		 */
		public function getPrivilegesForNestedRolesReturnsCorrectValue() {
			$privilegesFixture1 = array(
				'methods' => array(
					'pluginKey' => array(
						'TOOOL\\AccessControl\\Tests\\Controller\\FixtureController' => array(
							'showAction' => 1,
							'listAction' => 1,
						)
					)
				)
			);
			$privilegesFixture2 = array(
				'methods' => array(
					'pluginKey' => array(
						'TOOOL\\AccessControl\\Tests\\Controller\\FixtureController' => array(
							'showAction' => 1,
							'listAction' => 1,
							'newAction' => 1,
							'createAction' => 1
						)
					)
				)
			);
			$flattenedPrivileges = array(
				'methods' => array_replace_recursive($privilegesFixture1['methods']['pluginKey'], $privilegesFixture2['methods']['pluginKey'])
			);
			$role1 = new \NDH\AccessControl\Domain\Model\Role();
			$role1->setSerializedPrivileges(json_encode($privilegesFixture1));
			$role2 = new \NDH\AccessControl\Domain\Model\Role();
			$role2->setSerializedPrivileges(json_encode($privilegesFixture2));
			$role1->setParentRole($role2);
			$this->assertEquals($flattenedPrivileges, $role1->getPrivileges());
		}


}
?>