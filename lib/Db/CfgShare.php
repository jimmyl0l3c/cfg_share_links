<?php

namespace OCA\CfgShareLinks\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class CfgShare extends Entity implements JsonSerializable {
	protected string $full_id;
	protected string $token;
	protected int $node_id;

	public function __construct() {
		$this->addType('id', 'integer');
		$this->addType('full_id', 'string');
		$this->addType('token', 'string');
		$this->addType('node_id', 'integer');
	}

	public function setFullId($full_id): void {
		$this->full_id = $full_id;
	}

	public function setNodeId($node_id): void {
		$this->node_id = $node_id;
	}

	public function setToken($token): void {
		$this->token = $token;
	}

	public function getFullId(): string {
		return $this->full_id;
	}

	public function getNodeId(): int {
		return $this->node_id;
	}

	public function getToken(): string {
		return $this->token;
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'full_id' => $this->full_id,
			'token' => $this->token,
			'node_id' => $this->node_id
		];
	}
}
