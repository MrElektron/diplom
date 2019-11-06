<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106152350 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE competence (id INT AUTO_INCREMENT NOT NULL, discipline_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_94D4687FA5522701 (discipline_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competence ADD CONSTRAINT FK_94D4687FA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('ALTER TABLE semester ADD discipline_id INT DEFAULT NULL, DROP discipline, CHANGE maximum_load maximum_load VARCHAR(255) DEFAULT NULL, CHANGE independent_work independent_work VARCHAR(255) DEFAULT NULL, CHANGE consultations consultations VARCHAR(255) DEFAULT NULL, CHANGE obligatory obligatory VARCHAR(255) DEFAULT NULL, CHANGE lessons lessons VARCHAR(255) DEFAULT NULL, CHANGE practical_lessons practical_lessons VARCHAR(255) DEFAULT NULL, CHANGE laboratory_classes laboratory_classes VARCHAR(255) DEFAULT NULL, CHANGE course_design course_design VARCHAR(255) DEFAULT NULL, CHANGE intermediate_certification intermediate_certification VARCHAR(255) DEFAULT NULL, CHANGE lesson_workshop lesson_workshop VARCHAR(255) DEFAULT NULL, CHANGE individual_project individual_project VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE semester ADD CONSTRAINT FK_F7388EEDA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('CREATE INDEX IDX_F7388EEDA5522701 ON semester (discipline_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE competence');
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDA5522701');
        $this->addSql('DROP INDEX IDX_F7388EEDA5522701 ON semester');
        $this->addSql('ALTER TABLE semester ADD discipline VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP discipline_id, CHANGE maximum_load maximum_load VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci, CHANGE independent_work independent_work VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci, CHANGE consultations consultations VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci, CHANGE obligatory obligatory VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci, CHANGE lessons lessons VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci, CHANGE practical_lessons practical_lessons VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci, CHANGE laboratory_classes laboratory_classes VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci, CHANGE course_design course_design VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci, CHANGE intermediate_certification intermediate_certification VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci, CHANGE lesson_workshop lesson_workshop VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci, CHANGE individual_project individual_project VARCHAR(255) DEFAULT \'\'NULL\'\' COLLATE utf8mb4_unicode_ci');
    }
}
