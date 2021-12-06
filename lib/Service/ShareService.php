<?php

namespace OCA\CfgShareLinks\Service;

use OC\User\NoUserException;
use OCA\CfgShareLinks\Db\ShareMapper;
use OCP\AppFramework\OCS\OCSBadRequestException;
use OCP\AppFramework\OCS\OCSException;
use OCP\AppFramework\OCS\OCSForbiddenException;
use OCP\AppFramework\OCS\OCSNotFoundException;
use OCP\Constants;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\IL10N;
use OCP\Lock\ILockingProvider;
use OCP\Lock\LockedException;
use OCP\Share\Exceptions\GenericShareException;
use OCP\Share\Exceptions\ShareNotFound;
use OCP\Share\IManager;
use OCP\Share\IShare;

class ShareService
{
    /** @var ShareMapper */
    private $mapper;

    /** @var IManager */
    private $shareManager;
    /** @var IRootFolder */
    private $rootFolder;
    /** @var string */
    private $currentUser;
    /** @var IL10N */
    private $l;
    /** @var Node */
    private $lockedNode;

    public function __construct(
        ShareMapper $mapper,
        IManager $shareManager,
        IRootFolder $rootFolder,
        string $userId = null,
        IL10N $l10n
    ) {
        $this->mapper = $mapper;
        $this->shareManager = $shareManager;
        $this->rootFolder = $rootFolder;
        $this->currentUser = $userId;
        $this->l = $l10n;
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
    public function create(string $path, int $shareType, string $tokenCandidate, string $userId): array
    {
        if ($shareType != IShare::TYPE_LINK) {
            throw new OCSBadRequestException($this->l->t('Unknown share type'));
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

        $share->setToken($tokenCandidate);

        // Update share in db
        $this->shareManager->updateShare($share);

        return $this->serializeShare($share);
    }

    /**
     * @param string $id share FullId
     * @param string $tokenCandidate
     * @param string $userId
     * @return array
     * @throws InvalidTokenException
     * @throws TokenNotUniqueException
     * @throws OCSBadRequestException
     * @throws ShareNotFound
     */
    public function update(string $id, string $tokenCandidate, string $userId): array
    {
        // check token validity
        $this->tokenChecks($tokenCandidate);

        // Get share
        $share = $this->shareManager->getShareById($id);

        if ($share->getShareType() !== IShare::TYPE_LINK) {
            throw new OCSBadRequestException('Invalid share type');
        }

        // TODO: check whether user can edit the share

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
        if (!$this->isTokenValid($tokenCandidate)) {
            throw new InvalidTokenException('Invalid Token');
        }

        // Unique check
        try {
            $this->shareManager->getShareByToken($tokenCandidate);
            throw new TokenNotUniqueException('Token is not unique');
        } catch (ShareNotFound $e) {}
    }

    private function isTokenValid(string $token): bool
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-+';

        if ($token == null || strlen($token) < 1) {
            return false;
        }

        foreach($token as $char)
        {
            if(!in_array($char, (array)$characters))
            {
                return false;
            }
        }
        return true;
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

    private function serializeShare(IShare $share): array
    {
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
