<?php

namespace OCA\CfgShareLinks\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class ShareMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'cfg_shares', CustomShare::class);
	}

	/**
	 * @param int $id
	 * @return Entity|CustomShare
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getById(int $id): CustomShare {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('cfg_shares')
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
		return $this->findEntity($qb);
	}

	/**
	 * @param string $id
	 * @return Entity|CustomShare
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getByShareFullId(string $id): CustomShare {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('cfg_shares')
			->where($qb->expr()->eq('share_full_id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_STR)));
		return $this->findEntity($qb);
	}
}
