<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Access Control');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_accesscontrol_domain_model_role', 'EXT:access_control/Resources/Private/Language/locallang_csh_tx_accesscontrol_domain_model_role.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_accesscontrol_domain_model_role');
$GLOBALS['TCA']['tx_accesscontrol_domain_model_role'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:tx_accesscontrol_domain_model_role',
		'label' => 'identifier',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'identifier,description,serialized_privileges,parent_role,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Role.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_accesscontrol_domain_model_role.gif'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_accesscontrol_domain_model_party', 'EXT:access_control/Resources/Private/Language/locallang_csh_tx_accesscontrol_domain_model_party.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_accesscontrol_domain_model_party');
$GLOBALS['TCA']['tx_accesscontrol_domain_model_party'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:tx_accesscontrol_domain_model_party',
		'label' => 'uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => '',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Party.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_accesscontrol_domain_model_party.gif'
	),
);

if (!isset($GLOBALS['TCA']['fe_users']['ctrl']['type'])) {
	if (file_exists($GLOBALS['TCA']['fe_users']['ctrl']['dynamicConfigFile'])) {
		require_once($GLOBALS['TCA']['fe_users']['ctrl']['dynamicConfigFile']);
	}
	// no type field defined, so we define it here. This will only happen the first time the extension is installed!!
	$GLOBALS['TCA']['fe_users']['ctrl']['type'] = 'tx_extbase_type';
	$tempColumns = array();
	$tempColumns[$GLOBALS['TCA']['fe_users']['ctrl']['type']] = array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:tx_accesscontrol.tx_extbase_type',
		'config' => array(
			'type' => 'select',
			'items' => array(),
			'size' => 1,
			'maxitems' => 1,
		)
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tempColumns, 1);
}

$tmp_access_control_columns = array(

	'roles' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:tx_accesscontrol_domain_model_frontenduser.roles',
		'config' => array(
			'type' => 'select',
			'foreign_table' => 'tx_accesscontrol_domain_model_role',
			'MM' => 'tx_accesscontrol_frontenduser_role_mm',
			'size' => 10,
			'autoSizeMax' => 30,
			'maxitems' => 9999,
			'multiple' => 0,
			'wizards' => array(
				'_PADDING' => 1,
				'_VERTICAL' => 1,
				'edit' => array(
					'type' => 'popup',
					'title' => 'Edit',
					'script' => 'wizard_edit.php',
					'icon' => 'edit2.gif',
					'popup_onlyOpenIfSelected' => 1,
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				'add' => Array(
					'type' => 'script',
					'title' => 'Create new',
					'icon' => 'add.gif',
					'params' => array(
						'table' => 'tx_accesscontrol_domain_model_role',
						'pid' => '###CURRENT_PID###',
						'setValue' => 'prepend'
						),
					'script' => 'wizard_add.php',
				),
			),
		),
	),
	'party' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:tx_accesscontrol_domain_model_frontenduser.party',
		'config' => array(
			'type' => 'select',
			'foreign_table' => 'tx_accesscontrol_domain_model_party',
			'minitems' => 0,
			'maxitems' => 1,
		),
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users',$tmp_access_control_columns);

$GLOBALS['TCA']['fe_users']['types']['Tx_AccessControl_FrontendUser']['showitem'] = $TCA['fe_users']['types']['0']['showitem'];
$GLOBALS['TCA']['fe_users']['types']['Tx_AccessControl_FrontendUser']['showitem'] .= ',--div--;LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:tx_accesscontrol_domain_model_frontenduser,';
$GLOBALS['TCA']['fe_users']['types']['Tx_AccessControl_FrontendUser']['showitem'] .= 'roles, party';

$GLOBALS['TCA']['fe_users']['columns'][$TCA['fe_users']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:fe_users.tx_extbase_type.Tx_AccessControl_FrontendUser','Tx_AccessControl_FrontendUser');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', $GLOBALS['TCA']['fe_users']['ctrl']['type'],'','after:' . $TCA['fe_users']['ctrl']['label']);
## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

$TCA['fe_users']['types']['Tx_AccessControl_FrontendUser']['showitem'] = $TCA['fe_users']['types']['Tx_Extbase_Domain_Model_FrontendUser']['showitem'] . ',--div--;LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:tx_accesscontrol_domain_model_frontenduser.roles,roles';

//$TCA['fe_groups']['types']['Tx_AccessControl_FrontendUserGroup']['showitem'] = 'hidden;;;;1-1-1, title;;;;2-2-2, description, subgroup;;;;3-3-3, --div--;LLL:EXT:cms/locallang_tca.xml:fe_groups.tabs.options, lockToDomain;;;;1-1-1, TSconfig;;;;2-2-2, felogin_redirectPid;;;;1-1-1, --div--;LLL:EXT:cms/locallang_tca.xml:fe_groups.tabs.extended, tx_extbase_type';
// processDatamap_afterDatabaseOperations
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('access_control').'Classes/Backend/DataMapHooks.php:NDH\AccessControl\Backend\DataMapHooks';