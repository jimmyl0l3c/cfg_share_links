<?php

namespace OCA\CfgShareLinks\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\CfgShareLinks\Service\ShareServiceException;
use OCP\DB\Exception;

trait Errors {
	protected function handleNotFound(Closure $callback): DataResponse {
		try {
			return new DataResponse($callback());
		} catch (ShareServiceException $e) {
			$message = ['message' => $e->getMessage()];
			return new DataResponse($message, Http::STATUS_NOT_FOUND);
		}
	}

    protected function handleException(Closure $callback): DataResponse {
        try {
            return new DataResponse($callback());
        } catch (ShareServiceException $e) { // TODO: change this
            $message = ['message' => $e->getMessage()];
            return new DataResponse($message, Http::STATUS_NOT_FOUND);
        }
    }
}
