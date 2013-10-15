<?php
namespace TOOOL\AccessControl\Service;

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
 */
use TOOOL\Intranet\ChromePhp;
use TYPO3\CMS\Core\FormProtection\Exception;
use TYPO3\CMS\Core\Tests\Unit\Utility\GeneralUtilityTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AccessListService implements \TYPO3\CMS\Core\SingletonInterface{


	/**
	 * // $objectName, $role
	 */
	public function getAccessListFromPhpFiles($objectName, \NDH\AccessControl\Domain\Model\Role $role, $accessListBasePath, $isParentRoleRequest = FALSE) {

		$parentAccessList = array();
		$accessList = array();
		if($role->getParentRole() !== NULL) {
			$parentAccessList = $this->getAccessListFromPhpFiles($objectName, $role->getParentRole(), $accessListBasePath, TRUE);
		}
		$accessListFile = $accessListBasePath . $role->getIdentifier() . '/' . $objectName . '.php';
		if(!file_exists($accessListFile)) {
			if(empty($parentAccessList) && !$isParentRoleRequest) {
				throw new \NDH\AccessControl\Security\Exception\UnauthorizedAccessException('No access list found!' . $accessListFile);
			}
			return $parentAccessList;
		} else {
			// it might happen that no specific file exists fot that role, but then
			// an access list has to be in place in one of the parent roles
			$accessList = require($accessListFile);
		}
		return $this->array_merge_recursive_overrule($accessList, $parentAccessList);
	}

	protected function array_merge_recursive_overrule(array $arr0, array $arr1) {
		foreach ($arr1 as $key => $val) {
			if (is_array($arr0[$key]) && is_array($arr1[$key])) {
				$arr0[$key] = $this->array_merge_recursive_overrule($arr0[$key], $arr1[$key]);
			} else if(is_numeric($key)){
				// in case the key is just the index
				$arr0[] = $val;
			} else {
				$arr0[$key] = $val;
			}
		}
		reset($arr0);
		return $arr0;
	}

}
 