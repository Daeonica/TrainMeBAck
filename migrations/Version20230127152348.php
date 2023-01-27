<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230127152348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contabilities (id INT AUTO_INCREMENT NOT NULL, sponsor_id_id INT NOT NULL, bill_date DATETIME NOT NULL, quantity NUMERIC(10, 0) NOT NULL, concept VARCHAR(255) DEFAULT NULL, INDEX IDX_45BA02BDDFAEDE6C (sponsor_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course_purchases (id INT AUTO_INCREMENT NOT NULL, course_id INT DEFAULT NULL, user_id INT DEFAULT NULL, transaction_date DATETIME NOT NULL, INDEX IDX_28C57152D92975B5 (course_id), INDEX IDX_28C5715279F37AE5 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE courses (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, categories_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(500) NOT NULL, document_root VARCHAR(500) NOT NULL, price NUMERIC(10, 0) NOT NULL, INDEX IDX_A9A55A4C79F37AE5 (user_id), INDEX IDX_A9A55A4CA21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publications (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, id_category_id INT DEFAULT NULL, title VARCHAR(500) NOT NULL, content VARCHAR(500) NOT NULL, date_publication DATETIME DEFAULT NULL, INDEX IDX_32783AF479F37AE5 (user_id), INDEX IDX_32783AF4A545015 (id_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, key_value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, contact VARCHAR(255) NOT NULL, phone VARCHAR(9) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, password VARCHAR(500) NOT NULL, email VARCHAR(500) NOT NULL, register_date DATETIME DEFAULT NULL, INDEX IDX_1483A5E9393FB813 (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contabilities ADD CONSTRAINT FK_45BA02BDDFAEDE6C FOREIGN KEY (sponsor_id_id) REFERENCES sponsors (id)');
        $this->addSql('ALTER TABLE course_purchases ADD CONSTRAINT FK_28C57152D92975B5 FOREIGN KEY (course_id) REFERENCES courses (id)');
        $this->addSql('ALTER TABLE course_purchases ADD CONSTRAINT FK_28C5715279F37AE5 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4C79F37AE5 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4CA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE publications ADD CONSTRAINT FK_32783AF479F37AE5 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE publications ADD CONSTRAINT FK_32783AF4A545015 FOREIGN KEY (id_category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9393FB813 FOREIGN KEY (role_id) REFERENCES roles (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contabilities DROP FOREIGN KEY FK_45BA02BDDFAEDE6C');
        $this->addSql('ALTER TABLE course_purchases DROP FOREIGN KEY FK_28C57152D92975B5');
        $this->addSql('ALTER TABLE course_purchases DROP FOREIGN KEY FK_28C5715279F37AE5');
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4C79F37AE5');
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4CA21214B7');
        $this->addSql('ALTER TABLE publications DROP FOREIGN KEY FK_32783AF479F37AE5');
        $this->addSql('ALTER TABLE publications DROP FOREIGN KEY FK_32783AF4A545015');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9393FB813');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE contabilities');
        $this->addSql('DROP TABLE course_purchases');
        $this->addSql('DROP TABLE courses');
        $this->addSql('DROP TABLE publications');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE sponsors');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
