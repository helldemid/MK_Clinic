<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260227120000 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add editable price list hero and contact page content blocks';
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

		if (!$table->hasColumn('price_list_hero_desktop_image')) {
			$this->addSql('ALTER TABLE site_content_settings ADD price_list_hero_desktop_image VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('price_list_hero_mobile_image')) {
			$this->addSql('ALTER TABLE site_content_settings ADD price_list_hero_mobile_image VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('price_list_hero_eyebrow')) {
			$this->addSql('ALTER TABLE site_content_settings ADD price_list_hero_eyebrow VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('price_list_hero_title')) {
			$this->addSql('ALTER TABLE site_content_settings ADD price_list_hero_title VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('price_list_hero_lead')) {
			$this->addSql('ALTER TABLE site_content_settings ADD price_list_hero_lead LONGTEXT DEFAULT NULL');
		}

		if (!$table->hasColumn('contact_detail_blocks')) {
			$this->addSql('ALTER TABLE site_content_settings ADD contact_detail_blocks JSON DEFAULT NULL');
		}
		if (!$table->hasColumn('contact_icon_blocks')) {
			$this->addSql('ALTER TABLE site_content_settings ADD contact_icon_blocks JSON DEFAULT NULL');
		}

		$this->addSql("UPDATE site_content_settings SET price_list_hero_eyebrow = 'Science. Artistry. You.' WHERE price_list_hero_eyebrow IS NULL OR TRIM(price_list_hero_eyebrow) = ''");
		$this->addSql("UPDATE site_content_settings SET price_list_hero_title = 'Price List' WHERE price_list_hero_title IS NULL OR TRIM(price_list_hero_title) = ''");
		$this->addSql("UPDATE site_content_settings SET price_list_hero_lead = 'Discover our comprehensive range of aesthetic treatments, from advanced laser procedures to rejuvenating injectables — all delivered with precision and care.' WHERE price_list_hero_lead IS NULL OR TRIM(price_list_hero_lead) = ''");

		$contactDetailBlocksJson = $this->connection->quote((string) json_encode([
			[
				'title' => 'Our phone number:',
				'note' => "Mon–Fri 10:00–18:00\nSat 10:00–16:00\nSun closed",
				'position' => 0,
				'items' => [
					['icon' => 'res1.svg', 'label' => '+44-1702-844-959', 'url' => 'tel:+441702844959', 'position' => 0],
					['icon' => 'res1.svg', 'label' => '+44-7717-205-281', 'url' => 'tel:+447717205281', 'position' => 1],
				],
			],
			[
				'title' => 'Email:',
				'note' => '',
				'position' => 1,
				'items' => [
					['icon' => 'email.svg', 'label' => 'mkaestheticclinics@gmail.com', 'url' => 'mailto:mkaestheticclinics@gmail.com', 'position' => 0],
					['icon' => 'email.svg', 'label' => 'info@mk-aesthetic-clinic.com', 'url' => 'mailto:info@mk-aesthetic-clinic.com', 'position' => 1],
				],
			],
			[
				'title' => 'Address:',
				'note' => '',
				'position' => 2,
				'items' => [
					['icon' => 'location_grey.svg', 'label' => '135 St Clements, Broadway Leigh-on-Sea SS9 1PJ', 'url' => 'https://maps.app.goo.gl/842Yxuxdt8QUfx6k9', 'position' => 0],
				],
			],
		], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

		$contactIconBlocksJson = $this->connection->quote((string) json_encode([
			[
				'title' => 'We in social networks:',
				'position' => 0,
				'items' => [
					['icon' => 'instagram.svg', 'label' => 'Instagram', 'url' => 'https://www.instagram.com/mkaesthetic.clinic?igsh=c2g4NzRuOHh6MWNi', 'position' => 0],
					['icon' => 'facebook.svg', 'label' => 'Facebook', 'url' => 'https://www.facebook.com/share/1HT2JREY7s/', 'position' => 1],
					['icon' => 'whatsapp.svg', 'label' => 'WhatsApp', 'url' => 'https://wa.me/447717205281', 'position' => 2],
				],
			],
		], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

		$this->addSql("UPDATE site_content_settings SET contact_detail_blocks = {$contactDetailBlocksJson} WHERE contact_detail_blocks IS NULL OR JSON_LENGTH(contact_detail_blocks) = 0");
		$this->addSql("UPDATE site_content_settings SET contact_icon_blocks = {$contactIconBlocksJson} WHERE contact_icon_blocks IS NULL OR JSON_LENGTH(contact_icon_blocks) = 0");
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

		if ($table->hasColumn('price_list_hero_desktop_image')) {
			$this->addSql('ALTER TABLE site_content_settings DROP price_list_hero_desktop_image');
		}
		if ($table->hasColumn('price_list_hero_mobile_image')) {
			$this->addSql('ALTER TABLE site_content_settings DROP price_list_hero_mobile_image');
		}
		if ($table->hasColumn('price_list_hero_eyebrow')) {
			$this->addSql('ALTER TABLE site_content_settings DROP price_list_hero_eyebrow');
		}
		if ($table->hasColumn('price_list_hero_title')) {
			$this->addSql('ALTER TABLE site_content_settings DROP price_list_hero_title');
		}
		if ($table->hasColumn('price_list_hero_lead')) {
			$this->addSql('ALTER TABLE site_content_settings DROP price_list_hero_lead');
		}

		if ($table->hasColumn('contact_detail_blocks')) {
			$this->addSql('ALTER TABLE site_content_settings DROP contact_detail_blocks');
		}
		if ($table->hasColumn('contact_icon_blocks')) {
			$this->addSql('ALTER TABLE site_content_settings DROP contact_icon_blocks');
		}
	}
}
