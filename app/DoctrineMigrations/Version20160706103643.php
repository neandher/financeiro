<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160706103643 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bill_plan ADD bill_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bill_plan ADD CONSTRAINT FK_2E4533D7318FB88C FOREIGN KEY (bill_type_id) REFERENCES bill_type (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2E4533D7318FB88C ON bill_plan (bill_type_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bill_plan DROP FOREIGN KEY FK_2E4533D7318FB88C');
        $this->addSql('DROP INDEX IDX_2E4533D7318FB88C ON bill_plan');
        $this->addSql('ALTER TABLE bill_plan DROP bill_type_id');
    }
}
