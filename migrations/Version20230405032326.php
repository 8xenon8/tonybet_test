<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230405032326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE division (id INT NOT NULL, name VARCHAR(63) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE game (id INT NOT NULL, succeeding_game_id INT DEFAULT NULL, type INT NOT NULL, is_played BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_232B318C8E83FA0B ON game (succeeding_game_id)');
        $this->addSql('CREATE TABLE game_info (id INT NOT NULL, team_id INT NOT NULL, game_id INT NOT NULL, is_won BOOLEAN NOT NULL, is_home BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_209C5D7B296CD8AE ON game_info (team_id)');
        $this->addSql('CREATE INDEX IDX_209C5D7BE48FD905 ON game_info (game_id)');
        $this->addSql('CREATE TABLE team (id INT NOT NULL, division_id INT NOT NULL, name VARCHAR(63) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C4E0A61F41859289 ON team (division_id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C8E83FA0B FOREIGN KEY (succeeding_game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_info ADD CONSTRAINT FK_209C5D7B296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_info ADD CONSTRAINT FK_209C5D7BE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F41859289 FOREIGN KEY (division_id) REFERENCES division (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE game DROP CONSTRAINT FK_232B318C8E83FA0B');
        $this->addSql('ALTER TABLE game_info DROP CONSTRAINT FK_209C5D7B296CD8AE');
        $this->addSql('ALTER TABLE game_info DROP CONSTRAINT FK_209C5D7BE48FD905');
        $this->addSql('ALTER TABLE team DROP CONSTRAINT FK_C4E0A61F41859289');
        $this->addSql('DROP TABLE division');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_info');
        $this->addSql('DROP TABLE team');
    }
}
