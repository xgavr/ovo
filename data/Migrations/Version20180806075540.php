<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180806075540 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->createTable('log');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('status', 'integer', ['notnull'=>true]);
        $table->addColumn('date_created', 'datetime', ['notnull'=>true]);
        $table->addColumn('model', 'string', ['notnull'=>false, 'length'=>128]);
        $table->addColumn('parent_model', 'string', ['notnull'=>false, 'length'=>128]);
        $table->addColumn('model_id', 'integer', ['notnull'=>false]);
        $table->addColumn('parent_model_id', 'integer', ['notnull'=>false]);
        $table->addColumn('model_data', 'json', ['notnull'=>false]);
        $table->addColumn('message', 'string', ['notnull'=>false, 'length' => 512]);
        $table->addColumn('attachment', 'json', ['notnull'=>false]);
        $table->addColumn('extra_ip', 'string', ['notnull'=>true, 'lenght' => 64]);
        $table->addColumn('user_id', 'integer', ['notnull'=>false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['model', 'model_id'], 'model_i');
        $table->addIndex(['parent_model', 'parent_model_id'], 'parent_model_i');
        $table->addOption('engine' , 'InnoDB');        

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('log');
    }
}
