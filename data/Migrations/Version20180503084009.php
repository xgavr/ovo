<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180503084009 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->createTable('reserve');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('date_created', 'datetime', ['notnull'=>true]);
        $table->addColumn('total', 'float', ['notnull'=>true]);        
        $table->addColumn('comment', 'string', ['notnull'=>false, 'length'=>1024]);
        $table->addColumn('status', 'integer', ['notnull'=>true]);
        $table->addColumn('supplier_id', 'integer', ['notnull'=>true]);        
        $table->addColumn('user_id', 'integer', ['notnull'=>true]);        
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('supplier', ['supplier_id'], ['id'], 
                ['onUpdate'=>'CASCADE'], 'supplier_id_reserve_supplier_id_fk');
        $table->addForeignKeyConstraint('user', ['user_id'], ['id'], 
                ['onUpdate'=>'CASCADE'], 'user_id_reserve_user_id_fk');
        $table->addOption('engine' , 'InnoDB');
                
        $table = $schema->createTable('bid_reserve');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('date_created', 'datetime', ['notnull'=>true]);
        $table->addColumn('price', 'float', ['notnull'=>true]);        
        $table->addColumn('num', 'float', ['notnull'=>true]);        
        $table->addColumn('good_id', 'integer', ['notnull'=>true]);        
        $table->addColumn('reserve_id', 'integer', ['notnull'=>true]);        
        $table->addColumn('user_id', 'integer', ['notnull'=>true]);        
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('reserve', ['reserve_id'], ['id'], 
                ['onDelete'=>'CASCADE', 'onUpdate'=>'CASCADE'], 'reserve_id_bid_reserve_reserve_id_fk');
        $table->addForeignKeyConstraint('user', ['user_id'], ['id'], 
                ['onUpdate'=>'CASCADE'], 'user_id_bid_reserve_user_id_fk');
        $table->addForeignKeyConstraint('goods', ['good_id'], ['id'], 
                ['onUpdate'=>'CASCADE'], 'goods_id_bid_reserve_good_id_fk');
        $table->addOption('engine' , 'InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('bid');
        $schema->dropTable('orders');

    }
}
