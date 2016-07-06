<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160705230655 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bill_plan DROP FOREIGN KEY FK_2E4533D7533A79E');
        $this->addSql('ALTER TABLE bill_plan ADD CONSTRAINT FK_2E4533D7533A79E FOREIGN KEY (bill_plan_type_id) REFERENCES bill_plan_type (id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bill_plan DROP FOREIGN KEY FK_2E4533D7533A79E');
        $this->addSql('ALTER TABLE bill_plan ADD CONSTRAINT FK_2E4533D7533A79E FOREIGN KEY (bill_plan_type_id) REFERENCES bill_plan (id) ON DELETE SET NULL');
    }
}
