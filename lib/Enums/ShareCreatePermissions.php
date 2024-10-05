<?php

namespace OCA\CfgShareLinks\Enums;

enum ShareCreatePermissions: int
{
    case AdminOnly = 1;
    case UserThatCanShare = 2;
}
