<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250310094451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_grade (user_id INT NOT NULL, grade_id INT NOT NULL, PRIMARY KEY(user_id, grade_id))');
        $this->addSql('CREATE INDEX IDX_BB98556CA76ED395 ON user_grade (user_id)');
        $this->addSql('CREATE INDEX IDX_BB98556CFE19A1A8 ON user_grade (grade_id)');
        $this->addSql('ALTER TABLE user_grade ADD CONSTRAINT FK_BB98556CA76ED395 FOREIGN KEY (user_id) REFERENCES tbl_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_grade ADD CONSTRAINT FK_BB98556CFE19A1A8 FOREIGN KEY (grade_id) REFERENCES tbl_grade (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tbl_user DROP CONSTRAINT fk_38b383a13f828f5e');
        $this->addSql('DROP INDEX idx_38b383a13f828f5e');
        $this->addSql('ALTER TABLE tbl_user DROP grades_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_grade DROP CONSTRAINT FK_BB98556CA76ED395');
        $this->addSql('ALTER TABLE user_grade DROP CONSTRAINT FK_BB98556CFE19A1A8');
        $this->addSql('DROP TABLE user_grade');
        $this->addSql('ALTER TABLE tbl_user ADD grades_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_user ADD CONSTRAINT fk_38b383a13f828f5e FOREIGN KEY (grades_id) REFERENCES tbl_grade (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_38b383a13f828f5e ON tbl_user (grades_id)');
    }
}
