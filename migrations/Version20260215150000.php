<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\Migrations\AbstractMigration;

final class Version20260215150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align help_sections schema for EasyAdmin WYSIWYG editing';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on "mysql".'
        );

        if (!$schema->hasTable('help_sections')) {
            $this->addSql('CREATE TABLE help_sections (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(100) NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, position INT NOT NULL, UNIQUE INDEX UNIQ_A4ADC798989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE INDEX idx_help_sections_position ON help_sections (position)');

            return;
        }

        $table = $schema->getTable('help_sections');
        if ($table->hasColumn('content')) {
            $this->addSql('ALTER TABLE help_sections MODIFY content LONGTEXT NOT NULL');
        }

        if (!$this->hasIndexForColumns($table, ['position'])) {
            $this->addSql('CREATE INDEX idx_help_sections_position ON help_sections (position)');
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
        if ($table->hasIndex('idx_help_sections_position')) {
            $this->addSql('DROP INDEX idx_help_sections_position ON help_sections');
        }
    }

    private function hasIndexForColumns(Table $table, array $columns): bool
    {
        $expectedColumns = array_map('strtolower', $columns);

        foreach ($table->getIndexes() as $index) {
            $indexColumns = array_map('strtolower', $index->getColumns());
            if ($indexColumns === $expectedColumns) {
                return true;
            }
        }

        return false;
    }
}
