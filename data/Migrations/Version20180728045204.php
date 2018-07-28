<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180728045204 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('supplier');
        $table->addColumn('contract', 'integer', ['notnull'=>true, 'default' => 2]);
        $table->changeColumn('info', ['notnull'=>false]);
        $table->changeColumn('address', ['notnull'=>false]);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('supplier');
        $table->dropColumn('contract');
    }
}
