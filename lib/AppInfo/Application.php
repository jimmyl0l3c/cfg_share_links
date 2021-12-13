<?php

namespace OCA\CfgShareLinks\AppInfo;

use OCP\AppFramework\App;
use OCP\EventDispatcher\IEventDispatcher;

class Application extends App { // TODO: handle share deletion
	public const APP_ID = 'cfgsharelinks';

	public function __construct() {
		parent::__construct(self::APP_ID);

        $container = $this->getContainer();

        /* @var IEventDispatcher $dispatcher */
        $dispatcher = $container->query(IEventDispatcher::class);
//        $dispatcher->addServiceListener('OCA\Files_Sharing::loadAdditionalScripts', $loadScripts);
//        $dispatcher->addServiceListener('OCA\Files::loadAdditionalScripts', $loadScripts);
        $dispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
            script('cfgsharelinks', 'cfgsharelinks-reg-rename');
            script('cfgsharelinks', 'cfgsharelinks-reg-new');
        });
    }
}
