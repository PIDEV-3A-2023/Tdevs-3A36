<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230305123440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE budget (id INT AUTO_INCREMENT NOT NULL, id_account_id INT NOT NULL, montant DOUBLE PRECISION NOT NULL, datedebut DATETIME NOT NULL, datefin DATETIME NOT NULL, INDEX IDX_73F2F77B3EE1DF6D (id_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE depenses (id_depense INT AUTO_INCREMENT NOT NULL, idbudget_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, prenom_destinataire VARCHAR(255) NOT NULL, rib_destinataire VARCHAR(255) NOT NULL, montant INT NOT NULL, backgroundcolor VARCHAR(7) NOT NULL, datedebut DATETIME NOT NULL, categorie_depense VARCHAR(255) NOT NULL, type_depense VARCHAR(255) NOT NULL, INDEX IDX_EE350ECB48B00F4D (idbudget_id), PRIMARY KEY(id_depense)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE virement (id_virement INT AUTO_INCREMENT NOT NULL, montant INT NOT NULL, date_virement DATETIME NOT NULL, titre VARCHAR(255) NOT NULL, prenom_benef VARCHAR(255) NOT NULL, rib_benef VARCHAR(255) NOT NULL, PRIMARY KEY(id_virement)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE budget ADD CONSTRAINT FK_73F2F77B3EE1DF6D FOREIGN KEY (id_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE depenses ADD CONSTRAINT FK_EE350ECB48B00F4D FOREIGN KEY (idbudget_id) REFERENCES budget (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE budget DROP FOREIGN KEY FK_73F2F77B3EE1DF6D');
        $this->addSql('ALTER TABLE depenses DROP FOREIGN KEY FK_EE350ECB48B00F4D');
        $this->addSql('DROP TABLE budget');
        $this->addSql('DROP TABLE depenses');
        $this->addSql('DROP TABLE virement');
    }
}
