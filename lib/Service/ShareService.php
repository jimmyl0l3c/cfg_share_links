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

use OC\User\NoUserException;
use OCA\CfgShareLinks\AppInfo\Application;
use OCP\AppFramework\OCS\OCSBadRequestException;
use OCP\AppFramework\OCS\OCSException;
use OCP\AppFramework\OCS\OCSForbiddenException;
use OCP\AppFramework\OCS\OCSNotFoundException;
use OCP\Constants;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Lock\ILockingProvider;
use OCP\Lock\LockedException;
use OCP\Share\Exceptions\GenericShareException;
use OCP\Share\Exceptions\ShareNotFound;
use OCP\Share\IManager;
use OCP\Share\IShare;

/***
 * Based on ShareAPIController (From Nextcloud's core app files_sharing)
 */
class ShareService {
	/** @var IConfig */
	private $config;
	/** @var IManager */
	private $shareManager;
	/** @var IRootFolder */
	private $rootFolder;
	/** @var IL10N */
	private $l;
	/** @var Node */
	private $lockedNode;
	/** @var string */
	private $currentUser;

	public function __construct(
		IManager $shareManager,
		IRootFolder $rootFolder,
		string $userId = null,
		IL10N $l10n,
		IConfig $config
	) {
		$this->shareManager = $shareManager;
		$this->rootFolder = $rootFolder;
		$this->currentUser = $userId;
		$this->l = $l10n;
		$this->config = $config;
	}

	/**
	 * @throws NotFoundException
	 * @throws OCSNotFoundException
	 * @throws OCSException
	 * @throws OCSBadRequestException
	 * @throws OCSForbiddenException
	 * @throws NoUserException
	 * @throws InvalidTokenException
	 * @throws TokenNotUniqueException
	 * @throws NotPermittedException
	 */
	public function create(string $path, int $shareType, string $tokenCandidate, string $userId): array {
		if ($userId != null && $this->currentUser != $userId) {
			$this->currentUser = $userId;
		}

		if ($shareType != IShare::TYPE_LINK) {
			// TRANSLATORS function to create link with custom share token is expecting type link (but received some other type)
			throw new OCSBadRequestException($this->l->t('Invalid share type'));
		}

		// Can we even share links?
		if (!$this->shareManager->shareApiAllowLinks()) {
			throw new OCSNotFoundException($this->l->t('Public link sharing is disabled by the administrator'));
		}

		// Verify path
		if ($path === null) {
			throw new OCSNotFoundException($this->l->t('Please specify a file or folder path'));
		}

		$userFolder = $this->rootFolder->getUserFolder($this->currentUser);
		try {
			$path = $userFolder->get($path);
		} catch (NotFoundException $e) {
			throw new OCSNotFoundException($this->l->t('Wrong path, file/folder doesn\'t exist'));
		}

		// check token validity
		$this->tokenChecks($tokenCandidate);

		$share = $this->shareManager->newShare();

		$share->setNode($path);

		try {
			$this->lock($share->getNode());
		} catch (LockedException $e) {
			throw new OCSNotFoundException($this->l->t('Could not create share'));
		}

		$permissions = Constants::PERMISSION_READ;
		// TODO: It might make sense to have a dedicated setting to allow/deny converting link shares into federated ones
		if (($permissions & Constants::PERMISSION_READ) && $this->shareManager->outgoingServer2ServerSharesAllowed()) {
			$permissions |= Constants::PERMISSION_SHARE;
		}
		$share->setPermissions($permissions);

		$share->setShareType($shareType);
		$share->setSharedBy($this->currentUser);

		// Create share in the database
		try {
			$share = $this->shareManager->createShare($share);
		} catch (GenericShareException $e) {
			\OC::$server->getLogger()->logException($e);
			$code = $e->getCode() === 0 ? 403 : $e->getCode();
			throw new OCSException($e->getHint(), $code);
		} catch (\Exception $e) {
			\OC::$server->getLogger()->logException($e);
			throw new OCSForbiddenException($e->getMessage(), $e);
		}

		// Set label
		$labelMode = $this->config->getAppValue(Application::APP_ID, 'default_label_mode', 0);
		switch ($labelMode) {
			case 1:
				$share->setLabel($tokenCandidate);
				break;
			case 2:
				$share->setLabel($this->config->getAppValue(Application::APP_ID, 'default_label', 'Custom link'));
				break;
		}

		// Set custom token
		$share->setToken($tokenCandidate);

		// Update share in db
		$this->shareManager->updateShare($share);

		return $this->serializeShare($share);
	}

	/**
	 * @param string $id
	 * @param string $path
	 * @param string $currentToken
	 * @param string $tokenCandidate
	 * @param string $userId
	 * @return array
	 * @throws InvalidTokenException
	 * @throws OCSBadRequestException
	 * @throws ShareNotFound
	 * @throws TokenNotUniqueException
	 */
	public function update(string $id, string $path, string $currentToken, string $tokenCandidate, string $userId): array {
		if ($userId != null && $this->currentUser != $userId) {
			$this->currentUser = $userId;
		}

		// check token validity
		$this->tokenChecks($tokenCandidate);

		// Get share
		$share = $this->shareManager->getShareByToken($currentToken);
		if ($share->getId() != $id) {
			throw new ShareNotFound($this->l->t('Share not found'));
		}
		//        $share = $this->shareManager->getShareById($id);

		if ($share->getShareType() !== IShare::TYPE_LINK) {
			// TRANSLATORS function to update share token is expecting type link (but received some other type)
			throw new OCSBadRequestException($this->l->t('Invalid share type'));
		}

		// TODO: check whether user can edit the share

		// Update label
		$labelMode = $this->config->getAppValue(Application::APP_ID, 'default_label_mode', 0);
		if ($labelMode == 1 && ($share->getLabel() == null || strlen($share->getLabel()) == 0 || $share->getToken() == $share->getLabel())) {
			$share->setLabel($tokenCandidate);
		}

		// Update token
		$share->setToken($tokenCandidate);

		// Update share in db
		$this->shareManager->updateShare($share);

		return $this->serializeShare($share);
	}

	/**
	 * @throws InvalidTokenException
	 * @throws TokenNotUniqueException
	 */
	private function tokenChecks(string $tokenCandidate) {
		// Validity check
		$this->checkTokenValidity($tokenCandidate);

		// Unique check
		try {
			$this->shareManager->getShareByToken($tokenCandidate);
			throw new TokenNotUniqueException($this->l->t('Token is not unique'));
		} catch (ShareNotFound $e) {
		}
	}

	/**
	 * @throws InvalidTokenException
	 */
	private function checkTokenValidity(string $token) // TODO: use regular expression
	{
		$char_array = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-+');
		$min_length = $this->config->getAppValue(Application::APP_ID, 'min_token_length', 3);

		if ($token == null || strlen($token) < $min_length) {
			throw new InvalidTokenException($this->l->t('Token is not long enough'));
		}

		$token_arr = str_split($token);
		foreach ($token_arr as $char) {
			if (!in_array($char, $char_array)) {
				throw new InvalidTokenException($this->l->t('Token contains invalid characters'));
			}
		}
	}

	/**
	 * Lock a Node
	 *
	 * @param Node $node
	 * @throws LockedException
	 */
	private function lock(Node $node) {
		$node->lock(ILockingProvider::LOCK_SHARED);
		$this->lockedNode = $node;
	}

	/**
	 * Cleanup the remaining locks
	 * @throws LockedException
	 */
	public function cleanup() {
		if ($this->lockedNode !== null) {
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
