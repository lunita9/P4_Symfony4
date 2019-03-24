<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190305104532 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reservation CHANGE nombre_tarif_normal nombre_tarif_normal INT NOT NULL, CHANGE nombre_tarif_reduit nombre_tarif_reduit INT NOT NULL, CHANGE nombre_tarif_enfant nombre_tarif_enfant INT NOT NULL, CHANGE nombre_tarif_senior nombre_tarif_senior INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reservation CHANGE nombre_tarif_normal nombre_tarif_normal VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE nombre_tarif_reduit nombre_tarif_reduit VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE nombre_tarif_enfant nombre_tarif_enfant VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE nombre_tarif_senior nombre_tarif_senior VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
