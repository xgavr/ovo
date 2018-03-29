<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180329064121 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('contact');
        $table->addColumn('icq', 'string', ['notnull'=>false, 'length'=>64]);
        $table->addColumn('telegramm', 'string', ['notnull'=>false, 'length'=>64]);
        $table->addColumn('address', 'string', ['notnull'=>false, 'length'=>1024]);
        $table->addColumn('address_sms', 'string', ['notnull'=>false, 'length'=>256]);
        
        $table = $schema->createTable('contact_legal');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('contact_id', 'integer', ['notnull'=>true]);
        $table->addColumn('legal_id', 'integer', ['notnull'=>true]);
        $table->setPrimaryKey(['id']);
        $table->addOption('engine' , 'InnoDB');        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('contact');
        $table->dropColumn('icq');
        $table->dropColumn('telegramm');
        $table->dropColumn('address');
        $table->dropColumn('address_sms');
        
        $schema->dropTable('contact_legal');        
    }
}
