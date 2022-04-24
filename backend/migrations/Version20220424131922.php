<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220424131922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, username VARCHAR(254) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, surname VARCHAR(100) NOT NULL, birthday DATE NOT NULL, email VARCHAR(254) NOT NULL, gender VARCHAR(20) NOT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX unique_email ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX unique_username ON "user" (username)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE verify_user_request (id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7AD17FBAA76ED395 ON verify_user_request (user_id)');
        $this->addSql('COMMENT ON COLUMN verify_user_request.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN verify_user_request.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE verify_user_request ADD CONSTRAINT FK_7AD17FBAA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE verify_user_request DROP CONSTRAINT FK_7AD17FBAA76ED395');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE verify_user_request');
    }
}
