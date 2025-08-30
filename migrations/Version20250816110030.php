<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250816110030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            "ALTER TABLE treatment_recover ADD COLUMN period VARCHAR(50) NOT NULL DEFAULT 'Minimal recover time'"
        );

        $this->addSql('ALTER TABLE treatments DROP time_id');
        $this->addSql('ALTER TABLE treatments ADD CONSTRAINT FK_4A48CE0DF0C700B8 FOREIGN KEY (recover_id) REFERENCES treatment_recover (id)');
        $this->addSql('ALTER TABLE treatments ADD CONSTRAINT FK_4A48CE0DD614C7E7 FOREIGN KEY (price_id) REFERENCES treatment_price (id)');
        $this->addSql('CREATE INDEX IDX_4A48CE0DF0C700B8 ON treatments (recover_id)');
        $this->addSql('CREATE INDEX IDX_4A48CE0DD614C7E7 ON treatments (price_id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE treatments DROP FOREIGN KEY FK_4A48CE0DF0C700B8');
        $this->addSql('ALTER TABLE treatments DROP FOREIGN KEY FK_4A48CE0DD614C7E7');
        $this->addSql('DROP INDEX IDX_4A48CE0DF0C700B8 ON treatments');
        $this->addSql('DROP INDEX IDX_4A48CE0DD614C7E7 ON treatments');
        $this->addSql('ALTER TABLE treatments ADD time_id INT NOT NULL');
        $this->addSql('ALTER TABLE treatment_recover DROP period');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }
}
