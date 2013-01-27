<?php
namespace NDH\AccessControl\Extended\Extbase\Persistence\Generic;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Backend extends \TYPO3\CMS\Extbase\Persistence\Generic\Backend {

	/**
	 * Returns the object with the (internal) identifier, if it is known to the
	 * backend. Otherwise NULL is returned.
	 *
	 * @param string $identifier
	 * @param string $className
	 * @return object The object for the identifier if it is known, or NULL
	 */
	public function getObjectByIdentifier($identifier, $className) {
		if ($this->identityMap->hasIdentifier($identifier, $className)) {
			return $this->identityMap->getObjectByIdentifier($identifier, $className);
		} else {
			$query = $this->queryFactory->create($className);
			$query->getQuerySettings()->setRespectStoragePage(FALSE);
			return $query->matching($query->equals('uid', $identifier))->execute()->getFirst();
		}
	}

	/**
	 * Persists the given object.
	 *
	 * @param \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface $object The object to be inserted
	 * @return void
	 */
	protected function persistObject(\TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface $object) {
		if (isset($this->visitedDuringPersistence[$object])) {
			return;
		}
		$row = array();
		$queue = array();
		$dataMap = $this->dataMapper->getDataMap(get_class($object));
		$properties = $object->_getProperties();
		foreach ($properties as $propertyName => $propertyValue) {
			if (!$dataMap->isPersistableProperty($propertyName) || $this->propertyValueIsLazyLoaded($propertyValue)) {
				continue;
			}
			$columnMap = $dataMap->getColumnMap($propertyName);
			if ($propertyValue instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage) {
				if ($object->_isNew() || $propertyValue->_isDirty()) {
					$this->persistObjectStorage($propertyValue, $object, $propertyName, $row);
				}
				foreach ($propertyValue as $containedObject) {
					if ($containedObject instanceof \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface) {
						$queue[] = $containedObject;
					}
				}
			} elseif ($propertyValue instanceof \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface) {
				if ($object->_isDirty($propertyName)) {
					if ($propertyValue->_isNew()) {
						$this->insertObject($propertyValue);
					}
					$row[$columnMap->getColumnName()] = $this->getPlainValue($propertyValue);
				}
				$queue[] = $propertyValue;
			} elseif ($object->_isNew() || $object->_isDirty($propertyName)) {
				GeneralUtility::loadTCA($dataMap->getTableName());
				$tca = $GLOBALS['TCA'][$dataMap->getTableName()];
				$columnConfig = $tca['columns'][$columnMap->getColumnName()]['config'];
				$row[$columnMap->getColumnName()] = $this->getPlainValue($propertyValue, $columnConfig);

			}
		}
		if (count($row) > 0) {
			$this->updateObject($object, $row);
			$object->_memorizeCleanState();
		}
		$this->visitedDuringPersistence[$object] = $object->getUid();
		foreach ($queue as $queuedObject) {
			$this->persistObject($queuedObject);
		}
	}

	/**
	 * Returns a plain value, i.e. objects are flattened out if possible.
	 *
	 * @param mixed $input
	 * @return mixed
	 */
	protected function getPlainValue($input, $columnConfig = array()) {
		if ($input instanceof \DateTime) {
			if (isset($columnConfig['dbType']) && $columnConfig['dbType'] == 'date') {
				if (isset($columnConfig['default'])) {
					if($columnConfig['default'] == '0000-00-00 00:00:00') {
						return $input->format('Y-m-d H:m:i');
					}
				}
				return  $input->format('Y-m-d');

			} else {
				return $input->format('U');
			}
		} elseif ($input instanceof \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface) {
			return $input->getUid();
		} elseif (is_bool($input)) {
			return $input === TRUE ? 1 : 0;
		} else {
			return $input;
		}
	}
}
