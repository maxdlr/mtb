<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231204190713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, reporter_id INT NOT NULL, violation_id INT NOT NULL, resolution_id INT DEFAULT NULL, description VARCHAR(500) NOT NULL, reported_on DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', resolved_on DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_resolved TINYINT(1) NOT NULL, INDEX IDX_C42F77844B89032C (post_id), INDEX IDX_C42F7784E1CFE6F5 (reporter_id), INDEX IDX_C42F77847386118A (violation_id), INDEX IDX_C42F778412A1C43A (resolution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resolution (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE violation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77844B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77847386118A FOREIGN KEY (violation_id) REFERENCES violation (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F778412A1C43A FOREIGN KEY (resolution_id) REFERENCES resolution (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77844B89032C');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784E1CFE6F5');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77847386118A');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F778412A1C43A');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE resolution');
        $this->addSql('DROP TABLE violation');
    }
}
