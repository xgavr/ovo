<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180411205201 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->createTable('goods_group');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('name', 'string', ['notnull'=>true, 'length'=>128]);
        $table->addColumn('description', 'string', ['notnull'=>true, 'length'=>1024]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name'], 'name_idx');
        $table->addOption('engine' , 'InnoDB');

        $table = $schema->getTable('goods');
        $table->addColumn('group_id', 'integer', ['notnull'=>true]);
        $table->addForeignKeyConstraint('goods_group', ['group_id'], ['id'], 
                ['onDelete'=>'CASCADE', 'onUpdate'=>'CASCADE'], 'goods_group_id_goods_group_group_id_fk');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('goods');
        $table->dropColumn('group_id');

        $schema->dropTable('goods_group');
        
    }
}
