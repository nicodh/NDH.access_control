<?php
namespace \NDH\AccessControl\Security\Policy;

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

class Typo3FrontendRole {
	/**
	 * The string identifier of this role
	 *
	 * @var string
	 * @Flow\Identity
	 * @ORM\Id
	 */
	protected $identifier;

	public function setPrivileges($privileges) {
		$this->privileges = $privileges;
	}

	public function getPrivileges() {
		return $this->privileges;
	}

	public function addPrivilege($privilege) {
		$this->privileges[] = $privilege;
	}

	/**
	 * @param string $identifier
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}

	/**
	 * @return string
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 *
	 */
	protected $privileges = array();

	/**
	 * Constructor.
	 *
	 * @param string $identifier The string identifier of this role
	 * @throws \RuntimeException
	 */
	public function __construct($identifier = NULL) {
		if(NULL !== $identifier) {
			$this->identifier = $identifier;
		}
	}

}