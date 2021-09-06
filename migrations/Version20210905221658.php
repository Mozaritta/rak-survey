<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210905221658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add answered to user table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5B3FE509D');
        $this->addSql('DROP INDEX idx_8adc54d5b3fe509d ON questions');
        $this->addSql('CREATE INDEX IDX_8ADC54D5D823E37A ON questions (section_id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5B3FE509D FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_AD5F9BFC5FF69B7D');
        $this->addSql('DROP INDEX idx_ad5f9bfc5ff69b7d ON section');
        $this->addSql('CREATE INDEX IDX_2D737AEF5FF69B7D ON section (form_id)');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_AD5F9BFC5FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE user ADD answered TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5D823E37A');
        $this->addSql('DROP INDEX idx_8adc54d5d823e37a ON questions');
        $this->addSql('CREATE INDEX IDX_8ADC54D5B3FE509D ON questions (section_id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEF5FF69B7D');
        $this->addSql('DROP INDEX idx_2d737aef5ff69b7d ON section');
        $this->addSql('CREATE INDEX IDX_AD5F9BFC5FF69B7D ON section (form_id)');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEF5FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE user DROP answered');
    }
}