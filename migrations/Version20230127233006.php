<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230127233006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category CHANGE course_id course_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contability CHANGE sponsor_id sponsor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE course CHANGE img_path img_path VARCHAR(500) NOT NULL');
        $this->addSql('ALTER TABLE publication CHANGE category_id category_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD description VARCHAR(500) DEFAULT NULL, CHANGE img_path img_path VARCHAR(500) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category CHANGE course_id course_id INT NOT NULL');
        $this->addSql('ALTER TABLE contability CHANGE sponsor_id sponsor_id INT NOT NULL');
        $this->addSql('ALTER TABLE publication CHANGE category_id category_id INT NOT NULL, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP description, CHANGE img_path img_path VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE course CHANGE img_path img_path VARCHAR(500) DEFAULT NULL');
    }
}
