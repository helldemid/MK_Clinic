<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260217100000 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Create help_faq table for structured FAQ editing';
	}

	public function up(Schema $schema): void
	{
		$this->abortIf(
			$this->connection->getDatabasePlatform()->getName() !== 'mysql',
			'Migration can only be executed safely on "mysql".'
		);

		$this->addSql('ALTER TABLE help_sections MODIFY content LONGTEXT DEFAULT NULL');
		$this->addSql('CREATE TABLE help_faq (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, question VARCHAR(255) NOT NULL, answer LONGTEXT DEFAULT NULL, position INT NOT NULL, INDEX IDX_26506A8BD823E37A (section_id), INDEX idx_help_faq_position (position), UNIQUE INDEX uniq_help_faq_section_position (section_id, position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('ALTER TABLE help_faq ADD CONSTRAINT FK_26506A8BD823E37A FOREIGN KEY (section_id) REFERENCES help_sections (id) ON DELETE CASCADE');
	}

	public function down(Schema $schema): void
	{
		$this->abortIf(
			$this->connection->getDatabasePlatform()->getName() !== 'mysql',
			'Migration can only be executed safely on "mysql".'
		);

		$this->addSql('UPDATE help_sections SET content = \'\' WHERE content IS NULL');
		$this->addSql('ALTER TABLE help_sections MODIFY content LONGTEXT NOT NULL');
		$this->addSql('DROP TABLE help_faq');
	}
}
