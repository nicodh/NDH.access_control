<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_accesscontrol_domain_model_role'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_accesscontrol_domain_model_role']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, identifier, description, serialized_privileges, parent_role',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, identifier, description, serialized_privileges, parent_role, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
	
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_accesscontrol_domain_model_role',
				'foreign_table_where' => 'AND tx_accesscontrol_domain_model_role.pid=###CURRENT_PID### AND tx_accesscontrol_domain_model_role.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
	
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),

		'identifier' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:tx_accesscontrol_domain_model_role.identifier',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:tx_accesscontrol_domain_model_role.description',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'serialized_privileges' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:tx_accesscontrol_domain_model_role.serialized_privileges',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			)
		),
		'parent_role' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:access_control/Resources/Private/Language/locallang_db.xlf:tx_accesscontrol_domain_model_role.parent_role',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_accesscontrol_domain_model_role',
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
		
	),
);
## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

if(TYPO3_MODE == 'BE') {

//require_once(\TYPO3\Cms\Core\Utility\ExtensionManagementUtility::extPath('access_control').'Classes/Backend/CustomFields.php');


	$TCA['tx_accesscontrol_domain_model_role']['columns']['serialized_privileges']['config'] = array (
		'type' => 'user',
		'size' => '30',
		'userFunc' => 'EXT:access_control/Classes/Backend/RoleUserFields.php:NDH\\AccessControl\\Backend\\RoleUserFields->renderPrivilegesWizard',
	);

	$TCA['tx_accesscontrol_domain_model_role']['columns']['description']['config']['rows'] = 3;

	$TCA['tx_accesscontrol_domain_model_role']['types'][1]['showitem'] = 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, identifier, name, description, parent_role,--div--;Rechte,serialized_privileges';

	$TCA['tx_accesscontrol_domain_model_role']['columns']['parent_role']['config']['items'] = array(array('LLL:EXT:access_control/Resources/Private/Language/locallang.xlf:tx_accesscontrol_domain_model_frontenduser.select_role','0'));
}