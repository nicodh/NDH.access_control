<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Access Control');

## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['typeConverters'][] = 'NDH\AccessControl\TypeConverter\PersistentObjectConverter';

?>