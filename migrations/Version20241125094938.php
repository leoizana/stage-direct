<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125094938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_user ADD first_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tbl_user ADD last_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tbl_user ADD birth_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_user ADD address VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tbl_user ADD postal_code VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE tbl_user ADD city VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tbl_user DROP first_name');
        $this->addSql('ALTER TABLE tbl_user DROP last_name');
        $this->addSql('ALTER TABLE tbl_user DROP birth_date');
        $this->addSql('ALTER TABLE tbl_user DROP address');
        $this->addSql('ALTER TABLE tbl_user DROP postal_code');
        $this->addSql('ALTER TABLE tbl_user DROP city');
    }
}
