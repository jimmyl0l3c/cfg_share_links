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

namespace OCA\CfgShareLinks\Settings;

use OCA\CfgShareLinks\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Settings\ISettings;
use OCP\Util;

class LinksAdmin implements ISettings {
	/** @var IConfig */
	private $config;

	public function __construct(
		IConfig $config,
		IL10N $l
	) {
		$this->config = $config;
		$this->l = $l;
	}

	public function getForm(): TemplateResponse {
		$parameters = [
			'defaultLabelMode' => $this->config->getAppValue(Application::APP_ID, 'default_label_mode', 0),
			'defaultLabel' => $this->config->getAppValue(Application::APP_ID, 'default_label', 'Custom link'),
			'minTokenLength' => $this->config->getAppValue(Application::APP_ID, 'min_token_length', 3)
		];

		Util::addScript(Application::APP_ID, 'cfgsharelinks-settings-admin');
		return new TemplateResponse(Application::APP_ID, 'admin', $parameters, '');
	}

	public function getSection(): string {
		return 'cfgsharelinks';
	}

	public function getPriority(): int {
		return 10;
	}
}
