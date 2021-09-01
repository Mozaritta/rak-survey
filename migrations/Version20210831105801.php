<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210831105801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Answers Entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answers (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, client_id INT NOT NULL, answer VARCHAR(255) NOT NULL, INDEX IDX_50D0C6061E27F6BF (question_id), INDEX IDX_50D0C60619EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C6061E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C60619EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_AD5F9BFC5FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE answers');
        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_AD5F9BFC5FF69B7D');
    }
}
