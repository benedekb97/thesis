<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20211110100945 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE designs ADD background_color JSON DEFAULT NULL, ADD colors JSON DEFAULT NULL, ADD canvas_height DOUBLE PRECISION DEFAULT NULL, ADD canvas_width DOUBLE PRECISION DEFAULT NULL, ADD horizontal_offset DOUBLE PRECISION DEFAULT NULL, ADD vertical_offset DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE machines ADD current_design INT DEFAULT NULL, ADD design_count INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE designs DROP background_color, DROP colors, DROP canvas_height, DROP canvas_width, DROP horizontal_offset, DROP vertical_offset');
        $this->addSql('ALTER TABLE machines DROP current_design, DROP design_count');
    }
}
