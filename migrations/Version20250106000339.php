<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250106000339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, name, description, price, stock, brand, size, color, image, created_at, updated_at FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(122) NOT NULL, description CLOB DEFAULT NULL, price DOUBLE PRECISION NOT NULL, stock INTEGER NOT NULL, brand VARCHAR(122) NOT NULL, size VARCHAR(122) NOT NULL, color VARCHAR(122) NOT NULL, image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO product (id, name, description, price, stock, brand, size, color, image, created_at, updated_at) SELECT id, name, description, price, stock, brand, size, color, image, created_at, updated_at FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, name, description, price, stock, brand, size, color, image, created_at, updated_at FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(122) NOT NULL, description CLOB DEFAULT NULL, price DOUBLE PRECISION NOT NULL, stock INTEGER NOT NULL, brand VARCHAR(122) NOT NULL, size VARCHAR(122) NOT NULL, color VARCHAR(122) NOT NULL, image VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO product (id, name, description, price, stock, brand, size, color, image, created_at, updated_at) SELECT id, name, description, price, stock, brand, size, color, image, created_at, updated_at FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
    }
}
