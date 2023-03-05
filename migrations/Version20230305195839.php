<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230305195839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE beneficiaire (id_benef INT AUTO_INCREMENT NOT NULL, rib_benef VARCHAR(13) NOT NULL, nom_benef VARCHAR(13) NOT NULL, prenom_benef VARCHAR(13) NOT NULL, email_benef VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_B140D8024A77282B (email_benef), PRIMARY KEY(id_benef)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE virement ADD beneficiaire_id INT DEFAULT NULL, CHANGE montant montant DOUBLE PRECISION NOT NULL, CHANGE date_virement date_virement DATE NOT NULL');
        $this->addSql('ALTER TABLE virement ADD CONSTRAINT FK_2D4DCFA65AF81F68 FOREIGN KEY (beneficiaire_id) REFERENCES beneficiaire (id_benef)');
        $this->addSql('CREATE INDEX IDX_2D4DCFA65AF81F68 ON virement (beneficiaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE virement DROP FOREIGN KEY FK_2D4DCFA65AF81F68');
        $this->addSql('DROP TABLE beneficiaire');
        $this->addSql('DROP INDEX IDX_2D4DCFA65AF81F68 ON virement');
        $this->addSql('ALTER TABLE virement DROP beneficiaire_id, CHANGE montant montant INT NOT NULL, CHANGE date_virement date_virement DATETIME NOT NULL');
    }
}
