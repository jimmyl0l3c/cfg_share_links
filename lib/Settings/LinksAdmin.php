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

use OCA\CfgShareLinks\AppInfo\AppConstants;
use OCA\CfgShareLinks\AppInfo\Application;
use OCA\CfgShareLinks\Enums\SettingsKey;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IAppConfig;
use OCP\Settings\ISettings;
use OCP\Util;

class LinksAdmin implements ISettings {
	public function __construct(
		private readonly IAppConfig   $appConfig,
		private readonly AppConstants $appConstants,
	) {
	}

	public function getForm(): TemplateResponse {
		$parameters = [
			'defaultLabelMode' => $this->appConfig->getAppValueInt(SettingsKey::DefaultLabelMode->value, $this->appConstants::DEFAULT_LABEL_MODE),
			'defaultLabel' => $this->appConfig->getAppValueString(SettingsKey::DefaultCustomLabel->value, $this->appConstants::DEFAULT_CUSTOM_LABEL),
			'minTokenLength' => $this->appConfig->getAppValueInt(SettingsKey::MinTokenLength->value, $this->appConstants::DEFAULT_MIN_TOKEN_LENGTH),
			'deleteRemovedShareConflicts' => $this->appConfig->getAppValueBool(SettingsKey::DeleteRemovedShareConflicts->value, $this->appConstants::DEFAULT_DELETE_REMOVED_SHARE_CONFLICTS)
		];

		Util::addScript(Application::APP_ID, 'cfg_share_links-settings');
        Util::addStyle(Application::APP_ID, 'cfg_share_links-settings');
		return new TemplateResponse(Application::APP_ID, 'admin', $parameters, '');
	}

	public function getSection(): string {
		return Application::APP_ID;
	}

	public function getPriority(): int {
		return 10;
	}
}
