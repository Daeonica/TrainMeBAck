<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230127154405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE courses ADD img_path VARCHAR(500) NOT NULL');
        $this->addSql('ALTER TABLE publications ADD img_path VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD img_path VARCHAR(500) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE courses DROP img_path');
        $this->addSql('ALTER TABLE users DROP img_path');
        $this->addSql('ALTER TABLE publications DROP img_path');
    }
}
