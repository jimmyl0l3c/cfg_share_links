<?php

namespace OCA\CfgShareLinks\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;
use OCP\DB\Types;

class CfgShare extends Entity implements JsonSerializable {
	protected string $fullId;
	protected string $token;
	protected int $nodeId;

	public function __construct() {
		$this->addType('id', Types::INTEGER);
		$this->addType('fullId', Types::STRING);
		$this->addType('token', Types::STRING);
		$this->addType('nodeId', Types::INTEGER);
	}

	public function setFullId(string $fullId): void {
		$this->fullId = $fullId;
	}

	public function setNodeId(int $nodeId): void {
		$this->nodeId = $nodeId;
	}

	public function setToken(string $token): void {
		$this->token = $token;
	}

	public function getFullId(): string {
		return $this->fullId;
	}

	public function getNodeId(): int {
		return $this->nodeId;
	}

	public function getToken(): string {
		return $this->token;
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'fullId' => $this->fullId,
			'token' => $this->token,
			'nodeId' => $this->nodeId
		];
	}
}
