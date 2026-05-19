<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519220027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE actor_pelicula (id INT AUTO_INCREMENT NOT NULL, actor_id INT NOT NULL, pelicula_id INT NOT NULL, INDEX IDX_DEF90D8310DAF24A (actor_id), INDEX IDX_DEF90D8370713909 (pelicula_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE director_pelicula (id INT AUTO_INCREMENT NOT NULL, director_id INT NOT NULL, pelicula_id INT NOT NULL, INDEX IDX_950C9247899FB366 (director_id), INDEX IDX_950C924770713909 (pelicula_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE actor_pelicula ADD CONSTRAINT FK_DEF90D8310DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE actor_pelicula ADD CONSTRAINT FK_DEF90D8370713909 FOREIGN KEY (pelicula_id) REFERENCES pelicula (id)');
        $this->addSql('ALTER TABLE director_pelicula ADD CONSTRAINT FK_950C9247899FB366 FOREIGN KEY (director_id) REFERENCES director (id)');
        $this->addSql('ALTER TABLE director_pelicula ADD CONSTRAINT FK_950C924770713909 FOREIGN KEY (pelicula_id) REFERENCES pelicula (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actor_pelicula DROP FOREIGN KEY FK_DEF90D8310DAF24A');
        $this->addSql('ALTER TABLE actor_pelicula DROP FOREIGN KEY FK_DEF90D8370713909');
        $this->addSql('ALTER TABLE director_pelicula DROP FOREIGN KEY FK_950C9247899FB366');
        $this->addSql('ALTER TABLE director_pelicula DROP FOREIGN KEY FK_950C924770713909');
        $this->addSql('DROP TABLE actor_pelicula');
        $this->addSql('DROP TABLE director_pelicula');
    }
}
