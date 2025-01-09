<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250109103048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "order" ADD COLUMN total DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, phone, address, ville, code_postal, created_at, updated_at FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, phone VARCHAR(10) NOT NULL, address VARCHAR(122) NOT NULL, ville VARCHAR(32) NOT NULL, code_postal VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO "order" (id, phone, address, ville, code_postal, created_at, updated_at) SELECT id, phone, address, ville, code_postal, created_at, updated_at FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
    }
}
