<?php

namespace OCA\CfgShareLinks\Controller;

use Closure;

use OC\User\NoUserException;
use OCA\CfgShareLinks\Service\TokenNotUniqueException;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\CfgShareLinks\Service\InvalidTokenException;
use OCP\AppFramework\OCS\OCSBadRequestException;
use OCP\AppFramework\OCS\OCSException;
use OCP\AppFramework\OCS\OCSForbiddenException;
use OCP\AppFramework\OCS\OCSNotFoundException;
use Exception;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\Share\Exceptions\ShareNotFound;

trait Errors {
	/**
	 * Handles:
	 * - NotFoundException
	 * - OCSNotFoundException
	 * - NotPermittedException
	 * - OCSException
	 * - OCSBadRequestException
	 * - OCSForbiddenException
	 * - NoUserException
	 * - InvalidTokenException
	 * - TokenNotUniqueException
	 * - ShareNotFound
	 */
	protected function handleException(Closure $callback): DataResponse {
		try {
			return new DataResponse($callback());
		} catch (NotFoundException | OCSNotFoundException | ShareNotFound $e) {
			return $this->getDataResponse($e, Http::STATUS_NOT_FOUND);
		} catch (InvalidTokenException | TokenNotUniqueException | OCSBadRequestException $e) {
			return $this->getDataResponse($e, Http::STATUS_BAD_REQUEST);
		} catch (NoUserException $e) {
			return $this->getDataResponse($e, Http::STATUS_UNAUTHORIZED);
		} catch (OCSForbiddenException $e) {
			return $this->getDataResponse($e, Http::STATUS_FORBIDDEN);
		} catch (OCSException | NotPermittedException $e) {
			return $this->getDataResponse($e, Http::STATUS_INTERNAL_SERVER_ERROR);
		}
	}

	private function getDataResponse(Exception $e, int $statusCode): DataResponse {
		$message = ['message' => $e->getMessage()];
		return new DataResponse($message, $statusCode);
	}
}
