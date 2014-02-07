<?php
namespace NDH\AccessControl\Backend;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Nico de Haen <mail@ndh-websolutions.de>
 *  All rights reserved
 *
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * @package
 * @author Nico de Haen
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RoleUserFields {



	protected $currentUid;

	/**
	 * @var \NDH\AccessControl\Domain\Repository\RoleRepository
	 * @inject
	 *
	 */
	protected $roleRepository;


	/**
	 * @var array
	 */
	protected $typoScriptSetupCache = array();

	public function renderPrivilegesWizard($PA, $fObj) {

		if (!is_object($this->policyService)) {
			$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
			$this->roleRepository = $objectManager->get('NDH\AccessControl\Domain\Repository\RoleRepository');
		}
		$this->currentUid = $PA['row']['uid'];
		$currentRole = $this->roleRepository->findByUid($this->currentUid);

		$standAloneView = GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$standAloneView->setFormat('html');
		$standAloneView->setTemplatePathAndFilename(ExtensionManagementUtility::extPath('access_control') . 'Resources/Private/Templates/Backend/UserFields/RolePolicies.html');
		$standAloneView->setPartialRootPath(ExtensionManagementUtility::extPath('access_control') . 'Resources/Private/Partials/');
		$standAloneView->assign('extensions', $this->listControllerActions());
		//$standAloneView->assign('objects',$this->getObjectProperties());
		$standAloneView->assign('currentUid', $this->currentUid);
		$standAloneView->assign('currentRole', $currentRole);

		if(empty($PA['row']['serialized_privileges'])) {
			$privileges = 'null';
		} else {
			$privileges = $PA['row']['serialized_privileges'];
		}
		$standAloneView->assign('privileges', $privileges);

		if (!empty($PA['row']['parent_role'])) {
			$parentRole = $this->roleRepository->findByUid($PA['row']['parent_role']);
			$inheritedPrivileges = $parentRole->getPrivileges();
			$standAloneView->assign('inheritedPrivileges', $inheritedPrivileges['methods']);
			$standAloneView->assign('inheritedPrivilegesJson', json_encode($inheritedPrivileges));
		} else {
			$inheritedPrivileges = array();
		}
		$standAloneView->assign('inheritedPrivileges', $inheritedPrivileges);
		$standAloneView->assign('inheritedPrivilegesJson', json_encode($inheritedPrivileges));
		$content = $standAloneView->render();
		return $content;
	}


	/**
	 * No support for single table inheritance yet!!
	 * TODO: implement support for songle table inheritance
	 *
	 * @param $tableName
	 */
	public function convertTablenameToClassName($tableName) {
		if(strpos($tableName, 'domain_model')) {
			$parts = explode('_', $tableName);
			$extensionName = $parts[1];
			$extensionPart = GeneralUtility::underscoredToUpperCamelCase(ExtensionManagementUtility::getExtensionKeyByPrefix('tx_' . $extensionName));
			$modelName = $parts[4];
			return $extensionPart . '\\Domain\\Model\\' .  GeneralUtility::underscoredToUpperCamelCase($modelName);
		} else {
			return NULL;
		}

		$classNames = array();
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$configurationManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
		$configuration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		foreach($configuration['persistence']['classes'] as $className => $classConfiguration) {
			if(isset($classConfiguration['mapping']['tableName']) && $classConfiguration['mapping']['tableName'] == $tableName){
				$classNames[$classConfiguration['mapping']['recordType']] = $className;
			}
		}
	}

	public function renderControllerActionsWizard()  {
		$controllerActions = $this->listControllerActions();
		$standAloneView = GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$standAloneView->setFormat('html');
		$standAloneView->setTemplatePathAndFilename(ExtensionManagementUtility::extPath('access_control') . 'Resources/Private/Templates/Wizards/ControllerActions.html');
		$standAloneView->assign('extensions', $controllerActions);
		$standAloneView->assign('currentUid', $this->currentUid);
		return $standAloneView->render();
	}

	public function renderObjectAccessWizard()  {
		$objects = $this->getObjectProperties();
		$standAloneView = GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$standAloneView->setFormat('html');
		$standAloneView->setTemplatePathAndFilename(ExtensionManagementUtility::extPath('access_control') . 'Resources/Private/Templates/Wizards/ObjectAccess.html');
		$standAloneView->assign('objects', $objects);
		$standAloneView->assign('currentUid', $this->currentUid);
		return $standAloneView->render();

	}

	/**
	 * get all controller action pairs of installed extensions
	 *
	 * @return array
	 */
	protected function listControllerActions() {
		global $TYPO3_CONF_VARS;
		GeneralUtility::loadTCA('tt_content');
		$controllerActions = array();
		$excludeExtensions = array('About','Aboutmodules','Beuser','ExtensionBuilder','ExtensionManager','Lang','Viewpage','Reports');
		foreach($TYPO3_CONF_VARS['EXTCONF']['extbase']['extensions'] as $extensionName => $extension) {
			if(isset($extension['plugins']) && !in_array($extensionName, $excludeExtensions)) {
				foreach($extension['plugins'] as $pluginKey => $pluginConfig) {
					foreach($GLOBALS['TCA']['tt_content']['columns']['list_type']['config']['items'] as $item) {
						if($item[1] == strtolower($extensionName .  '_' . $pluginKey)) {
							$extension['plugins'][$pluginKey]['name'] = $item[0];
						}
					}
					foreach($pluginConfig['controllers'] as $controllerObjectName => $actions) {
						$extension['plugins'][$pluginKey]['controllers'][$controllerObjectName]['className'] = $this->getControllerClassName($extensionName, $pluginKey, $controllerObjectName);
					}

				}
				$controllerActions[$extensionName] = $extension;
			}
		}
		return $controllerActions;
	}

	protected function getControllerClassName($extensionName, $pluginKey, $objectName) {
		$extensionKey = ExtensionManagementUtility::getExtensionKeyByPrefix('tx_' . strtolower($extensionName));
		$typoscriptSetup = $this->getTypoScriptSetup(1);
		if(isset($typoscriptSetup['tt_content.']['list.']['20.'][strtolower($extensionName . '_' . $pluginKey) . '.'])) {
			$pluginConfig = $typoscriptSetup['tt_content.']['list.']['20.'][strtolower($extensionName . '_' . $pluginKey) . '.'];
			return $pluginConfig['vendorName'] . '\\' . GeneralUtility::underscoredToUpperCamelCase($extensionKey) . '\\Controller\\' . $objectName . 'Controller';
		}
	}

	/**
	 * we need the Typoscript setup to derive the vendor name for an extension
	 *
	 * @param $pageId
	 * @return mixed
	 */
	protected function getTypoScriptSetup($pageId) {
		if (!array_key_exists($pageId, $this->typoScriptSetupCache)) {
			$template = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\TypoScript\\TemplateService');
			// do not log time-performance information
			$template->tt_track = 0;
			$template->init();
			// Get the root line
			$rootline = array();
			if ($pageId > 0) {
				/** @var $sysPage \TYPO3\CMS\Frontend\Page\PageRepository */
				$sysPage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
				// Get the rootline for the current page
				$rootline = $sysPage->getRootLine($pageId, '', TRUE);
			}
			// This generates the constants/config + hierarchy info for the template.
			$template->runThroughTemplates($rootline, 0);
			$template->generateConfig();
			$this->typoScriptSetupCache[$pageId] = $template->setup;
		}
		return $this->typoScriptSetupCache[$pageId];
	}

	/**
	 * get all object properties that are exclude fields
	 * no distinction for single table inheritance yet!!
	 * TODO: implement support for single table inheritance
	 *
	 * @return array
	 */
	protected function getObjectProperties() {
		$items = array();
		$standardFields = array('hidden', 'starttime', 'endtime', 'sys_language_uid', 'l10n_parent');
		$theTypes = \TYPO3\CMS\Backend\Utility\BackendUtility::getExcludeFields();
		foreach ($theTypes as $theTypeArrays) {
			list($theTableLabel, $theFullFieldLabel) = explode(':', $theTypeArrays[0]);
			list($theTable, $theFullField) = explode(':', $theTypeArrays[1]);
			if($GLOBALS['TCA'][$theTable]['ctrl']['enableAccessControl']) {
				// If the field comes from a FlexForm, the syntax is more complex
				$theFieldParts = explode(';', $theFullField);
				$theField = array_pop($theFieldParts);
				// Add header if not yet set for table:
				if (!array_key_exists($theTable, $items)) {
					$icon = \TYPO3\CMS\Backend\Utility\IconUtility::mapRecordTypeToSpriteIconName($theTable, array());
					$items[$theTable] = array(
						'label' => $theTableLabel,
						'icon' => $icon,
						'properties' => array()
					);
				}
				// Add help text
				$helpText = array();
				$GLOBALS['LANG']->loadSingleTableDescription($theTable);
				$helpTextArray = $GLOBALS['TCA_DESCR'][$theTable]['columns'][$theFullField];
				if (!empty($helpTextArray['description'])) {
					$helpText['description'] = $helpTextArray['description'];
				}
				// Item configuration:
				if(!in_array($theField, $standardFields)) {

					$items[$theTable]['properties'][] = array(
						'label' => $theFullFieldLabel,
						'description' => $helpText['description'],
						'fieldName' => $theField
					);
				}
				if(empty($items[$theTable]['properties'])) {
					unset($items[$theTable]);
				}
			}
		}
		return $items;
	}

}
