<?php
namespace DoneLogic\Gate\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{

	public function upgrade(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('donelogic_gate')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('donelogic_gate')
			)
			->addColumn(
				'vendor_id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				[
					'identity' => true,
					'nullable' => false,
					'primary'  => true,
					'unsigned' => true,
				],
				'Vendor ID'
			)
			->addColumn(
				'customer_id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				[
					'nullable' => false,
					'unsigned' => true
				],
				'Customer ID'					
			)
			->addColumn(
				'category',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[
					'nullable' => false,
					'default' => 'VENDOR'
				],
				'Category'
			)
			->addColumn(
				'services',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'64k',
				[],
				'Services'
			)
			->addColumn(
				'service_regions',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'64k',
				[],
				'Service Regions'
			)				
			->addColumn(
				'company',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true],
				'Company'
			)
			->addColumn(
				'address_line_1',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true],
				'Address Line 1'
			)
			->addColumn(
				'address_line_2',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true],
				'Address Line 2'
			)
			->addColumn(
				'city',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true],
				'City'
			)		
			->addColumn(
				'country_code',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				2,
				['nullable' => true],
				'Country'
			)			
			->addColumn(
				'region_name',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				48,
				['nullable' => true],
				'State/Province'
			)				
			->addColumn(
				'postcode',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true],
				'Zip/Postal Code'
			)
			->addColumn(
				'phone',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true],
				'Phone'
			)
			->addColumn(
				'show_phone',
				\Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
				1,
				[
					'nullable' => false,
					'default' => 0
				],
				'Show Phone?'
			)
			->addColumn(
				'email',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => false],
				'Email'
			)
			->addColumn(
				'show_email',
				\Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
				1,
				[
					'nullable' => false,
					'default' => 0
				],
				'Show Email?'
			)
			->addColumn(
				'website',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true],
				'Website'
			)
			->addColumn(
				'show_website',
				\Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
				1,
				[
					'nullable' => false,
					'default' => 0
				],
				'Show Website?'
			)
			->addColumn(
				'description',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'64k',
				[],
				'Short Description'
			)					
			->addColumn(
				'content',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'64k',
				[],
				'Content'
			)
			->addColumn(
				'image_url',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'64k',
				[],
				'Image'
			)
			->addColumn(
				'active',
				\Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
				1,
				[
					'nullable' => false,
					'default' => 0
				],
				'Active'
			)
			->addColumn(
				'created_at',
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				[
					'nullable' => false, 
					'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
				],
				'Created At'
			)->addColumn(
				'updated_at',
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				[
					'nullable' => false, 
					'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
				],
				'Updated At')
			->setComment('DoneLogic Gate');
			$installer->getConnection()->createTable($table);

			$installer->getConnection()->addIndex(
				$installer->getTable('donelogic_gate'),
				$setup->getIdxName(
					$installer->getTable('donelogic_gate'),
					['email'],
					AdapterInterface::INDEX_TYPE_UNIQUE
				),
				['email'],
				AdapterInterface::INDEX_TYPE_UNIQUE
			);
			//then install data
			//Double check for the existence of the table
			//NOTE: having seperate schema and data upgrades was problematic
			if ($installer->tableExists('donelogic_gate')) {
				$data = [
					[
						'customer_id'		=> 0,
						'category'			=> 'SETTINGS',
						'service_regions' 	=> 'Minnesota',
						'company' 			=> '_SERVICES_',
						'address_line_1' 	=> '',
						'city' 				=> 'NA',
						'country_code'		=> 'US',
						'region_name'		=> 'Minnesota',
						'postcode'			=> '12345',
						'phone'				=> '123-456-7890',
						'show_phone'		=> 0,
						'email'				=> 'services@settings.com',
						'show_email'		=> 0,
						'website'			=> 'https://example.com',
						'show_website'		=> 0,
						'description'		=> '
!!See README.md for more information about this listing!! located at:

	[Magento Root]/app/code/DoneLogic/REAME.md

Upon Installation of DoneLogic Gate, do the following:
1) Login into Admin and go to DoneLogic -> Gate Vendors
2) Edit this listing called "_SERVICES_"
3) Add you services as explained
4) make inacive by selecting Active->No
5) Save
6) Add a vendor from backend and frontend and be sure all works
7) To add one on front end, you need to login as a customer.

DO NOT DELETE THIS LISTING OR THIS DESCRIPTION. 
MAKE INACTIVE SO IT IS NOT VISIBLE ON FRONTEND.
COMPANY MUST BE _SERVICES_.
CATEGORY MUST BE SETTINGS.
COUNTRY VALUE SERVES AS THE DEFAULT COUNTRY FOR YOUR DIRECTORY.
ADD/EDIT/REMOVE SERVICES BY EDITING THIS LISTING.
',
						'content'			=> '',
						'image_url'			=> '',
						'active'			=> 1
					],
					[
						'customer_id'		=> 0,
						'category'			=> 'ADS',
'service_regions' 	=> 'Alabama,Alaska,Arizona,Arkansas,California,Colorado,Connecticut,Delaware,District of Columbia,Florida,Georgia,Hawaii,Idaho,Illinois,Indiana,Iowa,Kansas,Kentucky,Louisiana,Maine,Maryland,Massachusetts,Michigan,Minnesota,Mississippi,Missouri,Montana,Nebraska,Nevada,New Hampshire,New Jersey,New Mexico,New York,North Carolina,North Dakota,Ohio,Oklahoma,Oregon,Pennsylvania,Rhode Island,South Carolina,South Dakota,Tennessee,Texas,Utah,Vermont,Virginia,Washington,West Virginia,Wisconsin,Wyoming',
						'company' 			=> 'Free Listing for Bollard Contractors',
						'city' 				=> 'Any City',
						'country_code'		=> 'US',
						'region_name'		=> 'Ohio',
						'postcode'			=> '12345',
						'phone'				=> '651-432-4050',
						'show_phone'		=> 1,
						'email'				=> 'sales@britepipe.com',
						'show_email'		=> 1,
'description'		=> 'BritePipe invites all Contractors who work with bollards to post a free listing for your services. It\'s easy. And free! Just login or create an account. Then follow link on the Service Directory Home Page, or use link on your Customer Account Dashboard.

You can target your state and neighboring states by selecting them as Service Regions. Then your listing will show up under your country (United States) and your state(s). 

Choose the specific services you perform such as In-Ground Bollard Installation or Bollard Cover Installation. Showing a website or link url, phone number or email are all optional.',
						'image_url'			=> '',
						'active'			=> 1
					]
				];
				foreach ($data as $item) {
					//Insert data
					$installer->getConnection()->insert('donelogic_gate', $item);
				}
			}
		}
		$installer->endSetup();
	}
}