<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227110832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_internship ADD relation_id INT NOT NULL');
        $this->addSql('ALTER TABLE tbl_internship ADD CONSTRAINT FK_7992FA9D3256915B FOREIGN KEY (relation_id) REFERENCES tbl_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7992FA9D3256915B ON tbl_internship (relation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tbl_internship DROP CONSTRAINT FK_7992FA9D3256915B');
        $this->addSql('DROP INDEX IDX_7992FA9D3256915B');
        $this->addSql('ALTER TABLE tbl_internship DROP relation_id');
    }
}
