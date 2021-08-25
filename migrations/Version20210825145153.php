<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210825145153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE TABLE survey (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        // $this->addSql('ALTER TABLE questions ADD survey_id INT DEFAULT NULL');
        // $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        // $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        // $this->addSql('CREATE INDEX IDX_8ADC54D5C54C8C93 ON questions (type_id)');
        // $this->addSql('CREATE INDEX IDX_8ADC54D5B3FE509D ON questions (survey_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5B3FE509D');
        $this->addSql('DROP TABLE survey');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5C54C8C93');
        $this->addSql('DROP INDEX IDX_8ADC54D5C54C8C93 ON questions');
        $this->addSql('DROP INDEX IDX_8ADC54D5B3FE509D ON questions');
        $this->addSql('ALTER TABLE questions DROP survey_id');
    }
}