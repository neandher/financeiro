<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160704230452 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bank (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, agency VARCHAR(255) NOT NULL, account VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_plan (id INT AUTO_INCREMENT NOT NULL, bill_plan_type_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_2E4533D7533A79E (bill_plan_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_plan_type (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_type (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, referency VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bill_plan ADD CONSTRAINT FK_2E4533D7533A79E FOREIGN KEY (bill_plan_type_id) REFERENCES bill_plan (id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bill_plan DROP FOREIGN KEY FK_2E4533D7533A79E');
        $this->addSql('DROP TABLE bank');
        $this->addSql('DROP TABLE bill_plan');
        $this->addSql('DROP TABLE bill_plan_type');
        $this->addSql('DROP TABLE bill_type');
    }
}
