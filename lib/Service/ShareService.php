<?php

/**
 * Configurable Share Links for Nextcloud
 *
 * @copyright Copyright (C) 2022  Filip Joska <filip@joska.dev>
 *
 * @author Filip Joska <filip@joska.dev>
 *
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\CfgShareLinks\Service;

use Exception as BaseException;
use OC\User\NoUserException;
use OCA\CfgShareLinks\AppInfo\AppConstants;
use OCA\CfgShareLinks\Db\CfgShare;
use OCA\CfgShareLinks\Db\CfgShareMapper;
use OCA\CfgShareLinks\Enums\LinkLabelMode;
use OCA\CfgShareLinks\Enums\SettingsKey;
use OCA\Files_Sharing\Exceptions\SharingRightsException;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\OCS\OCSBadRequestException;
use OCP\AppFramework\OCS\OCSException;
use OCP\AppFramework\OCS\OCSForbiddenException;
use OCP\AppFramework\OCS\OCSNotFoundException;
use OCP\AppFramework\Services\IAppConfig;
use OCP\Constants;
use OCP\DB\Exception;
use OCP\Files\InvalidPathException;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\Lock\ILockingProvider;
use OCP\Lock\LockedException;
use OCP\Share\Exceptions\GenericShareException;
use OCP\Share\Exceptions\ShareNotFound;
use OCP\Share\IManager;
use OCP\Share\IShare;
use Psr\Log\LoggerInterface;
use TypeError as BaseTypeError;

/***
 * Based on ShareAPIController (From Nextcloud's core app files_sharing)
 */
class ShareService {
	/** @var Node */
	private Node $lockedNode;
   
	public function __construct(
		private readonly LoggerInterface $logger,
		private readonly IManager        $shareManager,
		private readonly IGroupManager   $groupManager,
		private readonly IRootFolder     $rootFolder,
		private readonly IL10N           $l10n,
		private readonly IAppConfig      $appConfig,
		private readonly CfgShareMapper  $mapper,
		private readonly AppConstants    $appConstants,
		private string|null              $currentUserId = null
	) {
	}

	/**
	 * @throws OCSNotFoundException
	 * @throws OCSException
	 * @throws OCSBadRequestException
	 * @throws OCSForbiddenException
	 * @throws NoUserException
	 * @throws InvalidTokenException
	 * @throws TokenNotUniqueException
	 * @throws NotPermittedException
	 * @throws InvalidPathException
	 * @throws NotFoundException
	 */
	public function create(?string $path, int $shareType, string $tokenCandidate, ?string $userId, string $password = ''): array {
		if ($userId != null && $this->currentUserId != $userId) {
			$this->currentUserId = $userId;
		}

		if ($shareType != IShare::TYPE_LINK) {
			// TRANSLATORS function to create link with custom share token is expecting type link (but received some other type)
			throw new OCSBadRequestException($this->l10n->t('Invalid share type'));
		}

		// Can we even share links?
		if (!$this->shareManager->shareApiAllowLinks()) {
			throw new OCSNotFoundException($this->l10n->t('Public link sharing is disabled by the administrator'));
		}

		// Verify path
		if ($path === null) {
			// TRANSLATORS function to create link received empty (null) path
			throw new OCSNotFoundException($this->l10n->t('Please specify a file or folder path'));
		}

		$userFolder = $this->rootFolder->getUserFolder($this->currentUserId);
		try {
			$node = $userFolder->get($path);
		} catch (NotFoundException) {
			throw new OCSNotFoundException($this->l10n->t('Wrong path, file/folder does not exist'));
		}

		// check token validity
		$this->tokenChecks($tokenCandidate);

		$share = $this->shareManager->newShare();

		$share->setNode($node);

		try {
			$this->lock($share->getNode());
		} catch (NotFoundException|LockedException) {
			throw new OCSNotFoundException($this->l10n->t('Could not create share'));
		}

		$permissions = Constants::PERMISSION_READ;
		// TODO: It might make sense to have a dedicated setting to allow/deny converting link shares into federated ones
		if (($permissions & Constants::PERMISSION_READ) && $this->shareManager->outgoingServer2ServerSharesAllowed()) {
			$permissions |= Constants::PERMISSION_SHARE;
		}
		$share->setPermissions($permissions);

		$share->setShareType($shareType);
		$share->setSharedBy($this->currentUserId);

		// Set password
		if ($password !== '') {
			$share->setPassword($password);
		}

		// Create share in the database
		try {
			$share = $this->shareManager->createShare($share);
		} catch (GenericShareException $e) {
			$this->logger->warning('Error creating share: ' . $e->getMessage(), ['trace' => $e->getTrace()]);
			$code = $e->getCode() === 0 ? 403 : $e->getCode();
			throw new OCSException($e->getHint(), $code);
		} catch (BaseException $e) {
			$this->logger->warning('Error creating share: ' . $e->getMessage(), ['trace' => $e->getTrace()]);
			throw new OCSForbiddenException($e->getMessage(), $e);
		}

		// Set label
		$labelMode = LinkLabelMode::from(
			$this->appConfig->getAppValueInt(SettingsKey::DefaultLabelMode->value, $this->appConstants::DEFAULT_LABEL_MODE)
		);
		switch ($labelMode) {
			case LinkLabelMode::SameAsToken:
				$share->setLabel($tokenCandidate);
				break;
			case LinkLabelMode::UserSpecified:
				$share->setLabel($this->appConfig->getAppValueString(SettingsKey::DefaultCustomLabel->value, $this->appConstants::DEFAULT_CUSTOM_LABEL));
				break;
			case LinkLabelMode::NoLabel:
				break; // Do nothing
		}

		// Set custom token
		$share->setToken($tokenCandidate);

		// Update share in db
		$this->shareManager->updateShare($share);

		// Add item to cfg_shares table
		$c_share = new CfgShare();
		$c_share->setFullId($share->getFullId());
		$c_share->setToken($share->getToken());
		$c_share->setNodeId($share->getNode()->getId());
		try {
			$this->mapper->insert($c_share);
		} catch (Exception $e) {
			$this->logger->error('Unable to add cfg_share to db. Share will not be deleted when file is deleted. 
			(' . $e->getMessage() . ' - ' . $e->getTraceAsString() . ')');
		}

		return $this->serializeShare($share);
	}

	/**
	 * @param string $id
	 * @param string $tokenCandidate
	 * @param string $userId
	 * @return array
	 * @throws InvalidTokenException
	 * @throws OCSBadRequestException
	 * @throws ShareNotFound
	 * @throws TokenNotUniqueException
	 * @throws SharingRightsException
	 * @throws OCSNotFoundException
	 */
	public function updateById(string $id, string $tokenCandidate, string $userId): array {
		if ($userId != null && $this->currentUserId != $userId) {
			$this->currentUserId = $userId;
			$this->logger->debug('currentUser updated');
		}

		// check token validity
		$this->tokenChecks($tokenCandidate);

		// Get share
		$share = $this->shareManager->getShareById($id);

		return $this->update($share, $tokenCandidate);
	}

	/**
	 * @param string $currentToken
	 * @param string $tokenCandidate
	 * @param string $userId
	 * @return array
	 * @throws InvalidTokenException
	 * @throws OCSBadRequestException
	 * @throws ShareNotFound
	 * @throws TokenNotUniqueException
	 * @throws SharingRightsException
	 * @throws OCSNotFoundException
	 */
	public function updateByToken(string $currentToken, string $tokenCandidate, string $userId): array {
		if ($userId != null && $this->currentUserId != $userId) {
			$this->currentUserId = $userId;
			$this->logger->debug('currentUser updated');
		}

		// check token validity
		$this->tokenChecks($tokenCandidate);

		// Get share
		$share = $this->shareManager->getShareByToken($currentToken);

		return $this->update($share, $tokenCandidate);
	}

	/**
	 * @param IShare $share
	 * @param string $newToken
	 * @return array
	 * @throws InvalidTokenException
	 * @throws OCSBadRequestException
	 * @throws OCSNotFoundException
	 * @throws ShareNotFound
	 * @throws SharingRightsException
	 * @throws TokenNotUniqueException
	 */
	private function update(IShare $share, string $newToken): array {
		$currentToken = $share->getToken();

		try {
			$this->lock($share->getNode());
		} catch (NotFoundException|LockedException) {
			throw new OCSNotFoundException($this->l10n->t('Could not create share'));
		}

		if ($share->getShareType() !== IShare::TYPE_LINK) {
			// TRANSLATORS function to update share token is expecting type link, but received some other type
			throw new OCSBadRequestException($this->l10n->t('Invalid share type'));
		}

		// Check whether user can reshare
		try {
			if (!$this->hasResharingRight($share)) {
				$this->logger->warning('Insufficient permission');
				// TRANSLATORS user tried to change token, but he does not have reshare permission
				throw new SharingRightsException($this->l10n->t('Insufficient permission'));
			}
		} catch (NotFoundException) {
			$this->logger->warning('Unable to check permissions');
			// TRANSLATORS error occurred while checking reshare permission (when updating token)
			throw new SharingRightsException($this->l10n->t('Unable to check permissions'));
		}

		// Update label
		$labelMode = LinkLabelMode::from($this->appConfig->getAppValueInt(SettingsKey::DefaultLabelMode->value, $this->appConstants::DEFAULT_LABEL_MODE));
		if ($labelMode == LinkLabelMode::SameAsToken && ($share->getLabel() == null || strlen($share->getLabel()) == 0 || $share->getToken() == $share->getLabel())) {
			$share->setLabel($newToken);
		}

		// Update token
		$share->setToken($newToken);

		// Update share in db
		$this->shareManager->updateShare($share);

		// Update/add item to cfg_shares table
		try {
			$c_share = $this->mapper->findByToken($currentToken);
			$c_share->setToken($share->getToken());
			$this->mapper->update($c_share);
		} catch (Exception|DoesNotExistException) {
			$this->logger->debug('Cfg_share missing in db, attempting to add.');
			$c_share = new CfgShare();
			$c_share->setFullId($share->getFullId());
			$c_share->setToken($share->getToken());
			try {
				$c_share->setNodeId($share->getNode()->getId());
				$this->mapper->insert($c_share);
			} catch (Exception|NotFoundException|InvalidPathException) {
				$this->logger->error('Unable to add cfg_share to db. Share will not be deleted when file is deleted.');
			}
		} catch (MultipleObjectsReturnedException) {
			$this->logger->error('Db error - multiple objects returned.');
			// TODO: resolve conflict
		}

		return $this->serializeShare($share);
	}

	/**
	 * @throws NotFoundException
	 */
	private function hasResharingRight(IShare $qShare): bool {
		//        if (is_iterable($shares)) {
		//            $this->logger->debug('hasResharingRight: notIterable');
		//            return false;
		//        }
		try {
			$shares = $this->shareManager->getAllShares();
			foreach ($shares as $share) {
				if ($share->getNodeId() !== $qShare->getNodeId()) {
					$this->logger->debug('hasResharingRight: badShare');
					continue;
				}

				if ($this->canReshare($share)) {
					return true;
				}
				$this->logger->debug(sprintf('hasResharingRight: resharePermCheck = false (%s)', $share->getId()));
			}
		} catch (BaseException|BaseTypeError $exception) {
			$this->logger->debug(sprintf('hasResharingRight: sharesIterable exception (%s)', $exception->getMessage()));
		}
		return false;
	}

	private function canReshare(IShare $share): bool {
		if ($share->getShareOwner() === $this->currentUserId) {
			$this->logger->debug(sprintf('canReshare: shareOwnerCheck true (%s)', $share->getId()));
			return true;
		}

		if ((Constants::PERMISSION_SHARE & $share->getPermissions()) === 0) {
			$this->logger->debug(sprintf('canReshare: sharePermission false (%s)', $share->getId()));
			return false;
		}

		//        try {
		//            $node = $share->getNode();
		//            if ($node !== null && ($node->getPermissions() & Constants::PERMISSION_SHARE) !== 0) {
		//                $this->logger->debug(sprintf('canReshare: nodePermission true (%s)', $share->getId()));
		//                return true;
		//            }
		//        } catch (NotFoundException|InvalidPathException $e) {}

		if ($share->getShareType() === IShare::TYPE_USER && $share->getSharedWith() === $this->currentUserId) {
			$this->logger->debug(sprintf('canReshare: userCheck true (%s)', $share->getId()));
			return true;
		}

		if ($share->getShareType() === IShare::TYPE_GROUP && $this->groupManager->isInGroup($this->currentUserId, $share->getSharedWith())) {
			$this->logger->debug(sprintf('canReshare: groupCheck true (%s)', $share->getId()));
			return true;
		}

		$this->logger->debug(sprintf('canReshare: false (%s)', $share->getId()));
		return false;
	}

	/**
	 * @throws InvalidTokenException
	 * @throws TokenNotUniqueException
	 */
	private function tokenChecks(string $tokenCandidate): void {
		// Validity check
		$this->raiseIfTokenIsInvalid($tokenCandidate);

		// Unique check
		try {
			$share = $this->shareManager->getShareByToken($tokenCandidate);
			if ($this->appConfig->getAppValueBool(SettingsKey::DeleteRemovedShareConflicts->value, $this->appConstants::DEFAULT_DELETE_REMOVED_SHARE_CONFLICTS)) {
				try {
					$share->getNode();
				} catch (NotFoundException) {
					// Remove share if the file/folder does not exist
					$this->logger->debug('Conflicting token, but node does not exist. Removing conflicting share.');
					$this->shareManager->deleteShare($share);
					return;
				}
			}
			throw new TokenNotUniqueException($this->l10n->t('Token is not unique'));
		} catch (ShareNotFound) {
		}
	}

	/**
	 * @throws InvalidTokenException
	 */
	public function raiseIfTokenIsInvalid(string $token): void {
		$min_length = $this->appConfig->getAppValueInt(SettingsKey::MinTokenLength->value, $this->appConstants::DEFAULT_MIN_TOKEN_LENGTH);

		if ($token == null || strlen($token) < $min_length) {
			throw new InvalidTokenException($this->l10n->t('Token is not long enough'));
		}

		if (strlen($token) > $this->appConstants::MAX_TOKEN_LENGTH) {
			throw new InvalidTokenException($this->l10n->t('Token cannot be longer than %1$s characters', [$this->appConstants::MAX_TOKEN_LENGTH]));
		}

		$valid = preg_match($this->appConstants::DEFAULT_VALID_TOKEN_REGEX, $token);

		if ($valid != 1) {
			throw new InvalidTokenException($this->l10n->t('Token contains invalid characters'));
		}
	}

	/**
	 * Lock a Node
	 *
	 * @param Node $node
	 * @throws LockedException
	 */
	private function lock(Node $node): void {
		$node->lock(ILockingProvider::LOCK_SHARED);
		$this->lockedNode = $node;
	}

	/**
	 * Cleanup the remaining locks
	 * @throws LockedException
	 */
	public function cleanup(): void {
		if (!empty($this->lockedNode)) {
			$this->lockedNode->unlock(ILockingProvider::LOCK_SHARED);
		}
	}

	private function serializeShare(IShare $share): array {
		return [
			'id' => $share->getId(),
			'share_type' => $share->getShareType(),
			'uid_owner' => $share->getSharedBy(),
			'displayname_owner' => $share->getSharedBy(),
			// recipient permissions
			'permissions' => $share->getPermissions(),
			// current user permissions on this share
			'stime' => $share->getShareTime()->getTimestamp(),
			'parent' => null,
			'expiration' => null,
			'token' => $share->getToken(),
			'uid_file_owner' => $share->getShareOwner(),
			'note' => $share->getNote(),
			'label' => $share->getLabel(),
			'displayname_file_owner' => $share->getShareOwner(),
		];
	}
}
