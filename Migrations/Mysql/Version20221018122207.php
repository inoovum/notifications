<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221018122207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE steinbauerit_notifications_domain_model_accountsettings (persistence_object_identifier VARCHAR(40) NOT NULL, account VARCHAR(40) DEFAULT NULL, settings LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_D6A010D07D3656A4 (account), PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE steinbauerit_notifications_domain_model_notification (persistence_object_identifier VARCHAR(40) NOT NULL, account VARCHAR(40) DEFAULT NULL, image VARCHAR(40) DEFAULT NULL, senderimage VARCHAR(40) DEFAULT NULL, summarynotification VARCHAR(40) DEFAULT NULL, createdat DATETIME NOT NULL, title VARCHAR(255) NOT NULL, intro LONGTEXT NOT NULL, text LONGTEXT NOT NULL, actionuri VARCHAR(255) NOT NULL, recipientinfos LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', sendername VARCHAR(255) NOT NULL, distributionmethod VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, assummary VARCHAR(255) NOT NULL, distributeat DATETIME DEFAULT NULL, wasdistributedat DATETIME DEFAULT NULL, wasreadat DATETIME DEFAULT NULL, distributionlog VARCHAR(255) NOT NULL, INDEX IDX_A36A510C7D3656A4 (account), INDEX IDX_A36A510CC53D045F (image), INDEX IDX_A36A510C67F28297 (senderimage), INDEX IDX_A36A510C74760FB7 (summarynotification), PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE steinbauerit_notifications_domain_model_accountsettings ADD CONSTRAINT FK_D6A010D07D3656A4 FOREIGN KEY (account) REFERENCES neos_flow_security_account (persistence_object_identifier)');
        $this->addSql('ALTER TABLE steinbauerit_notifications_domain_model_notification ADD CONSTRAINT FK_A36A510C7D3656A4 FOREIGN KEY (account) REFERENCES neos_flow_security_account (persistence_object_identifier)');
        $this->addSql('ALTER TABLE steinbauerit_notifications_domain_model_notification ADD CONSTRAINT FK_A36A510CC53D045F FOREIGN KEY (image) REFERENCES neos_flow_resourcemanagement_persistentresource (persistence_object_identifier)');
        $this->addSql('ALTER TABLE steinbauerit_notifications_domain_model_notification ADD CONSTRAINT FK_A36A510C67F28297 FOREIGN KEY (senderimage) REFERENCES neos_flow_resourcemanagement_persistentresource (persistence_object_identifier)');
        $this->addSql('ALTER TABLE steinbauerit_notifications_domain_model_notification ADD CONSTRAINT FK_A36A510C74760FB7 FOREIGN KEY (summarynotification) REFERENCES steinbauerit_notifications_domain_model_notification (persistence_object_identifier)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDb1027Platform'."
        );

        $this->addSql('ALTER TABLE steinbauerit_notifications_domain_model_notification DROP FOREIGN KEY FK_A36A510C74760FB7');
        $this->addSql('DROP TABLE steinbauerit_notifications_domain_model_accountsettings');
        $this->addSql('DROP TABLE steinbauerit_notifications_domain_model_notification');
    }
}
