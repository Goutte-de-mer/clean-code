<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303090524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE book');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (a INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, b VARCHAR(255) NOT NULL COLLATE "BINARY", c VARCHAR(255) NOT NULL COLLATE "BINARY", d BOOLEAN NOT NULL, e DATETIME DEFAULT NULL, f DATETIME DEFAULT NULL)');
    }
}
