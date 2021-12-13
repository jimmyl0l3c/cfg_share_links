<?php

namespace OCA\CfgShareLinks\Service;

use OCA\CfgShareLinks\Db\CustomShare;
use OCA\CfgShareLinks\Db\ShareMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\DB\Exception;

class CfgShareService
{
    /** @var ShareMapper */
    private $mapper;

    public function __construct(ShareMapper $mapper) {
        $this->mapper = $mapper;
    }

    /**
     * @param $shareFullId
     * @param $token
     * @return mixed|CustomShare|Entity
     * @throws Exception
     */
    public function create($shareFullId, $token) {
        $share = new CustomShare();
        $share->setShareFullId($shareFullId);
        $share->setToken($token);
        return $this->mapper->insert($share);
    }

    /**
     * @param $shareFullId
     * @param $token
     * @return mixed|CustomShare|Entity
     * @throws DoesNotExistException
     * @throws Exception
     * @throws MultipleObjectsReturnedException
     */
    public function updateByShareFullId($shareFullId, $token) {
        $share = $this->mapper->getByShareFullId($shareFullId);
        $share->setToken($token);
        return $this->mapper->update($share);
    }

    /**
     * @param $id
     * @return CustomShare|Entity
     * @throws DoesNotExistException
     * @throws Exception
     * @throws MultipleObjectsReturnedException
     */
    public function delete($id) {
        $share = $this->mapper->getById($id);
        $this->mapper->delete($share);
        return $share;
    }
}
