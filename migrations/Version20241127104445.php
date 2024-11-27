<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241127104445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE tbl_stage_id_seq CASCADE');
        $this->addSql('CREATE TABLE stage (id SERIAL NOT NULL, classe_eleve VARCHAR(255) NOT NULL, date_debut TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_fin TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, session VARCHAR(255) NOT NULL, themes TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE tbl_stage');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE tbl_stage_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tbl_stage (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE stage');
    }
}
