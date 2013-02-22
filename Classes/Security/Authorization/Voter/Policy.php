<?php
namespace NDH\AccessControl\Security\Authorization\Voter;

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
 * An access decision voter, that asks the Flow PolicyService for a decision.
 *
 * @Flow\Scope("singleton")
 */
class Policy implements \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface {

	/**
	 * The policy service
	 * @var \NDH\AccessControl\Security\Policy\PolicyService
	 */
	protected $policyService;

	/**
	 * Constructor.
	 *
	 * @param \NDH\AccessControl\Security\Policy\PolicyService $policyService The policy service
	 */
	public function __construct(\NDH\AccessControl\Security\Policy\PolicyService $policyService) {
		$this->policyService = $policyService;
	}

	/**
	 * This is the default Policy voter, it votes for the access privilege for the given control point
	 *
	 * @param \NDH\AccessControl\Security\ContextInterface $securityContext The current security context
	 * @param \NDH\AccessControl\Security\ControlPointInterface $controlPoint The controlpoint to vote for
	 * @return integer One of: VOTE_GRANT, VOTE_ABSTAIN, VOTE_DENY
	 */
	public function voteForControlPoint(\NDH\AccessControl\Security\ContextInterface $securityContext, \NDH\AccessControl\Security\ControlPointInterface $controlPoint) {
		$accessGrants = 0;
		$accessDenies = 0;
		foreach ($securityContext->getRoles() as $role) {
			try {
				$privileges = $this->policyService->getPrivilegesForControlPoint($role, $controlPoint);
			} catch (\NDH\AccessControl\Security\Exception\NoEntryInPolicyException $e) {
				return self::VOTE_ABSTAIN;
			}
			foreach ($privileges as $privilege) {
				if ($privilege === \NDH\AccessControl\Security\Policy\PolicyService::PRIVILEGE_GRANT) {
					$accessGrants++;
				} elseif ($privilege === \NDH\AccessControl\Security\Policy\PolicyService::PRIVILEGE_DENY) {
					$accessDenies++;
				}
			}
		}
		if ($accessDenies > 0) {
			return self::VOTE_DENY;
		}
		if ($accessGrants > 0) {
			return self::VOTE_GRANT;
		}

		return self::VOTE_ABSTAIN;
	}

	/**
	 * This is the default Policy voter, it votes for the access privilege for the given resource
	 *
	 * @param \NDH\AccessControl\Security\ContextInterface $securityContext The current security context
	 * @param string $resource The resource to vote for
	 * @return integer One of: VOTE_GRANT, VOTE_ABSTAIN, VOTE_DENY
	 */
	public function voteForResource(\NDH\AccessControl\Security\ContextInterface $securityContext, $resource) {
		$accessGrants = 0;
		$accessDenies = 0;
		foreach ($securityContext->getRoles() as $role) {
			try {
				$privilege = $this->policyService->getPrivilegeForResource($role, $resource);
			} catch (\NDH\AccessControl\Security\Exception\NoEntryInPolicyException $e) {
				return self::VOTE_ABSTAIN;
			}

			if ($privilege === NULL) {
				continue;
			}

			if ($privilege === \NDH\AccessControl\Security\Policy\PolicyService::PRIVILEGE_GRANT) {
				$accessGrants++;
			} elseif ($privilege === \NDH\AccessControl\Security\Policy\PolicyService::PRIVILEGE_DENY) {
				$accessDenies++;
			}
		}

		if ($accessDenies > 0) {
			return self::VOTE_DENY;
		}
		if ($accessGrants > 0) {
			return self::VOTE_GRANT;
		}

		return self::VOTE_ABSTAIN;
	}
}

?>