<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202102547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_company ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tbl_company ADD street VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tbl_company ADD zip_code VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE tbl_company ADD city VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tbl_company ADD country VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tbl_company ADD phone VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE tbl_company ADD email VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tbl_company DROP name');
        $this->addSql('ALTER TABLE tbl_company DROP street');
        $this->addSql('ALTER TABLE tbl_company DROP zip_code');
        $this->addSql('ALTER TABLE tbl_company DROP city');
        $this->addSql('ALTER TABLE tbl_company DROP country');
        $this->addSql('ALTER TABLE tbl_company DROP phone');
        $this->addSql('ALTER TABLE tbl_company DROP email');
    }
}
