<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180413075833 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $schema->dropTable('images');
        
        $table = $schema->createTable('image');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('good_id', 'integer', ['notnull'=>true, 'default' => 0]);  
        $table->addColumn('path', 'string', ['notnull'=>true, 'length'=>1024]);        
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('goods', ['good_id'], ['id'], 
                ['onDelete'=>'CASCADE', 'onUpdate'=>'CASCADE'], 'goods_id_image_good_id_fk');
        $table->addOption('engine' , 'InnoDB');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('image');

    }
}
