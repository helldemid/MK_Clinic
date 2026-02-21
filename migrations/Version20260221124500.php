<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260221124500 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Create site_content_settings table for promo and hero image management';
	}

	public function up(Schema $schema): void
	{
		$this->abortIf(
			$this->connection->getDatabasePlatform()->getName() !== 'mysql',
			'Migration can only be executed safely on "mysql".'
		);

		$this->addSql('CREATE TABLE site_content_settings (id INT AUTO_INCREMENT NOT NULL, promo_items JSON DEFAULT NULL, hero_desktop_image VARCHAR(255) DEFAULT NULL, hero_mobile_image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
	}

	public function down(Schema $schema): void
	{
		$this->abortIf(
			$this->connection->getDatabasePlatform()->getName() !== 'mysql',
			'Migration can only be executed safely on "mysql".'
		);

		$this->addSql('DROP TABLE site_content_settings');
	}
}
