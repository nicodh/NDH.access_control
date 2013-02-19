<?php
namespace NDH\AccessControl\Extended\Extbase\Persistence\Generic\Storage;
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


class Typo3DbBackend extends \TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbBackend {

	protected $queryCounter = 0;

	/**
	 * @inject
	 * @var \NDH\AccessControl\Service\AccessControlService
	 */
	protected $accessControlService;

	/**
	 * Adds additional WHERE statements according to the query settings.
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings The TYPO3 CMS specific query settings
	 * @param string $tableName The table name to add the additional where clause for
	 * @param string $sql
	 * @return void
	 */
	protected function addAdditionalWhereClause(\TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings, $tableName, &$sql) {
		parent::addAdditionalWhereClause($querySettings, $tableName, $sql);
		$this->accessControlService->applyPolicyFilters($this, $tableName, $sql);
	}

	protected function replacePlaceholders(&$sqlString, array $parameters, $tableName = 'foo') {
		parent::replacePlaceholders($sqlString, $parameters, $tableName);
		$this->queryCounter++;
		if(strpos($sqlString, 'tx_projects_domain_model_project')) {
			//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog($sqlString,'extbase',0);
		}

	}

	public function __destruct() {
		//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog($this->queryCounter . ' queries executed','extbase',0);
		if(defined('TYPO3_MODE') && TYPO3_MODE == 'FE') {
			\TOOOL\Intranet\ChromePhp::log('Backend',$this->queryCounter . ' queries executed');
		}
	}

	/**
	 * Adds a row to the storage
	 *
	 * @param string $tableName The database table name
	 * @param array $row The row to be inserted
	 * @param boolean $isRelation TRUE if we are currently inserting into a relation table, FALSE by default
	 * @return int The uid of the inserted row
	 */
	public function addRow($tableName, array $row, $isRelation = FALSE) {
		//$this->accessControlService->checkWriteAccessForTable($tableName, $row);
		return parent::addRow($tableName, $row, $isRelation);
	}

}