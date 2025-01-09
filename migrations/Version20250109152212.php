<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250109152212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, phone, address, ville, code_postal, created_at, updated_at, total FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, phone VARCHAR(10) NOT NULL, address VARCHAR(122) NOT NULL, ville VARCHAR(32) NOT NULL, code_postal VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , total DOUBLE PRECISION NOT NULL, CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO "order" (id, phone, address, ville, code_postal, created_at, updated_at, total) SELECT id, phone, address, ville, code_postal, created_at, updated_at, total FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON "order" (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, phone, address, ville, code_postal, created_at, updated_at, total FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, phone VARCHAR(10) NOT NULL, address VARCHAR(122) NOT NULL, ville VARCHAR(32) NOT NULL, code_postal VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , total DOUBLE PRECISION NOT NULL)');
        $this->addSql('INSERT INTO "order" (id, phone, address, ville, code_postal, created_at, updated_at, total) SELECT id, phone, address, ville, code_postal, created_at, updated_at, total FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
    }
}
