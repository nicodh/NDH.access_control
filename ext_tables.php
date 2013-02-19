<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Access Control');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_accesscontrol_domain_model_role', 'EXT:access_control/Resources/Private/Language/locallang_csh_tx_accesscontrol_domain_model_role.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_accesscontrol_domain_model_role');
$TCA['tx_accesscontrol_domain_model_role'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:tx_accesscontrol_domain_model_role',
		'label' => 'identifier',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'identifier,description,privileges,parent_role,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Role.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_accesscontrol_domain_model_role.gif'
	),
);

## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

// processDatamap_afterDatabaseOperations
//$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('access_control').'Classes/Backend/Hooks.php:NDH\AccessControl\Backend\Hooks';

//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp'][] = 'EXT:access_control/Classes/Backend/Hooks.php:NDH\AccessControl\Backend\Hooks->postUserLookUp';

?>