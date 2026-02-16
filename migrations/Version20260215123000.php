<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260215123000 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add promo price column to price_cell';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE price_cell ADD promo_value NUMERIC(10, 2) DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE price_cell DROP promo_value');
	}
}

