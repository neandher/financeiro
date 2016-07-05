<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160705134210 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bill ADD bill_status_id INT DEFAULT NULL, ADD bill_plan_id INT DEFAULT NULL, ADD bill_type_id INT DEFAULT NULL, ADD bank_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E387190E55 FOREIGN KEY (bill_status_id) REFERENCES bill_status (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E31C5A3684 FOREIGN KEY (bill_plan_id) REFERENCES bill_plan (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E3318FB88C FOREIGN KEY (bill_type_id) REFERENCES bill_type (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E311C8FB41 FOREIGN KEY (bank_id) REFERENCES bank (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_7A2119E387190E55 ON bill (bill_status_id)');
        $this->addSql('CREATE INDEX IDX_7A2119E31C5A3684 ON bill (bill_plan_id)');
        $this->addSql('CREATE INDEX IDX_7A2119E3318FB88C ON bill (bill_type_id)');
        $this->addSql('CREATE INDEX IDX_7A2119E311C8FB41 ON bill (bank_id)');
        $this->addSql('ALTER TABLE bill_installments ADD payment_method_id INT DEFAULT NULL, ADD bill_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bill_installments ADD CONSTRAINT FK_805065895AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_method (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bill_installments ADD CONSTRAINT FK_805065891A8C12F5 FOREIGN KEY (bill_id) REFERENCES bill (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_805065895AA1164F ON bill_installments (payment_method_id)');
        $this->addSql('CREATE INDEX IDX_805065891A8C12F5 ON bill_installments (bill_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E387190E55');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E31C5A3684');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E3318FB88C');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E311C8FB41');
        $this->addSql('DROP INDEX IDX_7A2119E387190E55 ON bill');
        $this->addSql('DROP INDEX IDX_7A2119E31C5A3684 ON bill');
        $this->addSql('DROP INDEX IDX_7A2119E3318FB88C ON bill');
        $this->addSql('DROP INDEX IDX_7A2119E311C8FB41 ON bill');
        $this->addSql('ALTER TABLE bill DROP bill_status_id, DROP bill_plan_id, DROP bill_type_id, DROP bank_id');
        $this->addSql('ALTER TABLE bill_installments DROP FOREIGN KEY FK_805065895AA1164F');
        $this->addSql('ALTER TABLE bill_installments DROP FOREIGN KEY FK_805065891A8C12F5');
        $this->addSql('DROP INDEX IDX_805065895AA1164F ON bill_installments');
        $this->addSql('DROP INDEX IDX_805065891A8C12F5 ON bill_installments');
        $this->addSql('ALTER TABLE bill_installments DROP payment_method_id, DROP bill_id');
    }
}
