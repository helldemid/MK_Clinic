<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250908200407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE treatments ADD treatment_time TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE treatments ADD CONSTRAINT FK_4A48CE0D5EEADD3B FOREIGN KEY (time_id) REFERENCES treatment_time (id)');
        $this->addSql('CREATE INDEX IDX_4A48CE0D5EEADD3B ON treatments (time_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE treatments DROP FOREIGN KEY FK_4A48CE0D5EEADD3B');
        $this->addSql('DROP INDEX IDX_4A48CE0D5EEADD3B ON treatments');
    }
}
