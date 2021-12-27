<?php

namespace OCA\CfgShareLinks\Db;

use OCP\AppFramework\Db\Entity;

class CustomShare extends Entity {
	protected $shareFullId;
	protected $token;

	public function __construct() {
		$this->addType('id','integer');
	}

	public function getShareId(): string {
		return explode(':', $this->shareFullId)[1];
	}

	public function setShareFullId($shareFullId) {
		$this->shareFullId = $shareFullId;
	}

	public function setToken($token) {
		$this->token = $token;
	}
}
