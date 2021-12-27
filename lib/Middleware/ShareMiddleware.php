<?php

namespace OCA\CfgShareLinks\Middleware;

use OCA\CfgShareLinks\Controller\ShareController;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Middleware;
use OCP\Lock\LockedException;

class ShareMiddleware extends Middleware {
    /**
     * @throws LockedException
     */
    public function afterController($controller, $methodName, Response $response): Response
    {
        if ($controller instanceof ShareController) {
            $controller->cleanup();
        }
        return parent::afterController($controller, $methodName, $response);
    }
}
