<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250105210249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, name, description, price, stock, brand, size, color, image, created_at, updated_at FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(122) NOT NULL, description CLOB DEFAULT NULL, price DOUBLE PRECISION NOT NULL, stock INTEGER NOT NULL, brand VARCHAR(122) NOT NULL, size VARCHAR(122) NOT NULL, color VARCHAR(122) NOT NULL, image VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO product (id, name, description, price, stock, brand, size, color, image, created_at, updated_at) SELECT id, name, description, price, stock, brand, size, color, image, created_at, updated_at FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, name, description, price, stock, brand, size, color, image, created_at, updated_at FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(122) NOT NULL, description CLOB DEFAULT NULL, price DOUBLE PRECISION NOT NULL, stock INTEGER NOT NULL, brand VARCHAR(122) NOT NULL, size VARCHAR(122) NOT NULL, color VARCHAR(122) NOT NULL, image BLOB NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO product (id, name, description, price, stock, brand, size, color, image, created_at, updated_at) SELECT id, name, description, price, stock, brand, size, color, image, created_at, updated_at FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
    }
}
