<?php

namespace OCA\CfgShareLinks\Controller;

use OCA\CfgShareLinks\AppInfo\Application;
use OCA\CfgShareLinks\Service\ShareManager;
use OCA\CfgShareLinks\Service\ShareService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class ShareController extends Controller
{
    /** @var ShareService */
    private $service;

    /** @var string */
    private $userId;

//    /** @var ShareManager */
//    private $shareManager;

    use Errors;

    public function __construct(
        IRequest $request,
        ShareService $service,
        $userId
//        ShareManager $ShareManager
    ) {
        parent::__construct(Application::APP_ID, $request);
        $this->service = $service;
        $this->userId = $userId;

//        $this->shareManager = $ShareManager;
//        $this->service->setShareManager($this->shareManager);
    }

    /**
     * @NoAdminRequired
     */
    public function create(string $path, int $shareType, string $tokenCandidate): DataResponse {
        return new DataResponse($this->service->create($path, $shareType, $tokenCandidate,
            $this->userId));
    }

    /**
     * @NoAdminRequired
     */
    public function update(string $id,
                           string $tokenCandidate): DataResponse {
        return $this->handleNotFound(function () use ($id, $tokenCandidate) {
            return $this->service->update($id, $tokenCandidate, $this->userId);
        });
    }
}