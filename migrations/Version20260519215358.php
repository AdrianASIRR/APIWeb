<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519215358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE genero_pelicula (id INT AUTO_INCREMENT NOT NULL, pelicula_id INT NOT NULL, genero_id INT NOT NULL, INDEX IDX_7FA1F03870713909 (pelicula_id), INDEX IDX_7FA1F038BCE7B795 (genero_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE genero_pelicula ADD CONSTRAINT FK_7FA1F03870713909 FOREIGN KEY (pelicula_id) REFERENCES pelicula (id)');
        $this->addSql('ALTER TABLE genero_pelicula ADD CONSTRAINT FK_7FA1F038BCE7B795 FOREIGN KEY (genero_id) REFERENCES genero (id)');
        $this->addSql('ALTER TABLE pelicula CHANGE borrado borrado TINYINT NOT NULL');
        $this->addSql('DROP INDEX usuario_unique_1 ON usuario');
        $this->addSql('DROP INDEX usuario_unique ON usuario');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE genero_pelicula DROP FOREIGN KEY FK_7FA1F03870713909');
        $this->addSql('ALTER TABLE genero_pelicula DROP FOREIGN KEY FK_7FA1F038BCE7B795');
        $this->addSql('DROP TABLE genero_pelicula');
        $this->addSql('ALTER TABLE pelicula CHANGE borrado borrado TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX usuario_unique_1 ON usuario (nombre)');
        $this->addSql('CREATE UNIQUE INDEX usuario_unique ON usuario (correo)');
    }
}
