<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250906230712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointment_payment (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, method VARCHAR(20) NOT NULL, amount NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointment ADD payment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8444C3A3BB FOREIGN KEY (payment_id) REFERENCES appointment_payment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE38F8444C3A3BB ON appointment (payment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8444C3A3BB');
        $this->addSql('DROP TABLE appointment_payment');
        $this->addSql('DROP INDEX UNIQ_FE38F8444C3A3BB ON appointment');
        $this->addSql('ALTER TABLE appointment DROP payment_id');
    }
}
