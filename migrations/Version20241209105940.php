<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241209105940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE report_id_seq CASCADE');
        $this->addSql('CREATE TABLE tbl_grade (id SERIAL NOT NULL, class_name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_report (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE school_grades (school_id INT NOT NULL, grade_id INT NOT NULL, PRIMARY KEY(school_id, grade_id))');
        $this->addSql('CREATE INDEX IDX_C75F62DCC32A47EE ON school_grades (school_id)');
        $this->addSql('CREATE INDEX IDX_C75F62DCFE19A1A8 ON school_grades (grade_id)');
        $this->addSql('ALTER TABLE school_grades ADD CONSTRAINT FK_C75F62DCC32A47EE FOREIGN KEY (school_id) REFERENCES tbl_school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE school_grades ADD CONSTRAINT FK_C75F62DCFE19A1A8 FOREIGN KEY (grade_id) REFERENCES tbl_grade (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE report');
        $this->addSql('ALTER TABLE tbl_school ADD address VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tbl_school ADD zipcode VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE tbl_school ADD city VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE tbl_school ADD phone VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_school ADD email VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE report (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE school_grades DROP CONSTRAINT FK_C75F62DCC32A47EE');
        $this->addSql('ALTER TABLE school_grades DROP CONSTRAINT FK_C75F62DCFE19A1A8');
        $this->addSql('DROP TABLE tbl_grade');
        $this->addSql('DROP TABLE tbl_report');
        $this->addSql('DROP TABLE school_grades');
        $this->addSql('ALTER TABLE tbl_school DROP address');
        $this->addSql('ALTER TABLE tbl_school DROP zipcode');
        $this->addSql('ALTER TABLE tbl_school DROP city');
        $this->addSql('ALTER TABLE tbl_school DROP phone');
        $this->addSql('ALTER TABLE tbl_school DROP email');
    }
}
