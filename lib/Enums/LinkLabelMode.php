<?php

namespace OCA\CfgShareLinks\Enums;

enum LinkLabelMode: int {
	case NoLabel = 0;
	case SameAsToken = 1;
	case UserSpecified = 2;
}
