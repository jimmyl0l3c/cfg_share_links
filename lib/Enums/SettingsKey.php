<?php

namespace OCA\CfgShareLinks\Enums;

enum SettingsKey: string {
	case DefaultLabelMode = 'default_label_mode';
	case DefaultCustomLabel = 'default_label';
	case MinTokenLength = 'min_token_length';
	case DeleteRemovedShareConflicts = 'deleteRemovedShareConflicts';
}
