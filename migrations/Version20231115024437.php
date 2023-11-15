<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231115024437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calorique ADD imcs_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE calorique ADD CONSTRAINT FK_D58D83D1930D401 FOREIGN KEY (imcs_id) REFERENCES imc (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D58D83D1930D401 ON calorique (imcs_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calorique DROP FOREIGN KEY FK_D58D83D1930D401');
        $this->addSql('DROP INDEX UNIQ_D58D83D1930D401 ON calorique');
        $this->addSql('ALTER TABLE calorique DROP imcs_id');
    }
}
