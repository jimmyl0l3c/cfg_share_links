<?php

/**
 * Configurable Share Links for Nextcloud
 *
 * @copyright Copyright (C) 2022  Filip Joska <filip@joska.dev>
 *
 * @author Filip Joska <filip@joska.dev>
 *
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\CfgShareLinks\AppInfo;

use OCA\CfgShareLinks\Listener\NodeDeletedListener;
use OCA\CfgShareLinks\Listener\ShareDeletedListener;
use OCA\CfgShareLinks\Middleware\ShareMiddleware;
use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\Files\Events\Node\NodeDeletedEvent;
use OCP\Share\Events\ShareDeletedEvent;
use OCP\Util;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Application extends App implements IBootstrap {
	public const APP_ID = 'cfg_share_links';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		/**
		 * Middleware
		 */
		$context->registerService('ShareMiddleware', function () {
			return new ShareMiddleware();
		});
		$context->registerMiddleware('ShareMiddleware');

		/**
		 * Listen for NodeDeletedEvent (used to remove custom shares of deleted nodes)
		 */
		// possible events: BeforeNodeDeletedEvent, MoveToTrashEvent, NodeDeletedEvent
		$context->registerEventListener(NodeDeletedEvent::class, NodeDeletedListener::class);

		/**
		 * Listen for ShareDeletedEvent (used to remove custom shares from cfg_shares table)
		 */
		$context->registerEventListener(ShareDeletedEvent::class, ShareDeletedListener::class);
	}

	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function boot(IBootContext $context): void {
		/* @var IEventDispatcher $appEventDispatcher */
		$appEventDispatcher = $context->getAppContainer()->get(IEventDispatcher::class);

		/**
		 * Load scripts that mount Vue components
		 */
		$appEventDispatcher->addListener(LoadAdditionalScriptsEvent::class, function () {
			Util::addScript(self::APP_ID, 'cfg_share_links-regRenameLink');
			Util::addScript(self::APP_ID, 'cfg_share_links-regNewLink');
		});
	}
}
