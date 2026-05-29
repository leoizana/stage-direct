<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250401071217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_internship ADD session_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_internship ADD CONSTRAINT FK_7992FA9D613FECDF FOREIGN KEY (session_id) REFERENCES tbl_session (id)');
        $this->addSql('CREATE INDEX IDX_7992FA9D613FECDF ON tbl_internship (session_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_internship DROP FOREIGN KEY FK_7992FA9D613FECDF');
        $this->addSql('ALTER TABLE tbl_internship DROP INDEX IDX_7992FA9D613FECDF');
        $this->addSql('ALTER TABLE tbl_internship DROP COLUMN session_id');
    }
}
