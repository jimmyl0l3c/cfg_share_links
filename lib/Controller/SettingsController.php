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

use OCA\CfgShareLinks\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Services\IAppConfig;
use OCP\IRequest;

class SettingsController extends Controller {
	public function __construct(
		private IAppConfig $appConfig,
		IRequest           $request
	) {
		parent::__construct(Application::APP_ID, $request);
	}

	public function save(string $key, string $value): DataResponse {
		switch ($key) {
			case 'default_label_mode':
				if (is_numeric($value) && (int)$value >= 0 && (int)$value <= 2) {
					$this->appConfig->setAppValue('default_label_mode', (int)$value);
					return new DataResponse(['message' => 'Saved'], Http::STATUS_OK);
				}
				break;
			case 'default_label':
				if (strlen($value) >= 1) {
					$this->appConfig->setAppValue('default_label', $value);
					return new DataResponse(['message' => 'Saved'], Http::STATUS_OK);
				}
				break;
			case 'min_token_length':
				if (is_numeric($value) && (int)$value >= 1) {
					$this->appConfig->setAppValue('min_token_length', (int)$value);
					return new DataResponse(['message' => 'Saved'], Http::STATUS_OK);
				}
				break;
			case 'deleteRemovedShareConflicts':
				if (is_numeric($value) && (int)$value >= 0) {
					$this->appConfig->setAppValue('deleteRemovedShareConflicts', (int)$value > 0);
					return new DataResponse(['message' => 'Saved'], Http::STATUS_OK);
				}
				break;
		}

		return new DataResponse(['message' => 'Invalid key or value'], Http::STATUS_BAD_REQUEST);
	}

	public function fetch(): DataResponse {
		$settings = [
			'defaultLabelMode' => $this->appConfig->getAppValue('default_label_mode', 0),
			'defaultLabel' => $this->appConfig->getAppValue('default_label', 'Custom link'),
			'minTokenLength' => $this->appConfig->getAppValue('min_token_length', 3),
			'deleteRemovedShareConflicts' => $this->appConfig->getAppValue('deleteRemovedShareConflicts', false)
		];

		return new DataResponse($settings, Http::STATUS_OK);
	}
}
