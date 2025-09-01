<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250831113636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointment_request (id INT AUTO_INCREMENT NOT NULL, treatment_id INT DEFAULT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) NOT NULL, question LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_AAB4BDB7471C0366 (treatment_id), INDEX IDX_AAB4BDB7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointment_request ADD CONSTRAINT FK_AAB4BDB7471C0366 FOREIGN KEY (treatment_id) REFERENCES treatments (id)');
        $this->addSql('ALTER TABLE appointment_request ADD CONSTRAINT FK_AAB4BDB7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment_request DROP FOREIGN KEY FK_AAB4BDB7471C0366');
        $this->addSql('ALTER TABLE appointment_request DROP FOREIGN KEY FK_AAB4BDB7A76ED395');
        $this->addSql('DROP TABLE appointment_request');
    }
}
