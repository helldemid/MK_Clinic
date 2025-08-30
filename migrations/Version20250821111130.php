<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250821111130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email_verification_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, email VARCHAR(90) DEFAULT NULL, code VARCHAR(32) NOT NULL, updated_at DATETIME NOT NULL, action INT NOT NULL COMMENT \'0 - undefined, 1 - forgot password, 2 - create account, 3 - change email, 4 - change password\', UNIQUE INDEX UNIQ_EE9588C8E7927C74 (email), INDEX IDX_EE9588C8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email_verification_request ADD CONSTRAINT FK_EE9588C8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_verification_request DROP FOREIGN KEY FK_EE9588C8A76ED395');
        $this->addSql('DROP TABLE email_verification_request');
    }
}
