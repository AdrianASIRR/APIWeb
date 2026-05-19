<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519221824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actor ADD borrado TINYINT NOT NULL');
        $this->addSql('ALTER TABLE director ADD borrado TINYINT NOT NULL');
        $this->addSql('ALTER TABLE estado ADD borrado TINYINT NOT NULL');
        $this->addSql('ALTER TABLE genero ADD borrado TINYINT NOT NULL');
        $this->addSql('ALTER TABLE usuario ADD borrado TINYINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actor DROP borrado');
        $this->addSql('ALTER TABLE director DROP borrado');
        $this->addSql('ALTER TABLE estado DROP borrado');
        $this->addSql('ALTER TABLE genero DROP borrado');
        $this->addSql('ALTER TABLE usuario DROP borrado');
    }
}
