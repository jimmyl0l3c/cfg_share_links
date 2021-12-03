<?php

namespace OCA\CfgShareLinks\AppInfo;

use OCA\CfgShareLinks\Service\ShareManager;
use OCP\AppFramework\App;
use Psr\Container\ContainerInterface;

class Application extends App {
	public const APP_ID = 'cfgsharelinks';

	public function __construct() {
		parent::__construct(self::APP_ID);

        $container = $this->getContainer();

        $container->registerService('OC\Share20\Manager', function (ContainerInterface $c) {
            return $c->get('OCA\CfgShareLinks\Service\ShareManager');
        });
    }
}
