<?php

namespace OCA\CfgShareLinks\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;

/**
 * @template-extends QBMapper<CfgShare>
 */
class CfgShareMapper extends QBMapper {
	public function __construct(
		IDBConnection $db,
		private LoggerInterface $logger,
	) {
		parent::__construct($db, 'cfg_shares', CfgShare::class);
	}

	/**
	 * @throws DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 * @throws Exception
	 */
	public function find(int $id) : CfgShare {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param string $node
	 * @return array|Entity[]
	 * @throws Exception
	 */
	public function findByNode(string $node): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('node_id', $qb->createNamedParameter($node))
			);

		return $this->findEntities($qb);
	}

	/**
	 * @param string $fullId
	 * @return mixed|Entity
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function findByFullId(string $fullId): CfgShare {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('full_id', $qb->createNamedParameter($fullId))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param string $token
	 * @return mixed|Entity
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function findByToken(string $token): CfgShare {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('token', $qb->createNamedParameter($token))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @throws Exception
	 */
	public function findAll(): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName());

		return $this->findEntities($qb);
	}
}
