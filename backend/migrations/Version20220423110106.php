<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220423110106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX uniq_8d93d649e7927c74 RENAME TO email');
        $this->addSql('ALTER INDEX uniq_8d93d649f85e0677 RENAME TO username');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER INDEX username RENAME TO uniq_8d93d649f85e0677');
        $this->addSql('ALTER INDEX email RENAME TO uniq_8d93d649e7927c74');
    }
}
