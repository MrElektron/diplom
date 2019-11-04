<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191104133941 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE discipline (id INT AUTO_INCREMENT NOT NULL, discipline_index VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, exams VARCHAR(255) DEFAULT NULL, offsets VARCHAR(255) DEFAULT NULL, differentiated_offsets VARCHAR(255) DEFAULT NULL, course_projects VARCHAR(255) DEFAULT NULL, coursework VARCHAR(255) DEFAULT NULL, other VARCHAR(255) DEFAULT NULL, maximum_load VARCHAR(255) DEFAULT NULL, independent_work VARCHAR(255) DEFAULT NULL, consultations VARCHAR(255) DEFAULT NULL, total VARCHAR(255) DEFAULT NULL, lessons VARCHAR(255) DEFAULT NULL, practical_lessons VARCHAR(255) DEFAULT NULL, laboratory_classes VARCHAR(255) DEFAULT NULL, course_design VARCHAR(255) DEFAULT NULL, intermediate_certification VARCHAR(255) DEFAULT NULL, lesson_workshop VARCHAR(255) DEFAULT NULL, individual_project VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE semester (id INT AUTO_INCREMENT NOT NULL, discipline VARCHAR(255) NOT NULL, semester INT NOT NULL, maximum_load VARCHAR(255) DEFAULT NULL, independent_work VARCHAR(255) DEFAULT NULL, consultations VARCHAR(255) DEFAULT NULL, obligatory VARCHAR(255) DEFAULT NULL, lessons VARCHAR(255) DEFAULT NULL, practical_lessons VARCHAR(255) DEFAULT NULL, laboratory_classes VARCHAR(255) DEFAULT NULL, course_design VARCHAR(255) DEFAULT NULL, intermediate_certification VARCHAR(255) DEFAULT NULL, lesson_workshop VARCHAR(255) DEFAULT NULL, individual_project VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialty (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, number VARCHAR(255) NOT NULL, qualification VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE discipline');
        $this->addSql('DROP TABLE semester');
        $this->addSql('DROP TABLE specialty');
        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\'');
    }
}
