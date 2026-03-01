<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260225113000 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Extend site_content_settings with homepage blocks and footer configuration';
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

		if (!$table->hasColumn('hero_headline_line1')) {
			$this->addSql('ALTER TABLE site_content_settings ADD hero_headline_line1 VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('hero_headline_line2')) {
			$this->addSql('ALTER TABLE site_content_settings ADD hero_headline_line2 VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('hero_subheadline')) {
			$this->addSql('ALTER TABLE site_content_settings ADD hero_subheadline LONGTEXT DEFAULT NULL');
		}

		if (!$table->hasColumn('our_ethos_title')) {
			$this->addSql('ALTER TABLE site_content_settings ADD our_ethos_title VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('our_ethos_body')) {
			$this->addSql('ALTER TABLE site_content_settings ADD our_ethos_body LONGTEXT DEFAULT NULL');
		}
		if (!$table->hasColumn('our_ethos_image')) {
			$this->addSql('ALTER TABLE site_content_settings ADD our_ethos_image VARCHAR(255) DEFAULT NULL');
		}

		if (!$table->hasColumn('our_story_title')) {
			$this->addSql('ALTER TABLE site_content_settings ADD our_story_title VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('our_story_body')) {
			$this->addSql('ALTER TABLE site_content_settings ADD our_story_body LONGTEXT DEFAULT NULL');
		}
		if (!$table->hasColumn('our_story_image')) {
			$this->addSql('ALTER TABLE site_content_settings ADD our_story_image VARCHAR(255) DEFAULT NULL');
		}

		if (!$table->hasColumn('consultation_title')) {
			$this->addSql('ALTER TABLE site_content_settings ADD consultation_title VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('consultation_body')) {
			$this->addSql('ALTER TABLE site_content_settings ADD consultation_body LONGTEXT DEFAULT NULL');
		}
		if (!$table->hasColumn('consultation_image')) {
			$this->addSql('ALTER TABLE site_content_settings ADD consultation_image VARCHAR(255) DEFAULT NULL');
		}

		if (!$table->hasColumn('booking_title')) {
			$this->addSql('ALTER TABLE site_content_settings ADD booking_title VARCHAR(255) DEFAULT NULL');
		}
		if (!$table->hasColumn('booking_body')) {
			$this->addSql('ALTER TABLE site_content_settings ADD booking_body LONGTEXT DEFAULT NULL');
		}
		if (!$table->hasColumn('booking_image')) {
			$this->addSql('ALTER TABLE site_content_settings ADD booking_image VARCHAR(255) DEFAULT NULL');
		}

		if (!$table->hasColumn('footer_description')) {
			$this->addSql('ALTER TABLE site_content_settings ADD footer_description LONGTEXT DEFAULT NULL');
		}
		if (!$table->hasColumn('footer_social_links')) {
			$this->addSql('ALTER TABLE site_content_settings ADD footer_social_links JSON DEFAULT NULL');
		}
		if (!$table->hasColumn('footer_customer_care_links')) {
			$this->addSql('ALTER TABLE site_content_settings ADD footer_customer_care_links JSON DEFAULT NULL');
		}
		if (!$table->hasColumn('footer_company_legal_links')) {
			$this->addSql('ALTER TABLE site_content_settings ADD footer_company_legal_links JSON DEFAULT NULL');
		}

		$this->addSql("UPDATE site_content_settings SET hero_headline_line1 = 'SCIENCE. ARTISTRY. YOU.' WHERE hero_headline_line1 IS NULL OR TRIM(hero_headline_line1) = ''");
		$this->addSql("UPDATE site_content_settings SET hero_headline_line2 = 'M.K. AESTHETIC CLINIC' WHERE hero_headline_line2 IS NULL OR TRIM(hero_headline_line2) = ''");
		$this->addSql("UPDATE site_content_settings SET hero_subheadline = 'Experience a new era of aesthetics — restore youth, refine beauty, reveal confidence' WHERE hero_subheadline IS NULL OR TRIM(hero_subheadline) = ''");

		$this->addSql("UPDATE site_content_settings SET our_ethos_title = 'Where Science Meets Artistry' WHERE our_ethos_title IS NULL OR TRIM(our_ethos_title) = ''");
		$this->addSql("UPDATE site_content_settings SET our_ethos_body = 'Driven by a deep commitment to advancing every aspect of skin health, M.K. Aesthetic Clinic was founded on Broadway in Leigh-on-Sea, where we specialise in aesthetic medicine and anti-ageing treatments.\\n\\nLed by a team of elite practitioners, the clinic is renowned for its minimalist and integrated approach — combining advanced techniques and complementary modalities to achieve results that are always natural-looking, balanced, and refined.\\n\\nAt M.K. Aesthetic Clinic, our philosophy is simple: to deliver youthful, rejuvenated radiance through cutting-edge, clinically proven treatments, grounded in science and guided by artistry.\\n\\nAt the heart of our ethos lies a simple promise: to enhance, never to change — and to reveal your most authentic, naturally beautiful self.' WHERE our_ethos_body IS NULL OR TRIM(our_ethos_body) = ''");

		$this->addSql("UPDATE site_content_settings SET our_story_title = 'Where It All Began' WHERE our_story_title IS NULL OR TRIM(our_story_title) = ''");
		$this->addSql("UPDATE site_content_settings SET our_story_body = 'Situated on Broadway in Leigh-on-Sea, M.K. Aesthetic Clinic was founded with a clear vision — to enhance natural beauty and restore confidence through subtle, refined results.\\n\\nWe specialise in advanced skincare and anti-ageing treatments, blending the latest innovations in aesthetic medicine with a highly personalised approach. Every treatment is thoughtfully designed to harmonise with your unique features, ensuring results that are elegant, balanced, and authentically you.\\n\\nOur team of experienced and internationally trained practitioners are dedicated to delivering the highest standards of safety and care. Using only clinically proven techniques and premium medical-grade products, we provide outcomes that are both visible and lasting.\\n\\nFrom bespoke skin treatments to advanced injectable procedures, we are committed to helping you feel confident, refreshed, and naturally radiant.' WHERE our_story_body IS NULL OR TRIM(our_story_body) = ''");

		$this->addSql("UPDATE site_content_settings SET consultation_title = 'Book your consultation' WHERE consultation_title IS NULL OR TRIM(consultation_title) = ''");
		$this->addSql("UPDATE site_content_settings SET consultation_body = 'Get personalized advice and clear answers to your questions. Reserve your consultation and take the first step toward informed, confident decisions.' WHERE consultation_body IS NULL OR TRIM(consultation_body) = ''");

		$this->addSql("UPDATE site_content_settings SET booking_title = 'Book your visit' WHERE booking_title IS NULL OR TRIM(booking_title) = ''");
		$this->addSql("UPDATE site_content_settings SET booking_body = 'Reserve a spot and enjoy a seamless experience tailored to your needs. From personal consultations to exclusive sessions — secure your time with us and be sure you won’t miss out.' WHERE booking_body IS NULL OR TRIM(booking_body) = ''");

		$this->addSql("UPDATE site_content_settings SET footer_description = 'Personalised aesthetic treatments with a focus on safety, comfort and natural results. Every treatment is tailored to you, using advanced techniques to enhance your natural beauty while keeping results subtle, balanced and refined.' WHERE footer_description IS NULL OR TRIM(footer_description) = ''");

		$this->addSql("UPDATE site_content_settings SET footer_social_links = '[{\"icon\":\"fa-brands fa-instagram\",\"url\":\"https://www.instagram.com/mkaesthetic.clinic?igsh=c2g4NzRuOHh6MWNi\",\"label\":\"Instagram\",\"position\":0},{\"icon\":\"fa-brands fa-facebook-f\",\"url\":\"https://www.facebook.com/share/1HT2JREY7s/\",\"label\":\"Facebook\",\"position\":1},{\"icon\":\"fa-brands fa-whatsapp\",\"url\":\"https://wa.me/447717205281\",\"label\":\"WhatsApp\",\"position\":2}]' WHERE footer_social_links IS NULL OR JSON_LENGTH(footer_social_links) = 0");
		$this->addSql("UPDATE site_content_settings SET footer_customer_care_links = '[{\"sourceKey\":\"help:rewards-programme\",\"label\":\"Rewards Programme\",\"position\":0},{\"sourceKey\":\"help:cancellation-policy\",\"label\":\"Cancellation Policy\",\"position\":1},{\"sourceKey\":\"help:complaints-policy\",\"label\":\"Complaints Policy\",\"position\":2},{\"sourceKey\":\"help:chaperone-policy\",\"label\":\"Chaperone Policy\",\"position\":3},{\"sourceKey\":\"help:faqs\",\"label\":\"FAQs\",\"position\":4}]' WHERE footer_customer_care_links IS NULL OR JSON_LENGTH(footer_customer_care_links) = 0");
		$this->addSql("UPDATE site_content_settings SET footer_company_legal_links = '[{\"sourceKey\":\"help:access-statement\",\"label\":\"Access Statement\",\"position\":0},{\"sourceKey\":\"help:patient-notice\",\"label\":\"Patient Notice\",\"position\":1},{\"sourceKey\":\"help:privacy-policy\",\"label\":\"Privacy Policy\",\"position\":2},{\"sourceKey\":\"route:contacts\",\"label\":\"Contact Us\",\"position\":3},{\"sourceKey\":\"help:career\",\"label\":\"Career\",\"position\":4}]' WHERE footer_company_legal_links IS NULL OR JSON_LENGTH(footer_company_legal_links) = 0");
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

		if ($table->hasColumn('hero_headline_line1')) {
			$this->addSql('ALTER TABLE site_content_settings DROP hero_headline_line1');
		}
		if ($table->hasColumn('hero_headline_line2')) {
			$this->addSql('ALTER TABLE site_content_settings DROP hero_headline_line2');
		}
		if ($table->hasColumn('hero_subheadline')) {
			$this->addSql('ALTER TABLE site_content_settings DROP hero_subheadline');
		}

		if ($table->hasColumn('our_ethos_title')) {
			$this->addSql('ALTER TABLE site_content_settings DROP our_ethos_title');
		}
		if ($table->hasColumn('our_ethos_body')) {
			$this->addSql('ALTER TABLE site_content_settings DROP our_ethos_body');
		}
		if ($table->hasColumn('our_ethos_image')) {
			$this->addSql('ALTER TABLE site_content_settings DROP our_ethos_image');
		}

		if ($table->hasColumn('our_story_title')) {
			$this->addSql('ALTER TABLE site_content_settings DROP our_story_title');
		}
		if ($table->hasColumn('our_story_body')) {
			$this->addSql('ALTER TABLE site_content_settings DROP our_story_body');
		}
		if ($table->hasColumn('our_story_image')) {
			$this->addSql('ALTER TABLE site_content_settings DROP our_story_image');
		}

		if ($table->hasColumn('consultation_title')) {
			$this->addSql('ALTER TABLE site_content_settings DROP consultation_title');
		}
		if ($table->hasColumn('consultation_body')) {
			$this->addSql('ALTER TABLE site_content_settings DROP consultation_body');
		}
		if ($table->hasColumn('consultation_image')) {
			$this->addSql('ALTER TABLE site_content_settings DROP consultation_image');
		}

		if ($table->hasColumn('booking_title')) {
			$this->addSql('ALTER TABLE site_content_settings DROP booking_title');
		}
		if ($table->hasColumn('booking_body')) {
			$this->addSql('ALTER TABLE site_content_settings DROP booking_body');
		}
		if ($table->hasColumn('booking_image')) {
			$this->addSql('ALTER TABLE site_content_settings DROP booking_image');
		}

		if ($table->hasColumn('footer_description')) {
			$this->addSql('ALTER TABLE site_content_settings DROP footer_description');
		}
		if ($table->hasColumn('footer_social_links')) {
			$this->addSql('ALTER TABLE site_content_settings DROP footer_social_links');
		}
		if ($table->hasColumn('footer_customer_care_links')) {
			$this->addSql('ALTER TABLE site_content_settings DROP footer_customer_care_links');
		}
		if ($table->hasColumn('footer_company_legal_links')) {
			$this->addSql('ALTER TABLE site_content_settings DROP footer_company_legal_links');
		}
	}
}
