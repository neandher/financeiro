<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160711113020 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bank (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, agency VARCHAR(255) NOT NULL, account VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill (id INT AUTO_INCREMENT NOT NULL, bill_status_id INT DEFAULT NULL, bill_plan_id INT DEFAULT NULL, bill_category_id INT DEFAULT NULL, bank_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, amount VARCHAR(255) DEFAULT NULL, note VARCHAR(255) DEFAULT NULL, createdAt DATE NOT NULL, INDEX IDX_7A2119E387190E55 (bill_status_id), INDEX IDX_7A2119E31C5A3684 (bill_plan_id), INDEX IDX_7A2119E3635925EF (bill_category_id), INDEX IDX_7A2119E311C8FB41 (bank_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_category (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, referency VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_installments (id INT AUTO_INCREMENT NOT NULL, payment_method_id INT DEFAULT NULL, bill_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, dueDateAt DATE NOT NULL, paymentDateAt DATE DEFAULT NULL, amount VARCHAR(255) NOT NULL, amountPaid VARCHAR(255) DEFAULT NULL, INDEX IDX_805065895AA1164F (payment_method_id), INDEX IDX_805065891A8C12F5 (bill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_plan (id INT AUTO_INCREMENT NOT NULL, bill_plan_category_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_2E4533D7E7F74BB7 (bill_plan_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_plan_category (id INT AUTO_INCREMENT NOT NULL, bill_category_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_783D1A57635925EF (bill_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_status (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, referency VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_method (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E387190E55 FOREIGN KEY (bill_status_id) REFERENCES bill_status (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E31C5A3684 FOREIGN KEY (bill_plan_id) REFERENCES bill_plan (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E3635925EF FOREIGN KEY (bill_category_id) REFERENCES bill_category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E311C8FB41 FOREIGN KEY (bank_id) REFERENCES bank (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bill_installments ADD CONSTRAINT FK_805065895AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_method (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bill_installments ADD CONSTRAINT FK_805065891A8C12F5 FOREIGN KEY (bill_id) REFERENCES bill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bill_plan ADD CONSTRAINT FK_2E4533D7E7F74BB7 FOREIGN KEY (bill_plan_category_id) REFERENCES bill_plan_category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bill_plan_category ADD CONSTRAINT FK_783D1A57635925EF FOREIGN KEY (bill_category_id) REFERENCES bill_category (id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E311C8FB41');
        $this->addSql('ALTER TABLE bill_installments DROP FOREIGN KEY FK_805065891A8C12F5');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E3635925EF');
        $this->addSql('ALTER TABLE bill_plan_category DROP FOREIGN KEY FK_783D1A57635925EF');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E31C5A3684');
        $this->addSql('ALTER TABLE bill_plan DROP FOREIGN KEY FK_2E4533D7E7F74BB7');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E387190E55');
        $this->addSql('ALTER TABLE bill_installments DROP FOREIGN KEY FK_805065895AA1164F');
        $this->addSql('DROP TABLE bank');
        $this->addSql('DROP TABLE bill');
        $this->addSql('DROP TABLE bill_category');
        $this->addSql('DROP TABLE bill_installments');
        $this->addSql('DROP TABLE bill_plan');
        $this->addSql('DROP TABLE bill_plan_category');
        $this->addSql('DROP TABLE bill_status');
        $this->addSql('DROP TABLE payment_method');
    }
}
