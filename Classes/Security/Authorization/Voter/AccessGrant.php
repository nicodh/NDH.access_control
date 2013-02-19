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
 * An access decision voter, that always grants access for specific objects.
 *
 * @Flow\Scope("singleton")
 */
class AccessGrant implements \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface {

	/**
	 * Votes to grant access, if the given object is one of the supported types
	 *
	 * @param \NDH\AccessControl\Security\Context $securityContext The current security context
	 * @param \NDH\AccessControl\Security\JoinPointInterface $joinPoint The joinpoint to decide on
	 * @return integer One of: VOTE_GRANT
	 * @throws \NDH\AccessControl\Security\Exception\AccessDeniedException If access is not granted
	 */
	public function voteForJoinPoint(\NDH\AccessControl\Security\Context $securityContext, \NDH\AccessControl\Security\JoinPointInterface $joinPoint) {

	}

	/**
	 * Votes to grant access, if the resource exists
	 *
	 * @param \NDH\AccessControl\Security\Context $securityContext The current security context
	 * @param string $resource The resource to vote for
	 * @return integer One of: VOTE_GRANT, VOTE_ABSTAIN, VOTE_DENY
	 * @throws \NDH\AccessControl\Security\Exception\AccessDeniedException If access is not granted
	 */
	public function voteForResource(\NDH\AccessControl\Security\Context $securityContext, $resource) {

	}
}

?>