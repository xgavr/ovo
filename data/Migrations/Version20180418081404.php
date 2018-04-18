<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180418081404 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('rawprice');
        $table->addColumn('country', 'string', ['notnull'=>false, 'length' => 128]);        
        $table->addColumn('description', 'string', ['notnull'=>false, 'length' => 1024]);        
        $table->addColumn('image', 'string', ['notnull'=>false, 'length' => 256]);        
        $table->addColumn('currency', 'string', ['notnull'=>false, 'length' => 32]);        
        $table->addColumn('rate', 'float', ['notnull'=>false]);        
        $table->addColumn('unit', 'string', ['notnull'=>false, 'length' => 32]);        


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('rawprice');
        $table->dropColumn('country');
        $table->dropColumn('description');
        $table->dropColumn('image');
        $table->dropColumn('currency');
        $table->dropColumn('rate');
        $table->dropColumn('unit');

    }
}
