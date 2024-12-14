<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241214170645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE student (id SERIAL NOT NULL, school_id INT DEFAULT NULL, grade_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B723AF33C32A47EE ON student (school_id)');
        $this->addSql('CREATE INDEX IDX_B723AF33FE19A1A8 ON student (grade_id)');
        $this->addSql('CREATE TABLE tbl_company (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, zip_code VARCHAR(20) NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_grade (id SERIAL NOT NULL, class_name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_internship (id SERIAL NOT NULL, classe_eleve VARCHAR(255) NOT NULL, date_debut TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_fin TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, session VARCHAR(255) NOT NULL, themes TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_report (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, professor_email VARCHAR(255) NOT NULL, session VARCHAR(255) NOT NULL, classe VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tbl_school (id SERIAL NOT NULL, address VARCHAR(255) NOT NULL, zipcode VARCHAR(10) NOT NULL, city VARCHAR(100) NOT NULL, phone VARCHAR(20) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE school_grades (school_id INT NOT NULL, grade_id INT NOT NULL, PRIMARY KEY(school_id, grade_id))');
        $this->addSql('CREATE INDEX IDX_C75F62DCC32A47EE ON school_grades (school_id)');
        $this->addSql('CREATE INDEX IDX_C75F62DCFE19A1A8 ON school_grades (grade_id)');
        $this->addSql('CREATE TABLE tbl_user (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birth_date DATE DEFAULT NULL, address VARCHAR(255) NOT NULL, postal_code VARCHAR(10) NOT NULL, city VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, verification_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON tbl_user (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33C32A47EE FOREIGN KEY (school_id) REFERENCES tbl_school (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33FE19A1A8 FOREIGN KEY (grade_id) REFERENCES tbl_grade (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE school_grades ADD CONSTRAINT FK_C75F62DCC32A47EE FOREIGN KEY (school_id) REFERENCES tbl_school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE school_grades ADD CONSTRAINT FK_C75F62DCFE19A1A8 FOREIGN KEY (grade_id) REFERENCES tbl_grade (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF33C32A47EE');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF33FE19A1A8');
        $this->addSql('ALTER TABLE school_grades DROP CONSTRAINT FK_C75F62DCC32A47EE');
        $this->addSql('ALTER TABLE school_grades DROP CONSTRAINT FK_C75F62DCFE19A1A8');
        $this->addSql('DROP TABLE student');
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
