<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250419194148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_company ADD is_verified BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_internship ADD company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_internship ADD is_verified BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_internship ADD CONSTRAINT FK_7992FA9D979B1AD6 FOREIGN KEY (company_id) REFERENCES tbl_company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7992FA9D979B1AD6 ON tbl_internship (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tbl_internship DROP CONSTRAINT FK_7992FA9D979B1AD6');
        $this->addSql('DROP INDEX IDX_7992FA9D979B1AD6');
        $this->addSql('ALTER TABLE tbl_internship DROP company_id');
        $this->addSql('ALTER TABLE tbl_internship DROP is_verified');
        $this->addSql('ALTER TABLE tbl_company DROP is_verified');
    }
}
