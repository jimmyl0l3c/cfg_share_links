<?php

namespace OCA\CfgShareLinks\Controller;

use OCA\CfgShareLinks\Service\ShareService;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\ApiRoute;
use OCP\AppFramework\Http\Attribute\CORS;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\Attribute\OpenAPI;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCS\OCSBadRequestException;
use OCP\AppFramework\OCS\OCSException;
use OCP\AppFramework\OCS\OCSNotFoundException;
use OCP\AppFramework\OCSController;
use OCP\IRequest;
use ResponseDefinitions;

/**
 * @psalm-import-type CfgShareLinks_Share from ResponseDefinitions
 */
class ShareOcsController extends OCSController {
	use Errors;

	public function __construct(
		string                        $appName,
		IRequest                      $request,
		private readonly ShareService $service,
		private readonly ?string      $userId
	) {
		parent::__construct($appName, $request, 'PUT, POST, OPTIONS');
	}

	/**
	 * Create new share with custom token, currently only supports sharing links
	 *
	 * @param int $shareTypeId Share type id (Link=3, currently only one supported)
	 * @return DataResponse<Http::STATUS_OK, CfgShareLinks_Share, array{}> Detail of the newly created share
	 * @throws OCSBadRequestException if the token or password is invalid
	 * @throws OCSException if unauthorized or unexpected exception occurred (differentiated by the response code)
	 * @throws OCSNotFoundException if the path was not found
	 *
	 * 200: Share with custom token created
	 */
	#[NoAdminRequired]
	#[NoCSRFRequired]
	#[CORS]
	#[ApiRoute(verb: 'POST', url: '/api/v1/share/{shareTypeId}')]
	#[OpenAPI(scope: OpenAPI::SCOPE_DEFAULT, tags: ['API-Share'])]
	public function create(int $shareTypeId): DataResponse {
		[
			'path' => $path, 'tokenCandidate' => $tokenCandidate, 'password' => $password,
		] = $this->request->getParams();

		return $this->handleOcsException(function () use ($path, $shareTypeId, $tokenCandidate, $password) {
			return $this->service->create($path, $shareTypeId, $tokenCandidate, $this->userId, $password ?? '');
		});
	}

	/**
	 * Update token of existing share, select share by its id
	 *
	 * @param string $id Share id to update
	 * @param string $tokenCandidate New token
	 * @return DataResponse<Http::STATUS_OK, CfgShareLinks_Share, array{}> Detail of the updated share
	 * @throws OCSBadRequestException if the token is invalid
	 * @throws OCSException if unauthorized or unexpected exception occurred (differentiated by the response code)
	 * @throws OCSNotFoundException if the share was not found
	 *
	 *  200: Token of a share updated
	 */
	#[NoAdminRequired]
	#[NoCSRFRequired]
	#[CORS]
	#[ApiRoute(verb: 'PUT', url: '/api/v1/share-by-id/{id}/{tokenCandidate}')]
	#[OpenAPI(scope: OpenAPI::SCOPE_DEFAULT, tags: ['API-Share'])]
	public function updateById(string $id, string $tokenCandidate): DataResponse {
		return $this->handleOcsException(function () use ($id, $tokenCandidate) {
			return $this->service->updateById($id, $tokenCandidate, $this->userId);
		});
	}

	/**
	 * Update token of existing share, select share by its token
	 *
	 * @param string $token The (current) token to update
	 * @param string $tokenCandidate New token
	 * @return DataResponse<Http::STATUS_OK, CfgShareLinks_Share, array{}> Detail of the updated share
	 * @throws OCSBadRequestException if the token is invalid
	 * @throws OCSException if unauthorized or unexpected exception occurred (differentiated by the response code)
	 * @throws OCSNotFoundException if the share was not found
	 *
	 * 200: Token of a share updated
	 */
	#[NoAdminRequired]
	#[NoCSRFRequired]
	#[CORS]
	#[ApiRoute(verb: 'PUT', url: '/api/v1/share-by-token/{token}/{tokenCandidate}')]
	#[OpenAPI(scope: OpenAPI::SCOPE_DEFAULT, tags: ['API-Share'])]
	public function updateByToken(string $token, string $tokenCandidate): DataResponse {
		return $this->handleOcsException(function () use ($token, $tokenCandidate) {
			return $this->service->updateByToken($token, $tokenCandidate, $this->userId);
		});
	}
}
