<?php

declare(strict_types=1);

namespace OCA\CfgShareLinks\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20211213143000 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

        if ($schema->hasTable('CfgShareLinks')) {
            $schema->dropTable('CfgShareLinks');
        }

		if (!$schema->hasTable('cfg_shares')) {
			$table = $schema->createTable('cfg_shares');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('share_full_id', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('token', 'string', [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['share_full_id'], 'cfg_shares_share_full_id_index');
		}
		return $schema;
	}
}
