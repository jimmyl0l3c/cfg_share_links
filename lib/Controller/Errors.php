<?php

namespace OCA\CfgShareLinks\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\CfgShareLinks\Service\InvalidTokenException;
use OCP\DB\Exception;

trait Errors {
	protected function handleNotFound(Closure $callback): DataResponse {
		try {
			return new DataResponse($callback());
		} catch (InvalidTokenException $e) {
			$message = ['message' => $e->getMessage()];
			return new DataResponse($message, Http::STATUS_NOT_FOUND);
		}
	}

    protected function handleException(Closure $callback): DataResponse {
        try {
            return new DataResponse($callback());
        } catch (InvalidTokenException $e) { // TODO: change this to handle all exceptions
            $message = ['message' => $e->getMessage()];
            return new DataResponse($message, Http::STATUS_NOT_FOUND);
        }
    }
}
