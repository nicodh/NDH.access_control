<?php
namespace NDH\AccessControl\Service;

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

class AccessControlService implements SingletonInterface{

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	protected $filterImplementations = array();

	/**
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	public function initialize($context) {
		$context->getSettings();
	}

	public function applyPolicyFilters($objectToProcess, $resource) {
		return;
		$policies = $this->policyProvider->getPoliciesForCurrentContext();
		foreach($this->filterImplementations as $implementation) {
			if($implementation->supports($objectToProcess)) {
				$implementation->applyPolicyFilter($objectToProcess, $resource, $policies);
			}
		}
	}

	public function convertClassNameToTableName($className) {
		$dataMapper = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Mapper\\DataMapper');
		return $dataMapper->convertClassNameToTableName($className);
	}

	public function convertPropertyNameToColumnName($propertyName) {
		$dataMapper = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Mapper\\DataMapper');
		return $dataMapper->convertPropertyNameToColumnName($propertyName);
	}

}
