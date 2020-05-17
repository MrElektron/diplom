<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200410012154 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competence DROP FOREIGN KEY FK_94D4687F93CB796C');
        $this->addSql('ALTER TABLE competence CHANGE file_id file_id INT DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE competence ADD CONSTRAINT FK_94D4687F93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE discipline DROP FOREIGN KEY FK_75BEEE3F93CB796C');
        $this->addSql('ALTER TABLE discipline ADD cycle TINYINT(1) NOT NULL, CHANGE file_id file_id INT DEFAULT NULL, CHANGE exams exams VARCHAR(255) DEFAULT NULL, CHANGE offsets offsets VARCHAR(255) DEFAULT NULL, CHANGE differentiated_offsets differentiated_offsets VARCHAR(255) DEFAULT NULL, CHANGE course_projects course_projects VARCHAR(255) DEFAULT NULL, CHANGE coursework coursework VARCHAR(255) DEFAULT NULL, CHANGE other other VARCHAR(255) DEFAULT NULL, CHANGE maximum_load maximum_load VARCHAR(255) DEFAULT NULL, CHANGE independent_work independent_work VARCHAR(255) DEFAULT NULL, CHANGE consultations consultations VARCHAR(255) DEFAULT NULL, CHANGE total total VARCHAR(255) DEFAULT NULL, CHANGE lessons lessons VARCHAR(255) DEFAULT NULL, CHANGE practical_lessons practical_lessons VARCHAR(255) DEFAULT NULL, CHANGE laboratory_classes laboratory_classes VARCHAR(255) DEFAULT NULL, CHANGE course_design course_design VARCHAR(255) DEFAULT NULL, CHANGE intermediate_certification intermediate_certification VARCHAR(255) DEFAULT NULL, CHANGE lesson_workshop lesson_workshop VARCHAR(255) DEFAULT NULL, CHANGE individual_project individual_project VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE discipline ADD CONSTRAINT FK_75BEEE3F93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE discipline_competence DROP FOREIGN KEY FK_1309EE415761DAB');
        $this->addSql('ALTER TABLE discipline_competence DROP FOREIGN KEY FK_1309EE4A5522701');
        $this->addSql('ALTER TABLE discipline_competence CHANGE discipline_id discipline_id INT DEFAULT NULL, CHANGE competence_id competence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE discipline_competence ADD CONSTRAINT FK_1309EE415761DAB FOREIGN KEY (competence_id) REFERENCES competence (id)');
        $this->addSql('ALTER TABLE discipline_competence ADD CONSTRAINT FK_1309EE4A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36107E3C61F9');
        $this->addSql('ALTER TABLE file ADD training_form VARCHAR(255) NOT NULL, CHANGE owner_id owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36107E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDA5522701');
        $this->addSql('ALTER TABLE semester CHANGE discipline_id discipline_id INT DEFAULT NULL, CHANGE maximum_load maximum_load VARCHAR(255) DEFAULT NULL, CHANGE independent_work independent_work VARCHAR(255) DEFAULT NULL, CHANGE consultations consultations VARCHAR(255) DEFAULT NULL, CHANGE obligatory obligatory VARCHAR(255) DEFAULT NULL, CHANGE lessons lessons VARCHAR(255) DEFAULT NULL, CHANGE practical_lessons practical_lessons VARCHAR(255) DEFAULT NULL, CHANGE laboratory_classes laboratory_classes VARCHAR(255) DEFAULT NULL, CHANGE course_design course_design VARCHAR(255) DEFAULT NULL, CHANGE intermediate_certification intermediate_certification VARCHAR(255) DEFAULT NULL, CHANGE lesson_workshop lesson_workshop VARCHAR(255) DEFAULT NULL, CHANGE individual_project individual_project VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE semester ADD CONSTRAINT FK_F7388EEDA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competence DROP FOREIGN KEY FK_94D4687F93CB796C');
        $this->addSql('ALTER TABLE competence CHANGE file_id file_id INT DEFAULT NULL, CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE competence ADD CONSTRAINT FK_94D4687F93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discipline DROP FOREIGN KEY FK_75BEEE3F93CB796C');
        $this->addSql('ALTER TABLE discipline DROP cycle, CHANGE file_id file_id INT DEFAULT NULL, CHANGE exams exams VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE offsets offsets VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE differentiated_offsets differentiated_offsets VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE course_projects course_projects VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE coursework coursework VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE other other VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE maximum_load maximum_load VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE independent_work independent_work VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE consultations consultations VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE total total VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE lessons lessons VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE practical_lessons practical_lessons VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE laboratory_classes laboratory_classes VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE course_design course_design VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE intermediate_certification intermediate_certification VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE lesson_workshop lesson_workshop VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE individual_project individual_project VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE discipline ADD CONSTRAINT FK_75BEEE3F93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discipline_competence DROP FOREIGN KEY FK_1309EE4A5522701');
        $this->addSql('ALTER TABLE discipline_competence DROP FOREIGN KEY FK_1309EE415761DAB');
        $this->addSql('ALTER TABLE discipline_competence CHANGE discipline_id discipline_id INT DEFAULT NULL, CHANGE competence_id competence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE discipline_competence ADD CONSTRAINT FK_1309EE4A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discipline_competence ADD CONSTRAINT FK_1309EE415761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36107E3C61F9');
        $this->addSql('ALTER TABLE file DROP training_form, CHANGE owner_id owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36107E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE semester DROP FOREIGN KEY FK_F7388EEDA5522701');
        $this->addSql('ALTER TABLE semester CHANGE discipline_id discipline_id INT DEFAULT NULL, CHANGE maximum_load maximum_load VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE independent_work independent_work VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE consultations consultations VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE obligatory obligatory VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE lessons lessons VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE practical_lessons practical_lessons VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE laboratory_classes laboratory_classes VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE course_design course_design VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE intermediate_certification intermediate_certification VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE lesson_workshop lesson_workshop VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE individual_project individual_project VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE semester ADD CONSTRAINT FK_F7388EEDA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id) ON DELETE CASCADE');
    }
}
