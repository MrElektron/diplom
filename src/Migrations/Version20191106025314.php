<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106025314 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE discipline CHANGE exams exams VARCHAR(255) DEFAULT NULL, CHANGE offsets offsets VARCHAR(255) DEFAULT NULL, CHANGE differentiated_offsets differentiated_offsets VARCHAR(255) DEFAULT NULL, CHANGE course_projects course_projects VARCHAR(255) DEFAULT NULL, CHANGE coursework coursework VARCHAR(255) DEFAULT NULL, CHANGE other other VARCHAR(255) DEFAULT NULL, CHANGE maximum_load maximum_load VARCHAR(255) DEFAULT NULL, CHANGE independent_work independent_work VARCHAR(255) DEFAULT NULL, CHANGE consultations consultations VARCHAR(255) DEFAULT NULL, CHANGE total total VARCHAR(255) DEFAULT NULL, CHANGE lessons lessons VARCHAR(255) DEFAULT NULL, CHANGE practical_lessons practical_lessons VARCHAR(255) DEFAULT NULL, CHANGE laboratory_classes laboratory_classes VARCHAR(255) DEFAULT NULL, CHANGE course_design course_design VARCHAR(255) DEFAULT NULL, CHANGE intermediate_certification intermediate_certification VARCHAR(255) DEFAULT NULL, CHANGE lesson_workshop lesson_workshop VARCHAR(255) DEFAULT NULL, CHANGE individual_project individual_project VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36107E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_8C9F36107E3C61F9 ON file (owner_id)');
        $this->addSql('ALTER TABLE semester CHANGE maximum_load maximum_load VARCHAR(255) DEFAULT NULL, CHANGE independent_work independent_work VARCHAR(255) DEFAULT NULL, CHANGE consultations consultations VARCHAR(255) DEFAULT NULL, CHANGE obligatory obligatory VARCHAR(255) DEFAULT NULL, CHANGE lessons lessons VARCHAR(255) DEFAULT NULL, CHANGE practical_lessons practical_lessons VARCHAR(255) DEFAULT NULL, CHANGE laboratory_classes laboratory_classes VARCHAR(255) DEFAULT NULL, CHANGE course_design course_design VARCHAR(255) DEFAULT NULL, CHANGE intermediate_certification intermediate_certification VARCHAR(255) DEFAULT NULL, CHANGE lesson_workshop lesson_workshop VARCHAR(255) DEFAULT NULL, CHANGE individual_project individual_project VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE discipline CHANGE exams exams VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE offsets offsets VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE differentiated_offsets differentiated_offsets VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE course_projects course_projects VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE coursework coursework VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE other other VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE maximum_load maximum_load VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE independent_work independent_work VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE consultations consultations VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE total total VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE lessons lessons VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE practical_lessons practical_lessons VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE laboratory_classes laboratory_classes VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE course_design course_design VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE intermediate_certification intermediate_certification VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE lesson_workshop lesson_workshop VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE individual_project individual_project VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36107E3C61F9');
        $this->addSql('DROP INDEX IDX_8C9F36107E3C61F9 ON file');
        $this->addSql('ALTER TABLE file DROP owner_id');
        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE semester CHANGE maximum_load maximum_load VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE independent_work independent_work VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE consultations consultations VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE obligatory obligatory VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE lessons lessons VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE practical_lessons practical_lessons VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE laboratory_classes laboratory_classes VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE course_design course_design VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE intermediate_certification intermediate_certification VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE lesson_workshop lesson_workshop VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE individual_project individual_project VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
