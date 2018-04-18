<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180418050736 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('price_settings');
        $table->addColumn('country', 'integer', ['notnull'=>false]);        
        $table->addColumn('description', 'integer', ['notnull'=>false]);        
        $table->addColumn('image', 'integer', ['notnull'=>false]);        
        $table->addColumn('currency', 'integer', ['notnull'=>false]);        
        $table->addColumn('rate', 'integer', ['notnull'=>false]);        
        $table->addColumn('unit', 'integer', ['notnull'=>false]);        

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('price_settings');
        $table->dropColumn('country');
        $table->dropColumn('description');
        $table->dropColumn('image');
        $table->dropColumn('currency');
        $table->dropColumn('rate');
        $table->dropColumn('unit');

    }
}
