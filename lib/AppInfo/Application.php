<?php

namespace OCA\CfgShareLinks\AppInfo;

use OCP\AppFramework\App;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\Util;

class Application extends App{
	public const APP_ID = 'cfgsharelinks';

	public function __construct() {
		parent::__construct(self::APP_ID);

        $container = $this->getContainer();

//        $appName = self::APP_ID;
//        $loadScripts = function(Event $event) use ($appName) {
//            Util::addScript($appName, 'cfgsharelinks-main');
//            Util::addStyle($appName, 'style');
//        };

        /* @var IEventDispatcher $dispatcher */
        $dispatcher = $container->query(IEventDispatcher::class);
//        $dispatcher->addServiceListener('OCA\Files_Sharing::loadAdditionalScripts', $loadScripts);
//        $dispatcher->addServiceListener('OCA\Files::loadAdditionalScripts', $loadScripts);
        $dispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
            script('cfgsharelinks', 'cfgsharelinks-extend-share');  // adds js/script.js
        });
    }
}
