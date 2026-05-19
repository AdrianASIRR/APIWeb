<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519220621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE estado_pelicula (id INT AUTO_INCREMENT NOT NULL, puntuacion INT DEFAULT NULL, comentario LONGTEXT DEFAULT NULL, pelicula_id INT NOT NULL, usuario_id INT NOT NULL, estado_id INT NOT NULL, INDEX IDX_824AC37870713909 (pelicula_id), INDEX IDX_824AC378DB38439E (usuario_id), INDEX IDX_824AC3789F5A440B (estado_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE estado_pelicula ADD CONSTRAINT FK_824AC37870713909 FOREIGN KEY (pelicula_id) REFERENCES pelicula (id)');
        $this->addSql('ALTER TABLE estado_pelicula ADD CONSTRAINT FK_824AC378DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE estado_pelicula ADD CONSTRAINT FK_824AC3789F5A440B FOREIGN KEY (estado_id) REFERENCES estado (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE estado_pelicula DROP FOREIGN KEY FK_824AC37870713909');
        $this->addSql('ALTER TABLE estado_pelicula DROP FOREIGN KEY FK_824AC378DB38439E');
        $this->addSql('ALTER TABLE estado_pelicula DROP FOREIGN KEY FK_824AC3789F5A440B');
        $this->addSql('DROP TABLE estado_pelicula');
    }
}
