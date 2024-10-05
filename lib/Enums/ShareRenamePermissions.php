<?php

namespace OCA\CfgShareLinks\Enums;

enum ShareRenamePermissions: int {
	case ShareOwner = 1;
	case FileOwner = 1 << 1;
	case UserThatCanShare = 1 << 2;
}
