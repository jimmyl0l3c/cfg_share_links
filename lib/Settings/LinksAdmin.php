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
use OCP\AppFramework\Services\IAppConfig;
use OCP\Settings\ISettings;
use OCP\Util;

class LinksAdmin implements ISettings {
	public function __construct(
		private IAppConfig $appConfig
	) {
	}

	public function getForm(): TemplateResponse {
		$parameters = [
			'defaultLabelMode' => $this->appConfig->getAppValue('default_label_mode', 0),
			'defaultLabel' => $this->appConfig->getAppValue('default_label', 'Custom link'),
			'minTokenLength' => $this->appConfig->getAppValue('min_token_length', 3),
			'deleteRemovedShareConflicts' => $this->appConfig->getAppValue('deleteRemovedShareConflicts', false)
		];

		Util::addScript(Application::APP_ID, 'cfg_share_links-settings-admin');
		return new TemplateResponse(Application::APP_ID, 'admin', $parameters, '');
	}

	public function getSection(): string {
		return 'cfg_share_links';
	}

	public function getPriority(): int {
		return 10;
	}
}
