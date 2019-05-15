<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190513054138 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seller_requests (id INT AUTO_INCREMENT NOT NULL, seller_id INT NOT NULL, user_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, resume LONGTEXT DEFAULT NULL, file VARCHAR(255) DEFAULT NULL, INDEX IDX_11EEF81A8DE820D9 (seller_id), INDEX IDX_11EEF81AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seller (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address LONGTEXT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE checkout_product (id INT AUTO_INCREMENT NOT NULL, checkout_id INT NOT NULL, product_id INT NOT NULL, count INT NOT NULL, INDEX IDX_2F21E0D4146D8724 (checkout_id), INDEX IDX_2F21E0D44584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin_requests (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(255) NOT NULL, company_description LONGTEXT DEFAULT NULL, company_file VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, company_address VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, seller_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, role VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, login VARCHAR(120) NOT NULL, token VARCHAR(60) DEFAULT NULL, created_at DATETIME NOT NULL, name VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649AA08CB10 (login), INDEX IDX_8D93D6498DE820D9 (seller_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, seller_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, count INT DEFAULT NULL, price INT NOT NULL, external_id VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), INDEX IDX_D34A04AD8DE820D9 (seller_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE checkout (id INT AUTO_INCREMENT NOT NULL, seller_id INT NOT NULL, user_id INT NOT NULL, address VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, cost INT NOT NULL, phone VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_AF382D4E8DE820D9 (seller_id), INDEX IDX_AF382D4EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE seller_requests ADD CONSTRAINT FK_11EEF81A8DE820D9 FOREIGN KEY (seller_id) REFERENCES seller (id)');
        $this->addSql('ALTER TABLE seller_requests ADD CONSTRAINT FK_11EEF81AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE checkout_product ADD CONSTRAINT FK_2F21E0D4146D8724 FOREIGN KEY (checkout_id) REFERENCES checkout (id)');
        $this->addSql('ALTER TABLE checkout_product ADD CONSTRAINT FK_2F21E0D44584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498DE820D9 FOREIGN KEY (seller_id) REFERENCES seller (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD8DE820D9 FOREIGN KEY (seller_id) REFERENCES seller (id)');
        $this->addSql('ALTER TABLE checkout ADD CONSTRAINT FK_AF382D4E8DE820D9 FOREIGN KEY (seller_id) REFERENCES seller (id)');
        $this->addSql('ALTER TABLE checkout ADD CONSTRAINT FK_AF382D4EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE seller_requests DROP FOREIGN KEY FK_11EEF81A8DE820D9');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498DE820D9');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD8DE820D9');
        $this->addSql('ALTER TABLE checkout DROP FOREIGN KEY FK_AF382D4E8DE820D9');
        $this->addSql('ALTER TABLE seller_requests DROP FOREIGN KEY FK_11EEF81AA76ED395');
        $this->addSql('ALTER TABLE checkout DROP FOREIGN KEY FK_AF382D4EA76ED395');
        $this->addSql('ALTER TABLE checkout_product DROP FOREIGN KEY FK_2F21E0D44584665A');
        $this->addSql('ALTER TABLE checkout_product DROP FOREIGN KEY FK_2F21E0D4146D8724');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE seller_requests');
        $this->addSql('DROP TABLE seller');
        $this->addSql('DROP TABLE checkout_product');
        $this->addSql('DROP TABLE admin_requests');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE checkout');
    }
}
