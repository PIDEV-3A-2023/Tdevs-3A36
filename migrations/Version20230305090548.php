<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230305090548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prix INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, num_tel INT NOT NULL, email VARCHAR(255) NOT NULL, sexe VARCHAR(255) NOT NULL, date_naiss DATE NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, brochure_filename VARCHAR(255) NOT NULL, etat TINYINT(1) DEFAULT NULL, solde INT NOT NULL, FULLTEXT INDEX account (nom_complet, email, sexe, adresse, ville), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, id_abonnement_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_E19D9AD24FFF9576 (id_abonnement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD24FFF9576 FOREIGN KEY (id_abonnement_id) REFERENCES abonnement (id)');
        $this->addSql('ALTER TABLE user CHANGE telephone telephone INT NOT NULL, CHANGE adresse adresse VARCHAR(180) NOT NULL, CHANGE gender gender VARCHAR(255) NOT NULL, CHANGE age age INT NOT NULL, CHANGE reset_token reset_token VARCHAR(100) NOT NULL, CHANGE created_at created_at DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD24FFF9576');
        $this->addSql('DROP TABLE abonnement');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE service');
        $this->addSql('ALTER TABLE user CHANGE telephone telephone INT DEFAULT NULL, CHANGE adresse adresse VARCHAR(180) DEFAULT NULL, CHANGE gender gender VARCHAR(255) DEFAULT NULL, CHANGE age age INT DEFAULT NULL, CHANGE reset_token reset_token VARCHAR(100) DEFAULT NULL, CHANGE created_at created_at DATE DEFAULT \'CURRENT_TIMESTAMP\'');
    }
}
