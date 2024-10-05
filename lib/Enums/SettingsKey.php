<?php

namespace OCA\CfgShareLinks\Enums;

enum SettingsKey: string {
	case DefaultLabelMode = 'default_label_mode';
	case DefaultCustomLabel = 'default_label';
	case MinTokenLength = 'min_token_length';
	case CreatePermissions = 'create_permissions';
	case RenamePermissions = 'rename_permissions';
	case DeleteRemovedShareConflicts = 'deleteRemovedShareConflicts';
}
