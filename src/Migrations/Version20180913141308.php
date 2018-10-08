<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180913141308 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE battle_battle (id INT AUTO_INCREMENT NOT NULL, programmer_id INT NOT NULL, project_id INT NOT NULL, did_programmer_win TINYINT(1) NOT NULL, fought_at DATETIME NOT NULL, notes LONGTEXT NOT NULL, INDEX IDX_36EFFEC5181DAE45 (programmer_id), INDEX IDX_36EFFEC5166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE battle_battle ADD CONSTRAINT FK_36EFFEC5181DAE45 FOREIGN KEY (programmer_id) REFERENCES programmer (id)');
        $this->addSql('ALTER TABLE battle_battle ADD CONSTRAINT FK_36EFFEC5166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE battle_battle');
    }
}
