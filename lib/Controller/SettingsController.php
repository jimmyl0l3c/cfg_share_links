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

namespace OCA\CfgShareLinks\Controller;

use OCA\CfgShareLinks\AppInfo\AppConstants;
use OCA\CfgShareLinks\Enums\LinkLabelMode;
use OCA\CfgShareLinks\Enums\SettingsKey;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Services\IAppConfig;
use OCP\IRequest;
use ValueError;

class SettingsController extends Controller {
	public function __construct(
		string                        $appName,
		private readonly IAppConfig   $appConfig,
		private readonly AppConstants $appConstants,
		IRequest                      $request,
	) {
		parent::__construct($appName, $request);
	}

	public function save(string $key, string $value): DataResponse {
		try {
			$settings_key = SettingsKey::from($key);
			switch ($settings_key) {
				case SettingsKey::DefaultLabelMode:
					$this->appConfig->setAppValueInt($settings_key->value, LinkLabelMode::from($value)->value);
					return new DataResponse(['message' => 'Saved'], Http::STATUS_OK);
				case SettingsKey::DefaultCustomLabel:
					if (strlen($value) >= 1) {
						$this->appConfig->setAppValueString($settings_key->value, $value);
						return new DataResponse(['message' => 'Saved'], Http::STATUS_OK);
					}
					break;
				case SettingsKey::MinTokenLength:
					if (is_numeric($value) && (int)$value >= 1) {
						$this->appConfig->setAppValueInt($settings_key->value, (int)$value);
						return new DataResponse(['message' => 'Saved'], Http::STATUS_OK);
					}
					break;
				case SettingsKey::DeleteRemovedShareConflicts:
					if (is_numeric($value) && (int)$value >= 0) {
						$this->appConfig->setAppValueBool($settings_key->value, (int)$value > 0);
						return new DataResponse(['message' => 'Saved'], Http::STATUS_OK);
					}
					break;
			}
		} catch (ValueError) {
		}
		return new DataResponse(['message' => 'Invalid key or value'], Http::STATUS_BAD_REQUEST);
	}

	public function fetch(): DataResponse {
		$settings = [
			'defaultLabelMode' => $this->appConfig->getAppValueInt(SettingsKey::DefaultLabelMode->value, $this->appConstants::DEFAULT_LABEL_MODE),
			'defaultLabel' => $this->appConfig->getAppValueString(SettingsKey::DefaultCustomLabel->value, $this->appConstants::DEFAULT_CUSTOM_LABEL),
			'minTokenLength' => $this->appConfig->getAppValueInt(SettingsKey::MinTokenLength->value, $this->appConstants::DEFAULT_MIN_TOKEN_LENGTH),
			'deleteRemovedShareConflicts' => $this->appConfig->getAppValueBool(SettingsKey::DeleteRemovedShareConflicts->value, $this->appConstants::DEFAULT_DELETE_REMOVED_SHARE_CONFLICTS)
		];

		return new DataResponse($settings, Http::STATUS_OK);
	}
}
