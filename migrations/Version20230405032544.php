<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230405032544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO division VALUES (0, \'Division 1\'), (1, \'Division 2\')');
        $this->addSql("INSERT INTO team (id,division_id,name) VALUES 
        (0,0,'Team A')
        ,(1,0,'Team B')
        ,(2,0,'Team C')
        ,(3,0,'Team D')
        ,(4,0,'Team E')
        ,(5,0,'Team F')
        ,(6,0,'Team G')
        ,(7,0,'Team H')
        ,(8,1,'Team I')
        ,(9,1,'Team J')
        ,(10,1,'Team K')
        ,(11,1,'Team L')
        ,(12,1,'Team M')
        ,(13,1,'Team N')
        ,(14,1,'Team O')
        ,(15,1,'Team P')
    ");

    }

    public function down(Schema $schema): void
    {

    }
}
