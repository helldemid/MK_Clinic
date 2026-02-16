<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215111450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE price_cell RENAME INDEX idx_price_cell_row TO IDX_B6D5755883A269F2');
        $this->addSql('ALTER TABLE price_cell RENAME INDEX idx_price_cell_column TO IDX_B6D57558BE8E8ED5');
        $this->addSql('DROP INDEX idx_price_column_position ON price_column');
        $this->addSql('ALTER TABLE price_column RENAME INDEX idx_price_column_section TO IDX_30CA2F2ED823E37A');
        $this->addSql('DROP INDEX idx_price_row_position ON price_row');
        $this->addSql('ALTER TABLE price_row RENAME INDEX idx_price_row_section TO IDX_D15CFD54D823E37A');
        $this->addSql('DROP INDEX idx_price_section_position ON price_section');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX idx_price_column_position ON price_column (position)');
        $this->addSql('ALTER TABLE price_column RENAME INDEX idx_30ca2f2ed823e37a TO idx_price_column_section');
        $this->addSql('CREATE INDEX idx_price_row_position ON price_row (position)');
        $this->addSql('ALTER TABLE price_row RENAME INDEX idx_d15cfd54d823e37a TO idx_price_row_section');
        $this->addSql('CREATE INDEX idx_price_section_position ON price_section (position)');
        $this->addSql('ALTER TABLE price_cell RENAME INDEX idx_b6d57558be8e8ed5 TO idx_price_cell_column');
        $this->addSql('ALTER TABLE price_cell RENAME INDEX idx_b6d5755883a269f2 TO idx_price_cell_row');
    }
}
