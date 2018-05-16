<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180512083644 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('goods');
        $table->addColumn('tags', 'string', ['notnull'=>false, 'length' => 2048]);
        $table->addIndex(['tags'], 'goods_tags_fulltext', ['fulltext']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('goods');
        $table->dropIndex('goods_tags_fulltext');
        $table->dropColumn('tags');

    }
}
