<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231113210633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calorique (id INT AUTO_INCREMENT NOT NULL, objectif VARCHAR(255) DEFAULT NULL, besoins_caloriques DOUBLE PRECISION DEFAULT NULL, activite VARCHAR(255) DEFAULT NULL, regime_alimentaire VARCHAR(255) NOT NULL, niveau_stress VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE imc (id INT AUTO_INCREMENT NOT NULL, sexe VARCHAR(255) DEFAULT NULL, age INT DEFAULT NULL, taille DOUBLE PRECISION DEFAULT NULL, poids DOUBLE PRECISION DEFAULT NULL, categorie_imc VARCHAR(255) DEFAULT NULL, poids_ideal DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE calorique');
        $this->addSql('DROP TABLE imc');
    }
}
