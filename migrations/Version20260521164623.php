<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260521164623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE director CHANGE borrado borrado TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE estado CHANGE borrado borrado TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_265DE1E33A909126 ON estado (nombre)');
        $this->addSql('ALTER TABLE estado_pelicula ADD borrado TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE genero CHANGE borrado borrado TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A000883A3A909126 ON genero (nombre)');
        $this->addSql('ALTER TABLE pelicula CHANGE borrado borrado TINYINT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE director CHANGE borrado borrado TINYINT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_265DE1E33A909126 ON estado');
        $this->addSql('ALTER TABLE estado CHANGE borrado borrado TINYINT NOT NULL');
        $this->addSql('ALTER TABLE estado_pelicula DROP borrado');
        $this->addSql('DROP INDEX UNIQ_A000883A3A909126 ON genero');
        $this->addSql('ALTER TABLE genero CHANGE borrado borrado TINYINT NOT NULL');
        $this->addSql('ALTER TABLE pelicula CHANGE borrado borrado TINYINT NOT NULL');
    }
}
