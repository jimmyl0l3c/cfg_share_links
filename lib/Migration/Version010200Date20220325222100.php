<?php

declare(strict_types=1);

namespace OCA\CfgShareLinks\Migration;

use Closure;
use Doctrine\DBAL\Schema\SchemaException;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version010200Date20220325222100 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 * @throws SchemaException
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if ($schema->hasTable('cfg_shares')) {
			$schema->dropTable('cfg_shares');
		}

		if (!$schema->hasTable('cfg_shares')) {
			$table = $schema->createTable('cfg_shares');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('full_id', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('token', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('node_id', 'integer', [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['node_id'], 'cfg_shares_node_id');
			$table->addIndex(['full_id'], 'cfg_shares_full_id');
			$table->addIndex(['token'], 'cfg_shares_token');
		}
		return $schema;
	}
}
