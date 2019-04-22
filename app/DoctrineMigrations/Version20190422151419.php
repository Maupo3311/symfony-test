<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190422151419 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE basket_user (basket_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_461CCF7C1BE1FB52 (basket_id), INDEX IDX_461CCF7CA76ED395 (user_id), PRIMARY KEY(basket_id, user_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_basket (product_id INT NOT NULL, basket_id INT NOT NULL, INDEX IDX_403A11DF4584665A (product_id), INDEX IDX_403A11DF1BE1FB52 (basket_id), PRIMARY KEY(product_id, basket_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE basket_user ADD CONSTRAINT FK_461CCF7C1BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE basket_user ADD CONSTRAINT FK_461CCF7CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_basket ADD CONSTRAINT FK_403A11DF4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_basket ADD CONSTRAINT FK_403A11DF1BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BA76ED395');
        $this->addSql('DROP INDEX UNIQ_2246507BA76ED395 ON basket');
        $this->addSql('ALTER TABLE basket DROP user_id');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD1BE1FB52');
        $this->addSql('DROP INDEX IDX_D34A04AD1BE1FB52 ON product');
        $this->addSql('ALTER TABLE product DROP basket_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE basket_user');
        $this->addSql('DROP TABLE product_basket');
        $this->addSql('ALTER TABLE basket ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2246507BA76ED395 ON basket (user_id)');
        $this->addSql('ALTER TABLE product ADD basket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD1BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD1BE1FB52 ON product (basket_id)');
    }
}
