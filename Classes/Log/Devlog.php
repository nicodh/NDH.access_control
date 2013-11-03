<?php
namespace NDH\AccessControl\Log;

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

use TYPO3\CMS\Core\Log\LogLevel;

class Devlog {

	protected $logLevelMap = array (
		-1 => LogLevel::INFO,
		0 => LogLevel::INFO,
		1 => LogLevel::NOTICE,
		2 => LogLevel::WARNING,
		3 => LogLevel::ERROR
	);
	/**
	 *
	 * $logArr = array('msg'=>$msg, 'extKey'=>$extKey, 'severity'=>$severity, 'dataVar'=>$dataVar);
	 * 'msg'		string		Message (in english).
	 * 'extKey'		string		Extension key (from which extension you are calling the log)
	 * 'severity'	integer		Severity: 0 is info, 1 is notice, 2 is warning, 3 is fatal error, -1 is "OK" message
	 * 'dataVar'	array		Additional data you want to pass to the logger.
	 *
	 * @param array $params
	 */
	public function devLog(array $params) {
		$logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Log\\LogManager')->getLogger('TOOOL.Intranet.Log');
		$severityTranslated = $this->logLevelMap[$params['severity']];
		if(!is_array($params['dataVar'])) {
			$params['dataVar'] = array();
		}
		if($params['extKey'] == 'TYPO3\\CMS\\Core\\Authentication\\AbstractUserAuthentication' && $severityTranslated < LogLevel::NOTICE)  {
			$logger->log($severityTranslated, $params['msg'], $params['dataVar']);
		}

	}
}
