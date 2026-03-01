<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260301120000 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add editable hero highlights and CTA texts to site content settings';
	}

	public function up(Schema $schema): void
	{
		$this->abortIf(
			$this->connection->getDatabasePlatform()->getName() !== 'mysql',
			'Migration can only be executed safely on "mysql".'
		);

		if (!$schema->hasTable('site_content_settings')) {
			return;
		}

		$table = $schema->getTable('site_content_settings');

		if (!$table->hasColumn('price_list_hero_highlight1')) {
			$this->addSql('ALTER TABLE site_content_settings ADD price_list_hero_highlight1 VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('price_list_hero_highlight2')) {
			$this->addSql('ALTER TABLE site_content_settings ADD price_list_hero_highlight2 VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('price_list_hero_highlight3')) {
			$this->addSql('ALTER TABLE site_content_settings ADD price_list_hero_highlight3 VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('consultation_eyebrow')) {
			$this->addSql('ALTER TABLE site_content_settings ADD consultation_eyebrow VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('consultation_button_label')) {
			$this->addSql('ALTER TABLE site_content_settings ADD consultation_button_label VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('booking_eyebrow')) {
			$this->addSql('ALTER TABLE site_content_settings ADD booking_eyebrow VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('booking_button_label')) {
			$this->addSql('ALTER TABLE site_content_settings ADD booking_button_label VARCHAR(255) DEFAULT NULL');
		}

		$this->addSql("UPDATE site_content_settings SET price_list_hero_highlight1 = 'Course discounts available' WHERE price_list_hero_highlight1 IS NULL OR TRIM(price_list_hero_highlight1) = ''");
		$this->addSql("UPDATE site_content_settings SET price_list_hero_highlight2 = 'Free consultations' WHERE price_list_hero_highlight2 IS NULL OR TRIM(price_list_hero_highlight2) = ''");
		$this->addSql("UPDATE site_content_settings SET price_list_hero_highlight3 = 'Flexible payment options' WHERE price_list_hero_highlight3 IS NULL OR TRIM(price_list_hero_highlight3) = ''");
		$this->addSql("UPDATE site_content_settings SET consultation_eyebrow = 'About consultation & expert guidance' WHERE consultation_eyebrow IS NULL OR TRIM(consultation_eyebrow) = ''");
		$this->addSql("UPDATE site_content_settings SET consultation_button_label = 'Get now' WHERE consultation_button_label IS NULL OR TRIM(consultation_button_label) = ''");
		$this->addSql("UPDATE site_content_settings SET booking_eyebrow = 'About bookings & what we offer' WHERE booking_eyebrow IS NULL OR TRIM(booking_eyebrow) = ''");
		$this->addSql("UPDATE site_content_settings SET booking_button_label = 'Book now' WHERE booking_button_label IS NULL OR TRIM(booking_button_label) = ''");
	}

	public function down(Schema $schema): void
	{
		$this->abortIf(
			$this->connection->getDatabasePlatform()->getName() !== 'mysql',
			'Migration can only be executed safely on "mysql".'
		);

		if (!$schema->hasTable('site_content_settings')) {
			return;
		}

		$table = $schema->getTable('site_content_settings');

		if ($table->hasColumn('price_list_hero_highlight1')) {
			$this->addSql('ALTER TABLE site_content_settings DROP price_list_hero_highlight1');
		}
		if ($table->hasColumn('price_list_hero_highlight2')) {
			$this->addSql('ALTER TABLE site_content_settings DROP price_list_hero_highlight2');
		}
		if ($table->hasColumn('price_list_hero_highlight3')) {
			$this->addSql('ALTER TABLE site_content_settings DROP price_list_hero_highlight3');
		}
		if ($table->hasColumn('consultation_eyebrow')) {
			$this->addSql('ALTER TABLE site_content_settings DROP consultation_eyebrow');
		}
		if ($table->hasColumn('consultation_button_label')) {
			$this->addSql('ALTER TABLE site_content_settings DROP consultation_button_label');
		}
		if ($table->hasColumn('booking_eyebrow')) {
			$this->addSql('ALTER TABLE site_content_settings DROP booking_eyebrow');
		}
		if ($table->hasColumn('booking_button_label')) {
			$this->addSql('ALTER TABLE site_content_settings DROP booking_button_label');
		}
	}
}
