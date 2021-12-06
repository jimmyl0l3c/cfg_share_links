<?php

namespace OCA\CfgShareLinks\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class ShareMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'CfgShareLinks', CustomShare::class); // TODO: change DB name
    }
}
