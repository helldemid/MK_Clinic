<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260221120000 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add faq_section flag to help_sections';
	}

	public function up(Schema $schema): void
	{
		$this->abortIf(
			$this->connection->getDatabasePlatform()->getName() !== 'mysql',
			'Migration can only be executed safely on "mysql".'
		);

		if (!$schema->hasTable('help_sections')) {
			return;
		}

		$table = $schema->getTable('help_sections');
		if (!$table->hasColumn('faq_section')) {
			$this->addSql('ALTER TABLE help_sections ADD faq_section TINYINT(1) NOT NULL DEFAULT 0');
		}
	}

	public function down(Schema $schema): void
	{
		$this->abortIf(
			$this->connection->getDatabasePlatform()->getName() !== 'mysql',
			'Migration can only be executed safely on "mysql".'
		);

		if (!$schema->hasTable('help_sections')) {
			return;
		}

		$table = $schema->getTable('help_sections');
		if ($table->hasColumn('faq_section')) {
			$this->addSql('ALTER TABLE help_sections DROP faq_section');
		}
	}
}
