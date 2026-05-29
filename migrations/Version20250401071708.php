<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250401071708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_session ADD school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_session ADD CONSTRAINT FK_8B17DDA0C32A47EE FOREIGN KEY (school_id) REFERENCES tbl_school (id)');
        $this->addSql('CREATE INDEX IDX_8B17DDA0C32A47EE ON tbl_session (school_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_session DROP FOREIGN KEY FK_8B17DDA0C32A47EE');
        $this->addSql('ALTER TABLE tbl_session DROP INDEX IDX_8B17DDA0C32A47EE');
        $this->addSql('ALTER TABLE tbl_session DROP COLUMN school_id');
    }
}
