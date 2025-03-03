<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303101805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE loan (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, borrower_id INTEGER NOT NULL, book_id INTEGER NOT NULL, borrowed_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , expected_return_date DATETIME NOT NULL, actual_return_date DATETIME DEFAULT NULL, CONSTRAINT FK_C5D30D0311CE312B FOREIGN KEY (borrower_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C5D30D0316A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C5D30D0311CE312B ON loan (borrower_id)');
        $this->addSql('CREATE INDEX IDX_C5D30D0316A2B381 ON loan (book_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__book AS SELECT id, title, author, is_borrowed FROM book');
        $this->addSql('DROP TABLE book');
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, is_borrowed BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO book (id, title, author, is_borrowed) SELECT id, title, author, is_borrowed FROM __temp__book');
        $this->addSql('DROP TABLE __temp__book');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE loan');
        $this->addSql('ALTER TABLE book ADD COLUMN borrow_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD COLUMN return_date DATETIME DEFAULT NULL');
    }
}
