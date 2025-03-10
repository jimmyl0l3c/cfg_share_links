<?php

namespace OCA\CfgShareLinks\Controller;

use OCA\CfgShareLinks\Service\ShareService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\Lock\LockedException;

class ShareController extends Controller {
	use Errors;

	public function __construct(
		string $appName,
		IRequest $request,
		private readonly ShareService $service,
		private readonly ?string $userId,
	) {
		parent::__construct($appName, $request);
	}

	#[NoAdminRequired]
	public function create(string $path, int $shareType, string $tokenCandidate, string $password = ''): DataResponse {
		return $this->handleException(function () use ($path, $shareType, $tokenCandidate, $password) {
			return $this->service->create($path, $shareType, $tokenCandidate, $this->userId, $password);
		});
	}

	#[NoAdminRequired]
	public function updateById(string $id, string $tokenCandidate): DataResponse {
		return $this->handleException(function () use ($id, $tokenCandidate) {
			return $this->service->updateById($id, $tokenCandidate, $this->userId);
		});
	}

	#[NoAdminRequired]
	public function updateByToken(string $currentToken, string $tokenCandidate): DataResponse {
		return $this->handleException(function () use ($currentToken, $tokenCandidate) {
			return $this->service->updateByToken($currentToken, $tokenCandidate, $this->userId);
		});
	}

	/**
	 * @throws LockedException
	 */
	public function cleanup(): void {
		$this->service->cleanup();
	}
}
