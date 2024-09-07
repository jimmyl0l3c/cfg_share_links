<?php

namespace OCA\CfgShareLinks\AppInfo;

class AppConstants {
	public const DEFAULT_VALID_TOKEN_REGEX = '/^[a-zA-Z0-9_]+$/';
	public const DEFAULT_MIN_TOKEN_LENGTH = 3;
	public const MAX_TOKEN_LENGTH = 32; // Nextcloud saves the token in VARCHAR(32)

	public const DEFAULT_LABEL_MODE = 0;
	public const DEFAULT_CUSTOM_LABEL = 'Custom link';

	public const DEFAULT_DELETE_REMOVED_SHARE_CONFLICTS = false;
}
