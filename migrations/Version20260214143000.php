<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260214143000 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Create price list tables: price_section, price_column, price_row, price_cell';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('CREATE TABLE price_section (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, nav_label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, note LONGTEXT DEFAULT NULL, position INT NOT NULL, UNIQUE INDEX uniq_price_section_slug (slug), INDEX idx_price_section_position (position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('CREATE TABLE price_column (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, label VARCHAR(255) NOT NULL, position INT NOT NULL, INDEX idx_price_column_section (section_id), INDEX idx_price_column_position (position), UNIQUE INDEX uniq_price_column_section_position (section_id, position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('CREATE TABLE price_row (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, title VARCHAR(255) NOT NULL, position INT NOT NULL, INDEX idx_price_row_section (section_id), INDEX idx_price_row_position (position), UNIQUE INDEX uniq_price_row_section_position (section_id, position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('CREATE TABLE price_cell (id INT AUTO_INCREMENT NOT NULL, row_id INT NOT NULL, column_id INT NOT NULL, value NUMERIC(10, 2) DEFAULT NULL, INDEX idx_price_cell_row (row_id), INDEX idx_price_cell_column (column_id), UNIQUE INDEX uniq_price_cell_row_column (row_id, column_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('ALTER TABLE price_column ADD CONSTRAINT fk_price_column_section FOREIGN KEY (section_id) REFERENCES price_section (id) ON DELETE CASCADE');
		$this->addSql('ALTER TABLE price_row ADD CONSTRAINT fk_price_row_section FOREIGN KEY (section_id) REFERENCES price_section (id) ON DELETE CASCADE');
		$this->addSql('ALTER TABLE price_cell ADD CONSTRAINT fk_price_cell_row FOREIGN KEY (row_id) REFERENCES price_row (id) ON DELETE CASCADE');
		$this->addSql('ALTER TABLE price_cell ADD CONSTRAINT fk_price_cell_column FOREIGN KEY (column_id) REFERENCES price_column (id) ON DELETE CASCADE');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE price_cell DROP FOREIGN KEY fk_price_cell_row');
		$this->addSql('ALTER TABLE price_cell DROP FOREIGN KEY fk_price_cell_column');
		$this->addSql('ALTER TABLE price_row DROP FOREIGN KEY fk_price_row_section');
		$this->addSql('ALTER TABLE price_column DROP FOREIGN KEY fk_price_column_section');
		$this->addSql('DROP TABLE price_cell');
		$this->addSql('DROP TABLE price_row');
		$this->addSql('DROP TABLE price_column');
		$this->addSql('DROP TABLE price_section');
	}
}
