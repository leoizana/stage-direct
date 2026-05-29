<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250307153241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tbl_activity (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, zip_code VARCHAR(20) NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_grade (id INT AUTO_INCREMENT NOT NULL, class_name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_internship (id INT AUTO_INCREMENT NOT NULL, relation_id INT NOT NULL, classe_eleve VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, session VARCHAR(255) NOT NULL, themes TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7992FA9D3256915B ON tbl_internship (relation_id)');
        $this->addSql('CREATE TABLE tbl_report (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, created_at DATETIME NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, professor_email VARCHAR(255) NOT NULL, session VARCHAR(255) NOT NULL, classe VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_school (id INT AUTO_INCREMENT NOT NULL, address VARCHAR(255) NOT NULL, zipcode VARCHAR(10) NOT NULL, city VARCHAR(100) NOT NULL, phone VARCHAR(20) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE school_grades (school_id INT NOT NULL, grade_id INT NOT NULL, PRIMARY KEY(school_id, grade_id))');
        $this->addSql('CREATE INDEX IDX_C75F62DCC32A47EE ON school_grades (school_id)');
        $this->addSql('CREATE INDEX IDX_C75F62DCFE19A1A8 ON school_grades (grade_id)');
        $this->addSql('CREATE TABLE tbl_user (id INT AUTO_INCREMENT NOT NULL, grades_id INT DEFAULT NULL, school_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birth_date DATE DEFAULT NULL, address VARCHAR(255) NOT NULL, postal_code VARCHAR(10) NOT NULL, city VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, verification_token VARCHAR(255) DEFAULT NULL, phone INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_38B383A13F828F5E ON tbl_user (grades_id)');
        $this->addSql('CREATE INDEX IDX_38B383A1C32A47EE ON tbl_user (school_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON tbl_user (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('ALTER TABLE tbl_internship ADD CONSTRAINT FK_7992FA9D3256915B FOREIGN KEY (relation_id) REFERENCES tbl_user (id)');
        $this->addSql('ALTER TABLE school_grades ADD CONSTRAINT FK_C75F62DCC32A47EE FOREIGN KEY (school_id) REFERENCES tbl_school (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE school_grades ADD CONSTRAINT FK_C75F62DCFE19A1A8 FOREIGN KEY (grade_id) REFERENCES tbl_grade (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tbl_user ADD CONSTRAINT FK_38B383A13F828F5E FOREIGN KEY (grades_id) REFERENCES tbl_grade (id)');
        $this->addSql('ALTER TABLE tbl_user ADD CONSTRAINT FK_38B383A1C32A47EE FOREIGN KEY (school_id) REFERENCES tbl_school (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_internship DROP FOREIGN KEY FK_7992FA9D3256915B');
        $this->addSql('DROP TABLE tbl_activity');
        $this->addSql('ALTER TABLE school_grades DROP FOREIGN KEY FK_C75F62DCC32A47EE');
        $this->addSql('ALTER TABLE school_grades DROP FOREIGN KEY FK_C75F62DCFE19A1A8');
        $this->addSql('ALTER TABLE tbl_user DROP FOREIGN KEY FK_38B383A13F828F5E');
        $this->addSql('ALTER TABLE tbl_user DROP FOREIGN KEY FK_38B383A1C32A47EE');
        $this->addSql('DROP TABLE tbl_company');
        $this->addSql('DROP TABLE tbl_grade');
        $this->addSql('DROP TABLE tbl_internship');
        $this->addSql('DROP TABLE tbl_report');
        $this->addSql('DROP TABLE tbl_school');
        $this->addSql('DROP TABLE school_grades');
        $this->addSql('DROP TABLE tbl_user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
