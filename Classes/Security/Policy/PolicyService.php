<?php
namespace NDH\AccessControl\Security\Policy;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */


/**
 * The policy service reads the policy configuration. The security advice asks
 * this service which methods have to be intercepted by a security interceptor.
 *
 * The access decision voters get the roles and privileges configured (in the
 * security policy) for a specific method invocation from this service.
 *
 */
use TYPO3\CMS\Core\SingletonInterface;

class PolicyService implements SingletonInterface {

	const
		ADMIN_IDENTIFIER = 'Administrator',
		PRIVILEGE_ABSTAIN = 0,
		PRIVILEGE_GRANT = 1,
		PRIVILEGE_DENY = 2,
		MATCHER_ANY = 'ANY';

	/**
	 * The settings
	 * @var array
	 */
	protected $settings;

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @var array
	 */
	protected $privileges = array();


	/**
	 * All configured resources
	 * @var array
	 */
	protected $resources = array();


	/**
	 * A multidimensional array used containing the roles and privileges for each intercepted method
	 * @var array
	 */
	protected $acls = array();

	/**
	 * The constraints for entity resources
	 * @var array
	 */
	protected $entityResourcesConstraints = array();

	/**
	 * @var array
	 */
	protected $roles = array();

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @var array
	 */
	protected $cachedPrivileges = array();

	/**
	 * Injects the settings
	 *
	 * @param array $settings Settings of the access control service
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Injects the configuration manager
	 *
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager The configuration manager
	 * @return void
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Returns the privileges a specific role has for the given controlpoint. The returned array
	 * contains the privilege's resource as key of each privilege.
	 *
	 * @param \NDH\AccessControl\Domain\Model\Role $role The role for which the privileges should be returned
	 * @param \NDH\AccessControl\Security\ControlPointInterface $controlPoint The controlpoint for which the privileges should be returned
	 * @return array Array of privileges
	 * @throws \NDH\AccessControl\Security\Exception\NoEntryInPolicyException
	 */
	public function getPrivilegesForControlPoint(\NDH\AccessControl\Domain\Model\Role $role, \NDH\AccessControl\Security\ControlPointInterface $controlPoint) {
		$privileges = array();
		if($role->getIdentifier() == self::ADMIN_IDENTIFIER) {
			return array(self::PRIVILEGE_GRANT);
		}
		$methodName = $controlPoint->getMethodName();
		$lassName = $controlPoint->getClassName();
		if(isset($this->cachedPrivileges[$role->getIdentifier()])) {
			$privilegesForRole = $this->cachedPrivileges[$role->getIdentifier()];
		} else {
			$privilegesForRole = $role->getPrivileges();
			$this->cachedPrivileges[$role->getIdentifier()] = $privilegesForRole;
		}
		if(is_array($privilegesForRole)) {
			if(isset($privilegesForRole['methods'][$lassName][$methodName])) {
				$privileges[] = $privilegesForRole['methods'][$lassName][$methodName];
			}
		}
		return $privileges;
	}

	/**
	 * Returns the privilege a specific role has for the given resource.
	 * Note: Resources with runtime evaluations return always a PRIVILEGE_DENY!
	 * @see getPrivilegesForControlPoint() instead, if you need privileges for them.
	 *
	 * @param \NDH\AccessControl\Domain\Model\Role $role The role for which the privileges should be returned
	 * @param string $resource The resource for which the privileges should be returned
	 * @return integer One of: PRIVILEGE_GRANT, PRIVILEGE_DENY
	 * @throws \NDH\AccessControl\Security\Exception\NoEntryInPolicyException
	 */
	public function getPrivilegeForResource(\NDH\AccessControl\Domain\Model\Role $role, $resource) {
		if($role->getIdentifier() == self::ADMIN_IDENTIFIER) {
			return array(self::PRIVILEGE_GRANT);
		}
		if (!isset($this->acls[$resource])) {
			if (isset($this->resources[$resource])) {
				return self::PRIVILEGE_DENY;
			} else {
				throw new \NDH\AccessControl\Security\Exception\NoEntryInPolicyException('The given resource ("' . $resource . '") was not found in the policy cache. Most likely you have to recreate the AOP proxy classes.', 1248348214);
			}
		}

		$roleIdentifier = $role->getIdentifier();
		if (!array_key_exists($roleIdentifier, $this->acls[$resource])) {
			return NULL;
		}

		return $this->acls[$resource][$roleIdentifier]['privilege'];
	}

	/**
	 * Checks if the given method has a policy entry. If $roles are given
	 * this method returns only TRUE, if there is an acl entry for the method for
	 * at least one of the given roles.
	 *
	 * @param string $className The class name to check the policy for
	 * @param string $methodName The method name to check the policy for
	 * @param array $roles
	 * @return boolean TRUE if the given controller action has a policy entry
	 */
	public function hasPolicyEntryForMethod($className, $methodName, array $roles = array()) {
		$methodIdentifier = strtolower($className . '->' . $methodName);

		if (isset($this->acls[$methodIdentifier])) {
			if (count($roles) > 0) {
				foreach ($roles as $roleIdentifier) {
					if (isset($this->acls[$methodIdentifier][$roleIdentifier])) {
						return TRUE;
					}
				}
			} else {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Checks if the given entity type has a policy entry for at least one of the given roles
	 *
	 * @param string $entityType The entity type (object name) to be checked
	 * @param array $roles The roles to be checked
	 * @return boolean TRUE if the given entity type has a policy entry
	 */
	public function hasPolicyEntryForEntityType($entityType, array $roles) {
		if (isset($this->entityResourcesConstraints[$entityType])) {
			foreach ($this->entityResourcesConstraints[$entityType] as $resource => $constraint) {
				foreach ($roles as $role) {
					if (isset($this->acls[$resource][(string)$role])) {
						return TRUE;
					}
				}
			}
		}

		return FALSE;
	}

	/**
	 * Checks if the given there is any policy entry for entities
	 *
	 * @return boolean TRUE if the a resource entry for entities exist
	 */
	public function hasPolicyEntriesForEntities() {
		return (count($this->entityResourcesConstraints) > 0);
	}

	/**
	 * Returns an array of not GRANTED or explicitly DENIED resource constraints, which are
	 * configured for the given entity type and for at least one of the given roles.
	 * Note: If two roles have conflicting privileges for the same resource the GRANT priviliege
	 * has precedence.
	 *
	 * @param string $entityType The entity type (object name)
	 * @param array $roles An array of roles the resources have to be configured for
	 * @return array An array resource constraints
	 */
	public function getResourcesConstraintsForEntityTypeAndRoles($entityType, array $roles) {
		$deniedResources = array();
		$grantedResources = array();
		$abstainedResources = array();

		foreach ($this->entityResourcesConstraints[$entityType] as $resource => $constraint) {
			if ($constraint === self::MATCHER_ANY) {
				continue;
			}

			foreach ($roles as $roleIdentifier) {
				if (!isset($this->acls[$resource][(string)$roleIdentifier]['privilege'])
					|| $this->acls[$resource][(string)$roleIdentifier]['privilege'] === self::PRIVILEGE_ABSTAIN) {

					$abstainedResources[$resource] = $constraint;
				} elseif ($this->acls[$resource][(string)$roleIdentifier]['privilege'] === self::PRIVILEGE_DENY) {
					$deniedResources[$resource] = $constraint;
				} else {
					$grantedResources[] = $resource;
				}
			}
		}

		foreach ($grantedResources as $grantedResource) {
			if (isset($abstainedResources[$grantedResource])) {
				unset($abstainedResources[$grantedResource]);
			}
		}

		return array_merge($abstainedResources, $deniedResources);
	}


	/**
	 * Checks if there is a special resource definition covering all objects of the
	 * given type and if this resource has been granted to at least one of the
	 * given roles.
	 *
	 * @param string $entityType The entity type (object name)
	 * @param array $roles An array of roles the resources have to be configured for
	 * @return array TRUE if general access is granted, FALSE otherwise
	 */
	public function isGeneralAccessForEntityTypeGranted($entityType, array $roles) {
		$foundGeneralResourceDefinition = FALSE;
		foreach ($this->entityResourcesConstraints[$entityType] as $resource => $constraint) {
			if ($constraint === self::MATCHER_ANY) {
				$foundGeneralResourceDefinition = TRUE;
				$foundGrantPrivilege = FALSE;
				foreach ($roles as $roleIdentifier) {
					if (!isset($this->acls[$resource][(string)$roleIdentifier]['privilege'])) {
						continue;
					} elseif ($this->acls[$resource][(string)$roleIdentifier]['privilege'] === self::PRIVILEGE_DENY) {
						return FALSE;
					} elseif ($this->acls[$resource][(string)$roleIdentifier]['privilege'] === self::PRIVILEGE_GRANT) {
						$foundGrantPrivilege = TRUE;
					}
				}
				if ($foundGrantPrivilege === TRUE) {
					return TRUE;
				}
			}
		}

		if ($foundGeneralResourceDefinition === FALSE) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Parses the policy and stores the configured entity acls in the internal acls array
	 *
	 * @return void
	 * @throws \NDH\AccessControl\Security\Exception\InvalidPrivilegeException
	 */
	protected function parseEntityAcls() {
		foreach ($this->policy['acls'] as $role => $aclEntries) {
			if (!array_key_exists('entities', $aclEntries)) {
				continue;
			}

			foreach ($aclEntries['entities'] as $resource => $privilege) {
				if (!isset($this->acls[$resource])) {
					$this->acls[$resource] = array();
				}
				$this->acls[$resource][$role] = array();
				switch ($privilege) {
					case 'GRANT':
						$this->acls[$resource][$role]['privilege'] = self::PRIVILEGE_GRANT;
						break;
					case 'DENY':
						$this->acls[$resource][$role]['privilege'] = self::PRIVILEGE_DENY;
						break;
					case 'ABSTAIN':
						$this->acls[$resource][$role]['privilege'] = self::PRIVILEGE_ABSTAIN;
						break;
					default:
						throw new \NDH\AccessControl\Security\Exception\InvalidPrivilegeException('Invalid privilege defined in security policy. An ACL entry may have only one of the privileges ABSTAIN, GRANT or DENY, but we got:' . $privilege . ' for role : ' . $role . ' and resource: ' . $resource, 1267311437);
				}
			}
		}
	}

	/**
	 * Sets the default ACLs for the Everybody role
	 *
	 * @return void
	 */
	protected function setAclsForEverybodyRole() {
		$this->policy['roles']['Everybody'] = array();

		if (!isset($this->policy['acls']['Everybody'])) {
			$this->policy['acls']['Everybody'] = array();
		}
		if (!isset($this->policy['acls']['Everybody']['methods'])) {
			$this->policy['acls']['Everybody']['methods'] = array();
		}
		if (!isset($this->policy['acls']['Everybody']['entities'])) {
			$this->policy['acls']['Everybody']['entities'] = array();
		}

		foreach (array_keys($this->policy['resources']['methods']) as $resource) {
			if (!isset($this->policy['acls']['Everybody']['methods'][$resource])) {
				$this->policy['acls']['Everybody']['methods'][$resource] = 'ABSTAIN';
			}
		}
		foreach ($this->policy['resources']['entities'] as $resourceDefinition) {
			foreach (array_keys($resourceDefinition) as $resource) {
				if (!isset($this->policy['acls']['Everybody']['entities'][$resource])) {
					$this->policy['acls']['Everybody']['entities'][$resource] = 'ABSTAIN';
				}
			}
		}
	}


}

?>