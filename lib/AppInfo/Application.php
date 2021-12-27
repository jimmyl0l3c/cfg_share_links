<?php

namespace OCA\CfgShareLinks\AppInfo;

use OCA\CfgShareLinks\Middleware\ShareMiddleware;
use OCP\AppFramework\App;
use OCP\AppFramework\QueryException;
use OCP\EventDispatcher\IEventDispatcher;

class Application extends App {
	public const APP_ID = 'cfgsharelinks';

    /**
     * @throws QueryException
     */
    public function __construct() {
		parent::__construct(self::APP_ID);

        $container = $this->getContainer();

        /**
         * Middleware
         */
        $container->registerService('ShareMiddleware', function(){
            return new ShareMiddleware();
        });
        $container->registerMiddleware('ShareMiddleware');

        /* @var IEventDispatcher $dispatcher */
        $dispatcher = $container->query(IEventDispatcher::class);
//        $dispatcher->addServiceListener('OCA\Files_Sharing::loadAdditionalScripts', );
//        $dispatcher->addServiceListener('OCA\Files::loadAdditionalScripts', );
        $dispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
            script('cfgsharelinks', 'cfgsharelinks-reg-rename');
            script('cfgsharelinks', 'cfgsharelinks-reg-new');
        });
    }
}
