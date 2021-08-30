<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210830080054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ad fields to user';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD full_name VARCHAR(255) NOT NULL, ADD cin VARCHAR(50) NOT NULL, ADD phone VARCHAR(20) DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD agency VARCHAR(255) DEFAULT NULL, ADD division VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP first_name, DROP last_name, DROP full_name, DROP cin, DROP phone, DROP address, DROP agency, DROP division, DROP created_at, DROP updated_at');
    }
}