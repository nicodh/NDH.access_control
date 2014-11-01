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

class AccessListService implements \TYPO3\CMS\Core\SingletonInterface{


	/**
	 * @param string $objectName
	 * @param \NDH\AccessControl\Domain\Model\Role  $role
	 * @param string $accessListBasePath
	 * @param bool $isParentRoleRequest
	 *
	 */
	public function getAccessListFromPhpFiles($objectName, \NDH\AccessControl\Domain\Model\Role $role, $accessListBasePath, $isParentRoleRequest = FALSE) {

		$parentAccessList = array();
        //ChromePhp::log('getAccessListFromPhpFiles',$objectName . ' for ' . $role->getIdentifier() );
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
        //ChromePhp::log('$parentAccessList',$parentAccessList);
        //ChromePhp::log('accessList',$accessList);
        self::array_merge_recursive_overrule($parentAccessList, $accessList);
        //ChromePhp::log('mergedAccessList',$parentAccessList);
        return $parentAccessList;
	}

    /**
     * merges 2 arrays allowing a mixture of one and multi level elements:
     * $arr0 = array('foo', 'bar' => array('uid'));
     * $arr1 = array('uid', 'team' => array('uid'));
     *
     *
     * @param array $arr0
     * @param array $arr1
     * @return void
     */
	public static function array_merge_recursive_overrule(&$arr0 = NULL, $arr1 = NULL) {
        if (!is_array($arr0)) {
            $arr0 = array();
        }
        if (!is_array($arr1)) {
            $arr1 = array();
        }
		foreach ($arr1 as $key => $val) {
            if (isset($arr0[$key]) && is_array($arr0[$key])&& is_array($arr1[$key])) {
                self::array_merge_recursive_overrule($arr0[$key], $arr1[$key]);
            } elseif(is_numeric($key)) {
                // numeric key means a simple string was found in the array
                if (!in_array($val, array_values($arr0))) {
                    $arr0[] = $val;
                }
            } else{
                $arr0[$key] = $arr1[$key];
            }
		}
        array_unique($arr0);
		reset($arr0);
	}

}
 