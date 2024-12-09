<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241209104539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE report (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_school (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_student (id SERIAL NOT NULL, school_id INT NOT NULL, user_id INT NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, phone VARCHAR(20) DEFAULT NULL, class_name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EC70A747C32A47EE ON tbl_student (school_id)');
        $this->addSql('CREATE INDEX IDX_EC70A747A76ED395 ON tbl_student (user_id)');
        $this->addSql('ALTER TABLE tbl_student ADD CONSTRAINT FK_EC70A747C32A47EE FOREIGN KEY (school_id) REFERENCES tbl_school (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tbl_student ADD CONSTRAINT FK_EC70A747A76ED395 FOREIGN KEY (user_id) REFERENCES tbl_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tbl_student DROP CONSTRAINT FK_EC70A747C32A47EE');
        $this->addSql('ALTER TABLE tbl_student DROP CONSTRAINT FK_EC70A747A76ED395');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE tbl_school');
        $this->addSql('DROP TABLE tbl_student');
    }
}
