<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250320111944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $activityTableExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_activity'"
        );

        if (!$activityTableExists) {
            $this->addSql('CREATE TABLE tbl_activity (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        }

        $columnExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_company' AND COLUMN_NAME = 'activity_id'"
        );

        if (!$columnExists) {
            $this->addSql('ALTER TABLE tbl_company ADD activity_id INT DEFAULT NULL');
        }

        $foreignKeyExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_company' AND CONSTRAINT_NAME = 'FK_14EC013B81C06096'"
        );

        if (!$foreignKeyExists) {
            $this->addSql('ALTER TABLE tbl_company ADD CONSTRAINT FK_14EC013B81C06096 FOREIGN KEY (activity_id) REFERENCES tbl_activity (id)');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $foreignKeyExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_company' AND CONSTRAINT_NAME = 'FK_14EC013B81C06096'"
        );

        if ($foreignKeyExists) {
            $this->addSql('ALTER TABLE tbl_company DROP FOREIGN KEY FK_14EC013B81C06096');
        }

        $columnExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_company' AND COLUMN_NAME = 'activity_id'"
        );

        if ($columnExists) {
            $this->addSql('ALTER TABLE tbl_company DROP COLUMN activity_id');
        }
    }
}
