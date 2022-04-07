<?php
namespace DoneLogic\Core\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('donelogic_core')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('donelogic_core')
			)
				->addColumn(
					'record_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'Record ID'
				)
				->addColumn(
					'name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable => false'],
					'Record Name'
				)
				->addColumn(
					'value',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'64k',
					[],
					'Record Value'
				)
				->addColumn(
					'description',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'64k',
					[],
					'Description'
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created At'
				)->addColumn(
					'updated_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
					'Updated At')
				->setComment('DoneLogic Core');
			$installer->getConnection()->createTable($table);

			$installer->getConnection()->addIndex(
				$installer->getTable('donelogic_core'),
				$setup->getIdxName(
					$installer->getTable('donelogic_core'),
					['name'],
					AdapterInterface::INDEX_TYPE_UNIQUE
				),
				['name'],
				AdapterInterface::INDEX_TYPE_UNIQUE
			);
			//then install data
			//Double check for the existence of the table
			//NOTE: having seperate schema and data upgrades was problematic
			if ($installer->tableExists('donelogic_core')) {
				$data = [
					[
						'name' => 'gtag_head_snippet',
						'description' => 'gtag_head_snippet: Paste your Gtag head code in the value textarea. 
							The code is at Google Tag Manager. Do not change the name [gtag_head_snippet].
							The template file needs to retrieve this value. This snippet is then placed
							in the head, right before the body opening tag, on frontend pages.
							',
					],
					[
						'name' => 'gtag_body_snippet',
						'description' => 'gtag_body_snippet: Paste your Gtag body code in the value textarea. 
						The code is at Google Tag Manager. Do not change the name [gtag_body_snippet].
						The template file needs to retrieve this value. This snippet is then placed
						in the body, right after the body opening tag, on frontend pages.
						',
					],
				];
				foreach ($data as $item) {
					//Insert data
					$installer->getConnection()->insert('donelogic_core', $item);
				}
			}
		}
		$installer->endSetup();
	}
}