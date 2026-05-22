<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260521163837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actor CHANGE borrado borrado TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE usuario CHANGE borrado borrado TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2265B05D77040BC9 ON usuario (correo)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2265B05D3A909126 ON usuario (nombre)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actor CHANGE borrado borrado TINYINT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_2265B05D77040BC9 ON usuario');
        $this->addSql('DROP INDEX UNIQ_2265B05D3A909126 ON usuario');
        $this->addSql('ALTER TABLE usuario CHANGE borrado borrado TINYINT NOT NULL');
    }
}
