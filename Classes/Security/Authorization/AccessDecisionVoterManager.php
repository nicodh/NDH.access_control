<?php
namespace NDH\AccessControl\Security\Authorization;

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
 * An access decision voter manager
 *
 * @Flow\Scope("singleton")
 */
class AccessDecisionVoterManager implements AccessDecisionManagerInterface {

	/**
	 * The object manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * The current security context
	 * @var \NDH\AccessControl\Security\ContextInterface
	 */
	protected $securityContext;

	/**
	 * Array of \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface objects
	 * @var array
	 */
	protected $accessDecisionVoters = array();

	/**
	 * If set to TRUE access will be granted for objects where all voters abstain from decision.
	 * @var boolean
	 */
	protected $allowAccessIfAllAbstain = FALSE;

	/**
	 * Constructor.
	 *
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager The object manager
	 * @param \NDH\AccessControl\Security\ContextInterface $securityContext The security context
	 */
	public function __construct(\TYPO3\CMS\Extbase\Object\ObjectManager $objectManager, \NDH\AccessControl\Security\ContextInterface $securityContext) {
		$this->objectManager = $objectManager;
		$this->securityContext = $securityContext;
		$this->createAccessDecisionVoters(array('NDH\\AccessControl\\Security\\Authorization\\Voter\\Policy')); // TODO: get from settings
	}

	/**
	 * Injects the configuration settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->createAccessDecisionVoters(array('NDH\\AccessControl\\Security\\Authorization\\Voter\\Policy')); // TODO: get from settings
		//$this->allowAccessIfAllAbstain = $settings['security']['authorization']['allowAccessIfAllVotersAbstain'];
	}

	/**
	 * Returns the configured access decision voters
	 *
	 * @return array Array of \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface objects
	 */
	public function getAccessDecisionVoters() {
		return $this->accessDecisionVoters;
	}

	/**
	 * Decides if access should be granted on the given object in the current security context.
	 * It iterates over all available \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface objects.
	 * If all voters abstain, access will be denied by default, except $allowAccessIfAllAbstain is set to TRUE.
	 *
	 * @param \NDH\AccessControl\Security\ControlPointInterface $controlPoint The controlpoint to decide on
	 * @return void
	 * @throws \NDH\AccessControl\Security\Exception\AccessDeniedException If access is not granted
	 */
	public function decideOnControlPoint(\NDH\AccessControl\Security\ControlPointInterface $controlPoint) {
		$denyVotes = 0;
		$grantVotes = 0;
		$abstainVotes = 0;
		if(count($this->accessDecisionVoters) < 1) {
			die('No voter!');
		}
		foreach ($this->accessDecisionVoters as $voter) {
			$vote = $voter->voteForControlPoint($this->securityContext, $controlPoint);
			switch ($vote) {
				case \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface::VOTE_DENY:
					$denyVotes++;
					break;
				case \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface::VOTE_GRANT:
					$grantVotes++;
					break;
				case \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface::VOTE_ABSTAIN:
					$abstainVotes++;
					break;
			}
		}

		if ($denyVotes === 0 && $grantVotes > 0) {
			return;
		}
		if ($denyVotes === 0 && $grantVotes === 0 && $abstainVotes > 0 && $this->allowAccessIfAllAbstain === TRUE) {
			return;
		}

		$votes = sprintf('(%d denied, %d granted, %d abstained)', $denyVotes, $grantVotes, $abstainVotes);
		throw new \NDH\AccessControl\Security\Exception\AccessDeniedException('Access denied ' . $votes, 1222268609);
	}

	/**
	 * Decides if access should be granted on the given resource in the current security context.
	 * It iterates over all available \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface objects.
	 * If all voters abstain, access will be denied by default, except $allowAccessIfAllAbstain is set to TRUE.
	 *
	 * @param string $resource The resource to decide on
	 * @return void
	 * @throws \NDH\AccessControl\Security\Exception\AccessDeniedException If access is not granted
	 */
	public function decideOnResource($resource) {
		$denyVotes = 0;
		$grantVotes = 0;
		$abstainVotes = 0;

		foreach ($this->accessDecisionVoters as $voter) {
			$vote = $voter->voteForResource($this->securityContext, $resource);
			switch ($vote) {
				case \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface::VOTE_DENY:
					$denyVotes++;
					break;
				case \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface::VOTE_GRANT:
					$grantVotes++;
					break;
				case \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface::VOTE_ABSTAIN:
					$abstainVotes++;
					break;
			}
		}

		if ($denyVotes === 0 && $grantVotes > 0) {
			return;
		}
		if ($denyVotes === 0 && $grantVotes === 0 && $abstainVotes > 0 && $this->allowAccessIfAllAbstain === TRUE) {
			return;
		}

		$votes = sprintf('(%d denied, %d granted, %d abstained)', $denyVotes, $grantVotes, $abstainVotes);
		throw new \NDH\AccessControl\Security\Exception\AccessDeniedException('Access denied ' . $votes, 1283175927);
	}

	/**
	 * Creates and sets the configured access decision voters
	 *
	 * @param array $voterClassNames Array of access decision voter class names
	 * @return void
	 * @throws \NDH\AccessControl\Security\Exception\VoterNotFoundException
	 */
	protected function createAccessDecisionVoters(array $voterClassNames) {
		foreach ($voterClassNames as $voterClassName) {
			if (!$this->objectManager->isRegistered($voterClassName)) throw new \NDH\AccessControl\Security\Exception\VoterNotFoundException('No voter of type ' . $voterClassName . ' found!', 1222267934);

			$voter = $this->objectManager->get($voterClassName);
			if (!($voter instanceof \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface)) throw new \NDH\AccessControl\Security\Exception\VoterNotFoundException('The found voter class did not implement \NDH\AccessControl\Security\Authorization\AccessDecisionVoterInterface', 1222268008);

			$this->accessDecisionVoters[] = $voter;
		}
	}
}

?>
