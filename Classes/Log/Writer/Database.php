<?php
namespace NDH\AccessControl\Log\Writer;

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
 * Demonstrating the database writer of the TYPO3 Logging API
 *
 */
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Fluid\Tests\Unit\Core\ViewHelper\ViewHelperVariableContainerTest;

class Database  extends \TYPO3\CMS\Core\Log\Writer\AbstractWriter{

	protected $logTable = 'tx_accesscontrol_log';

	/**
	 * Set Configuration
	 *
	 * @return void
	 * @static
	 */
	static protected function initializeConfiguration() {
		$GLOBALS['TYPO3_CONF_VARS']['LOG']['NDH']['AccessControl']['Log'] = array(
			'writerConfiguration' => array(
				LogLevel::ERROR => array(
					'\\TOOL\\Intranet\\Log\\Writer\\Database' => array(
					)
				),
				LogLevel::DEBUG => array(
					'\\TOOL\\Intranet\\Log\\Writer\\Database' => array(
					)
				),
			)
		);
	}

	/**
	 * Writes the log record
	 *
	 * @param \TYPO3\CMS\Core\Log\LogRecord $record Log record
	 * @return \TYPO3\CMS\Core\Log\Writer\WriterInterface $this
	 * @throws \RuntimeException
	 */
	public function writeLog(\TYPO3\CMS\Core\Log\LogRecord $record) {
		$context = '';
		$userId = 0;
		if(defined('TYPO3_MODE')) {
			$context = TYPO3_MODE;
			if(isset($GLOBALS['TSFE']->loginUser) && TYPO3_MODE == 'FE') {
				$userId = $GLOBALS["TSFE"]->fe_user->user['uid'];
			} else if ($GLOBALS['TSFE']->beUserLogin && TYPO3_MODE == 'BE') {
				$userId = $GLOBALS['BE_USER']->user['uid'];
			}
		}

		// add script context
		if($record['level'] == LogLevel::DEBUG || $record['level'] < 4) {
			$record = $this->processLogRecord($record);
		}

		// all severities below warning get additional environment info
		if($record['level'] < 4) {
			$record->addData(\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('_ARRAY'));
		}
		$data = array(
			'request_id' => $record['requestId'],
			'context' => $context,
			'tstamp' => time(),
			'logdate' => date('Y-m-d H:m:i'),
			'userid' => $userId,
			'time_micro' => $record['created'],
			'component' => $record['component'],
			'level' => $record['level'],
			'message' => $record['message'],
			'data' => !empty($record['data']) ? json_encode($record['data']) : ''
		);
		if (FALSE === $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->logTable, $data)) {
			throw new \RuntimeException('Could not write log record to database', 1345036334);
		}
		return $this;
	}

	public function processLogRecord(\TYPO3\CMS\Core\Log\LogRecord $logRecord) {
		$trace = debug_backtrace();
		// skip first since it's always the current method
		array_shift($trace);
		// the call_user_func call is also skipped
		array_shift($trace);
		// skip TYPO3\CMS\Core\Log classes
		// @TODO: Check, if this still works. This was 't3lib_log_' before namespace switch.
		$i = 0;
		while (isset($trace[$i]['class']) && FALSE !== strpos($trace[$i]['class'], 'TYPO3\\CMS\\Core\\Log')) {
			$i++;
		}
		// we should have the call source now
		$logRecord->addData(array(
			'file' => isset($trace[$i - 1]['file']) ? $trace[$i - 1]['file'] : (isset($trace[$i]['file']) ? $trace[$i]['file'] : NULL),
			'line' => isset($trace[$i - 1]['line']) ? $trace[$i - 1]['line'] : (isset($trace[$i]['line']) ? $trace[$i]['line'] : NULL),
			'class' => isset($trace[$i]['class']) ? $trace[$i]['class'] : (isset($trace[$i + 1]['class']) ? $trace[$i + 1]['class'] : NULL),
			'function' => isset($trace[$i]['function']) ? $trace[$i]['function'] : (isset($trace[$i + 1]['function']) ? $trace[$i + 1]['function'] : NULL),
		));

		return $logRecord;
	}
}

?>