<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190907080150 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("CREATE TABLE `sessions` (`sess_id` VARCHAR(128) NOT NULL PRIMARY KEY, `sess_data` BLOB NOT NULL, `sess_time` INTEGER UNSIGNED NOT NULL, `sess_lifetime` MEDIUMINT NOT NULL ) COLLATE utf8mb4_bin, ENGINE = InnoDB");
        $this->addSql('CREATE TABLE users (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(60) NOT NULL, roles JSON NOT NULL, password VARCHAR(64) NOT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', gallery_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', original_filename VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_E01FBE6A4E7AF8F (gallery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE galleries (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_F70E6EB7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A4E7AF8F FOREIGN KEY (gallery_id) REFERENCES galleries (id)');
        $this->addSql('ALTER TABLE galleries ADD CONSTRAINT FK_F70E6EB7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE galleries DROP FOREIGN KEY FK_F70E6EB7A76ED395');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A4E7AF8F');
        $this->addSql('DROP TABLE sessions');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE galleries');
    }
}
