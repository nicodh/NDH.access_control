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

class EnableFieldService implements \TYPO3\CMS\Core\SingletonInterface {

	protected $enableFieldWHereClauses = array();

	/**
	 * @param $tableName
	 * @param $additionaWhereClause
	 */
	public function addEnableFieldWhereClause($tableName, $additionalWhereClause) {
		if(!is_array($this->enableFieldWHereClauses[$tableName])) {
			$this->enableFieldWHereClauses[$tableName] = array();
		}
		$this->enableFieldWHereClauses[$tableName][] = $additionalWhereClause;
	}

	public function addEnableField($tableName, $field, $value) {
		if(is_string($value)) {
			$value = '\'' . mysql_real_escape_string($value) . '\'';
		}
		$this->addEnableFieldWhereClause($tableName, $tableName . '.' . $field . ' = ' . $value);
	}

	/**
	 * @param $tableName
	 * @param $additionaWhereClause
	 */
	public function getEnableFieldWHereClauses($tableName) {
		return $this->enableFieldWHereClauses[$tableName];
	}

	public function hasAdditionalEnableFieldsForTable($tableName) {
		return isset($this->enableFieldWHereClauses[$tableName]);
	}
}
