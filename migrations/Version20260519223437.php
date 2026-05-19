<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519223437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actor_pelicula MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE actor_pelicula DROP id, DROP PRIMARY KEY, ADD PRIMARY KEY (actor_id, pelicula_id)');
        $this->addSql('ALTER TABLE director_pelicula MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE director_pelicula DROP id, DROP PRIMARY KEY, ADD PRIMARY KEY (director_id, pelicula_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actor_pelicula ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE director_pelicula ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
