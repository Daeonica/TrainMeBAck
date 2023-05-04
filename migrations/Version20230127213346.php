<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230127213346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE buy_user_course (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, course_id INT NOT NULL, transaction_date DATETIME NOT NULL, INDEX IDX_38625CC4A76ED395 (user_id), INDEX IDX_38625CC4591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE buy_user_course ADD CONSTRAINT FK_38625CC4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE buy_user_course ADD CONSTRAINT FK_38625CC4591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_169E6FB9A76ED395 ON course (user_id)');
        $this->addSql('ALTER TABLE course ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9A76ED300 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_169E6FB9A76ED300 ON course (category_id)');
        $this->addSql('ALTER TABLE user ADD role_id INT NOT NULL ');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8D93D649D60322AC ON user (role_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE buy_user_course DROP FOREIGN KEY FK_38625CC4A76ED395');
        $this->addSql('ALTER TABLE buy_user_course DROP FOREIGN KEY FK_38625CC4591CC992');
        $this->addSql('DROP TABLE buy_user_course');
        $this->addSql('DROP INDEX IDX_9645EA3612F7FB51 ON contability');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9A76ED395');
        $this->addSql('DROP INDEX IDX_169E6FB9A76ED395 ON course');
        $this->addSql('ALTER TABLE course DROP user_id');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9A76ED300');
        $this->addSql('DROP INDEX IDX_169E6FB9A76ED300 ON course');
        $this->addSql('ALTER TABLE course DROP category_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('DROP INDEX IDX_8D93D649D60322AC ON user');
        $this->addSql('ALTER TABLE user DROP role_id');
    }
}
