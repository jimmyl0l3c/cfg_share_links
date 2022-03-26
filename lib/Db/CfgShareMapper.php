<?php

namespace OCA\CfgShareLinks\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\DB\Exception;
use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;
use Psr\Log\LoggerInterface;

class CfgShareMapper extends QBMapper {
	/** @var LoggerInterface */
	private $logger;

	public function __construct(IDBConnection $db, LoggerInterface $logger) {
		parent::__construct($db, 'cfg_shares', CfgShare::class);
		$this->logger = $logger;
	}

	public function insert(Entity $entity): Entity {
		$qb = $this->db->getQueryBuilder();
		$qb->insert($this->getTableName())
			->values(
				array(
					'full_id' => "'" . $entity->getFullId() . "'",
					'token' => "'" . $entity->getToken() . "'",
					'node_id' => $entity->getNodeId()
				)
			);

		$this->logger->debug($qb->getSQL());

		$qb->executeStatement();
		return $entity;
	}

	/**
	 * @throws DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 * @throws Exception
	 */
	public function find(int $id) {
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
	public function findByFullId(string $fullId) {
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
	public function findByToken(string $token) {
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
