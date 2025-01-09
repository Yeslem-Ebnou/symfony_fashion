<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250109095346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, phone VARCHAR(10) NOT NULL, address VARCHAR(122) NOT NULL, ville VARCHAR(32) NOT NULL, code_postal VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('ALTER TABLE panel ADD COLUMN order_id INTEGER DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TEMPORARY TABLE __temp__panel AS SELECT id, user_id, product_id, status FROM panel');
        $this->addSql('DROP TABLE panel');
        $this->addSql('CREATE TABLE panel (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, product_id INTEGER NOT NULL, status VARCHAR(20) NOT NULL, CONSTRAINT FK_A2ADD30FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A2ADD30F4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO panel (id, user_id, product_id, status) SELECT id, user_id, product_id, status FROM __temp__panel');
        $this->addSql('DROP TABLE __temp__panel');
        $this->addSql('CREATE INDEX IDX_A2ADD30FA76ED395 ON panel (user_id)');
        $this->addSql('CREATE INDEX IDX_A2ADD30F4584665A ON panel (product_id)');
    }
}
