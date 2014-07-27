<?php

namespace NDH\AccessControl\ViewHelpers\Backend;

class IfHasPrivilegeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper {

	/**
	 * Renders <f:then> child if any BE user is currently authenticated, otherwise renders <f:else> child.
	 *
	 * @param array $privileges
	 * @param string $pluginKey
	 * @param string $className
	 * @param string $methodName
	 * @return string the rendered string
	 */
	public function render(array $privileges, $pluginKey, $className, $methodName) {
		$condition = FALSE;
		if($methodName == '*') {
			$condition = isset($privileges['methods'][$pluginKey][$className]);
		} else {
			$condition = isset($privileges['methods'][$pluginKey][$className][$methodName]);
		}
		if ($condition) {
			return $this->renderThenChild();
		}
		return $this->renderElseChild();
	}
}