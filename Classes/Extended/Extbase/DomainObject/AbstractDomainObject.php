<?php
namespace NDH\AccessControl\Extended\Extbase\DomainObject;

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


class AbstractEntity extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * Reconstitutes a property. Only for internal use.
	 *
	 * @param string $propertyName
	 * @param mixed $propertyValue
	 * @return boolean
	 */
	public function _setProperty($propertyName, $propertyValue) {
		\TOOOL\Intranet\ChromePhp::log('_setProperty: ' . get_class($this) . ':' . $propertyName);
		if ($this->_hasProperty($propertyName)) {
			$this->{$propertyName} = $propertyValue;
			return TRUE;
		}
		return FALSE;
	}
}
