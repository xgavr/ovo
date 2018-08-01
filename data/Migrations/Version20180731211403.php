<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180731211403 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('legal');
        $table->dropColumn('contact_id');
        $table->changeColumn('full_name', ['notnull' => false]);
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('legal');
        $table->addColumn('contact_id', 'integer', ['notnull'=>true, 'default' => 0]);
        $table->changeColumn('full_name', ['notnull' => true]);

    }
}
