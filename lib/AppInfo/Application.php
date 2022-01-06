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
		$container->registerService('ShareMiddleware', function () {
			return new ShareMiddleware();
		});
		$container->registerMiddleware('ShareMiddleware');

		/* @var IEventDispatcher $dispatcher */
		$dispatcher = $container->query(IEventDispatcher::class);
		//        'OCA\Files_Sharing::loadAdditionalScripts'
		$dispatcher->addListener('OCA\Files::loadAdditionalScripts', function () {
			script('cfgsharelinks', 'cfgsharelinks-reg-rename');
			script('cfgsharelinks', 'cfgsharelinks-reg-new');
		});
	}
}
