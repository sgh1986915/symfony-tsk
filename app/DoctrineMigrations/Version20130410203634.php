<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130410203634 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE tsk_contact (contact_id INT AUTO_INCREMENT NOT NULL, fk_org_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, email_canonical VARCHAR(255) DEFAULT NULL, address1 VARCHAR(255) DEFAULT NULL, address2 VARCHAR(255) DEFAULT NULL, city VARCHAR(50) DEFAULT NULL, state VARCHAR(50) DEFAULT NULL, zipcode VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, mobile VARCHAR(20) DEFAULT NULL, fax VARCHAR(20) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, geocode VARCHAR(255) DEFAULT NULL, img_path VARCHAR(255) DEFAULT NULL, date_of_birth DATE DEFAULT NULL, INDEX IDX_BB3C6953D42D148B (fk_org_id), PRIMARY KEY(contact_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_contact_school (fk_contact_id INT NOT NULL, fk_school_id INT NOT NULL, INDEX IDX_93C90C9C17CFEFD9 (fk_contact_id), INDEX IDX_93C90C9CA273907E (fk_school_id), PRIMARY KEY(fk_contact_id, fk_school_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_corporation (corporation_id INT AUTO_INCREMENT NOT NULL, tax_id VARCHAR(20) NOT NULL, account_num VARCHAR(50) DEFAULT NULL, routing_num VARCHAR(50) DEFAULT NULL, legal_name VARCHAR(50) NOT NULL, dba VARCHAR(50) DEFAULT NULL, PRIMARY KEY(corporation_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_corporation_contact (tsk_corp_contact_id INT AUTO_INCREMENT NOT NULL, fk_corporation_id INT NOT NULL, fk_contact_id INT NOT NULL, INDEX IDX_AF597804C766BA80 (fk_corporation_id), INDEX IDX_AF59780417CFEFD9 (fk_contact_id), PRIMARY KEY(tsk_corp_contact_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_emergency_contact (emergency_contact_id INT AUTO_INCREMENT NOT NULL, fk_contact_id INT NOT NULL, UNIQUE INDEX UNIQ_8F82B42A17CFEFD9 (fk_contact_id), PRIMARY KEY(emergency_contact_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_login_history (login_history_id INT AUTO_INCREMENT NOT NULL, fk_user_id INT NOT NULL, login_ip VARCHAR(100) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, login_date DATETIME NOT NULL, INDEX IDX_FD4BAB285741EEB9 (fk_user_id), PRIMARY KEY(login_history_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_organization (org_id INT AUTO_INCREMENT NOT NULL, fk_contact_id INT DEFAULT NULL, fk_corporation_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_A7E9F78D17CFEFD9 (fk_contact_id), UNIQUE INDEX UNIQ_A7E9F78DC766BA80 (fk_corporation_id), PRIMARY KEY(org_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_prospective (prospective_id INT AUTO_INCREMENT NOT NULL, fk_contact_id INT NOT NULL, UNIQUE INDEX UNIQ_4A969AFA17CFEFD9 (fk_contact_id), PRIMARY KEY(prospective_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_ref_states (state_id VARCHAR(2) NOT NULL, state_name VARCHAR(50) NOT NULL, PRIMARY KEY(state_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_user (user_id INT AUTO_INCREMENT NOT NULL, fk_contact_id INT NOT NULL, username VARCHAR(50) NOT NULL, username_canonical VARCHAR(50) NOT NULL, password VARCHAR(255) NOT NULL, is_admin TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', credentials_expired TINYINT(1) NOT NULL, credentials_expired_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_E41F246392FC23A8 (username_canonical), UNIQUE INDEX UNIQ_E41F246317CFEFD9 (fk_contact_id), PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_ref_payment_plan_type (payment_plan_type_id INT AUTO_INCREMENT NOT NULL, fk_org_id INT NOT NULL, name VARCHAR(100) NOT NULL, INDEX IDX_3F017D5BD42D148B (fk_org_id), PRIMARY KEY(payment_plan_type_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_program (program_id INT AUTO_INCREMENT NOT NULL, fk_org_id INT NOT NULL, fk_program_type_id INT NOT NULL, program_name VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, expiration_date DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_65B3F8EFD42D148B (fk_org_id), INDEX IDX_65B3F8EF97052072 (fk_program_type_id), PRIMARY KEY(program_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_program_school (fk_program_id INT NOT NULL, fk_school_id INT NOT NULL, INDEX IDX_571B0547CED6CD99 (fk_program_id), INDEX IDX_571B0547A273907E (fk_school_id), PRIMARY KEY(fk_program_id, fk_school_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_program_payment_plan (program_payment_plan_id INT AUTO_INCREMENT NOT NULL, fk_program_id INT NOT NULL, fk_payment_plan_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION DEFAULT NULL, payments_data LONGTEXT NOT NULL COMMENT '(DC2Type:array)', max_discount_percentage VARCHAR(10) DEFAULT NULL, max_discount_dollar_amount DOUBLE PRECISION DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_9C4DCB80CED6CD99 (fk_program_id), INDEX IDX_9C4DCB80BB6941ED (fk_payment_plan_type_id), PRIMARY KEY(program_payment_plan_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_ref_program_type (program_type_id INT AUTO_INCREMENT NOT NULL, fk_org_id INT NOT NULL, name VARCHAR(20) NOT NULL, INDEX IDX_4A9CF799D42D148B (fk_org_id), PRIMARY KEY(program_type_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_class (class_id INT AUTO_INCREMENT NOT NULL, fk_org_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, tokens INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_36995CBBD42D148B (fk_org_id), PRIMARY KEY(class_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_ref_class_type (class_type_id INT AUTO_INCREMENT NOT NULL, fk_org_id INT NOT NULL, class_type_name VARCHAR(20) NOT NULL, INDEX IDX_C4A36EDED42D148B (fk_org_id), PRIMARY KEY(class_type_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_class_type_credit (class_type_credit_id INT AUTO_INCREMENT NOT NULL, fk_class_id INT NOT NULL, fk_class_type_id INT NOT NULL, value INT NOT NULL, INDEX IDX_76E61F4AD82848CE (fk_class_id), INDEX IDX_76E61F4A64739EAD (fk_class_type_id), UNIQUE INDEX class_type_credit_uniq (fk_class_id, fk_class_type_id), PRIMARY KEY(class_type_credit_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_school (school_id INT AUTO_INCREMENT NOT NULL, fk_contact_id INT NOT NULL, fk_corporation_id INT NOT NULL, name VARCHAR(255) NOT NULL, legacy_id VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C546EC2F17CFEFD9 (fk_contact_id), UNIQUE INDEX UNIQ_C546EC2FC766BA80 (fk_corporation_id), PRIMARY KEY(school_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_student (student_id INT AUTO_INCREMENT NOT NULL, fk_contact_id INT NOT NULL, fk_student_status_id INT NOT NULL, UNIQUE INDEX UNIQ_407D205817CFEFD9 (fk_contact_id), UNIQUE INDEX UNIQ_407D2058C2AE4FC5 (fk_student_status_id), PRIMARY KEY(student_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_ref_student_status (student_status_id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(student_status_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_student_token (student_token_id INT AUTO_INCREMENT NOT NULL, fk_student_id INT NOT NULL, amount INT NOT NULL, expire_date DATE NOT NULL, INDEX IDX_1DDC1DB83BFA8589 (fk_student_id), PRIMARY KEY(student_token_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_instructor (instructor_id INT AUTO_INCREMENT NOT NULL, fk_contact_id INT NOT NULL, qualifications VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_45B69F9C17CFEFD9 (fk_contact_id), PRIMARY KEY(instructor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_billee (billee_id INT AUTO_INCREMENT NOT NULL, fk_contact_id INT NOT NULL, UNIQUE INDEX UNIQ_496B5C9017CFEFD9 (fk_contact_id), PRIMARY KEY(billee_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE BilleePaymentMethod (id INT AUTO_INCREMENT NOT NULL, fk_billee_id INT NOT NULL, fk_payment_method_id INT NOT NULL, cc_num VARCHAR(100) NOT NULL, cc_expiration_date DATE NOT NULL, cvv_number VARCHAR(5) NOT NULL, routing_num VARCHAR(100) NOT NULL, account_num VARCHAR(100) NOT NULL, INDEX IDX_C5AB6038CD6BDB12 (fk_billee_id), INDEX IDX_C5AB6038949698B7 (fk_payment_method_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_schedule (schedule_id INT AUTO_INCREMENT NOT NULL, fk_school_id INT NOT NULL, fk_class_id INT NOT NULL, fk_instrutor_id INT NOT NULL, student_limit INT NOT NULL, day_of_week VARCHAR(255) NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_80AFF7A4A273907E (fk_school_id), INDEX IDX_80AFF7A4D82848CE (fk_class_id), INDEX IDX_80AFF7A4DEA2318C (fk_instrutor_id), PRIMARY KEY(schedule_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_roster (roster_id INT AUTO_INCREMENT NOT NULL, fk_schedule_id INT NOT NULL, fk_student_id INT NOT NULL, roster_date DATE NOT NULL, roster_status VARCHAR(255) NOT NULL, INDEX IDX_5C619B6DCDFD6EE1 (fk_schedule_id), INDEX IDX_5C619B6D3BFA8589 (fk_student_id), PRIMARY KEY(roster_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_charge (charge_id INT AUTO_INCREMENT NOT NULL, amount DOUBLE PRECISION NOT NULL, description LONGTEXT NOT NULL, created_date DATETIME NOT NULL, misc VARCHAR(255) NOT NULL, PRIMARY KEY(charge_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_ref_income_type (incomet_type_id INT AUTO_INCREMENT NOT NULL, fk_org_id INT NOT NULL, name VARCHAR(100) NOT NULL, INDEX IDX_83D27AB7D42D148B (fk_org_id), PRIMARY KEY(incomet_type_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_ref_payment_method (payment_method_id INT AUTO_INCREMENT NOT NULL, fk_org_id INT NOT NULL, name VARCHAR(100) NOT NULL, payment_type VARCHAR(20) NOT NULL, is_recurring TINYINT(1) NOT NULL, INDEX IDX_1D3254BED42D148B (fk_org_id), PRIMARY KEY(payment_method_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_ref_payment_term (payment_term_id INT AUTO_INCREMENT NOT NULL, fk_org_id INT NOT NULL, name VARCHAR(20) NOT NULL, INDEX IDX_4F28854ED42D148B (fk_org_id), PRIMARY KEY(payment_term_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_ref_rank (rank_id INT AUTO_INCREMENT NOT NULL, fk_org_id INT NOT NULL, name VARCHAR(100) NOT NULL, rank_order INT NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_459D4F3DD42D148B (fk_org_id), PRIMARY KEY(rank_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tsk_contract (contract_id INT AUTO_INCREMENT NOT NULL, fk_program_id INT NOT NULL, fk_school_id INT NOT NULL, fk_student_id INT NOT NULL, legacy_contract_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, payment_terms LONGTEXT NOT NULL COMMENT '(DC2Type:array)', created_by VARCHAR(255) DEFAULT NULL, created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_3318CE06CED6CD99 (fk_program_id), INDEX IDX_3318CE06A273907E (fk_school_id), UNIQUE INDEX UNIQ_3318CE063BFA8589 (fk_student_id), PRIMARY KEY(contract_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_classes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_type VARCHAR(200) NOT NULL, UNIQUE INDEX UNIQ_69DD750638A36066 (class_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_security_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, identifier VARCHAR(200) NOT NULL, username TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8835EE78772E836AF85E0677 (identifier, username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_object_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_object_identity_id INT UNSIGNED DEFAULT NULL, class_id INT UNSIGNED NOT NULL, object_identifier VARCHAR(100) NOT NULL, entries_inheriting TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_9407E5494B12AD6EA000B10 (object_identifier, class_id), INDEX IDX_9407E54977FA751A (parent_object_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_object_identity_ancestors (object_identity_id INT UNSIGNED NOT NULL, ancestor_id INT UNSIGNED NOT NULL, INDEX IDX_825DE2993D9AB4A6 (object_identity_id), INDEX IDX_825DE299C671CEA1 (ancestor_id), PRIMARY KEY(object_identity_id, ancestor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_entries (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_id INT UNSIGNED NOT NULL, object_identity_id INT UNSIGNED DEFAULT NULL, security_identity_id INT UNSIGNED NOT NULL, field_name VARCHAR(50) DEFAULT NULL, ace_order SMALLINT UNSIGNED NOT NULL, mask INT NOT NULL, granting TINYINT(1) NOT NULL, granting_strategy VARCHAR(30) NOT NULL, audit_success TINYINT(1) NOT NULL, audit_failure TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_46C8B806EA000B103D9AB4A64DEF17BCE4289BF4 (class_id, object_identity_id, field_name, ace_order), INDEX IDX_46C8B806EA000B103D9AB4A6DF9183C9 (class_id, object_identity_id, security_identity_id), INDEX IDX_46C8B806EA000B10 (class_id), INDEX IDX_46C8B8063D9AB4A6 (object_identity_id), INDEX IDX_46C8B806DF9183C9 (security_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE tsk_contact ADD CONSTRAINT FK_BB3C6953D42D148B FOREIGN KEY (fk_org_id) REFERENCES tsk_organization (org_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_contact_school ADD CONSTRAINT FK_93C90C9C17CFEFD9 FOREIGN KEY (fk_contact_id) REFERENCES tsk_contact (contact_id)");
        $this->addSql("ALTER TABLE tsk_contact_school ADD CONSTRAINT FK_93C90C9CA273907E FOREIGN KEY (fk_school_id) REFERENCES tsk_school (school_id)");
        $this->addSql("ALTER TABLE tsk_corporation_contact ADD CONSTRAINT FK_AF597804C766BA80 FOREIGN KEY (fk_corporation_id) REFERENCES tsk_corporation (corporation_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_corporation_contact ADD CONSTRAINT FK_AF59780417CFEFD9 FOREIGN KEY (fk_contact_id) REFERENCES tsk_contact (contact_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_emergency_contact ADD CONSTRAINT FK_8F82B42A17CFEFD9 FOREIGN KEY (fk_contact_id) REFERENCES tsk_contact (contact_id)");
        $this->addSql("ALTER TABLE tsk_login_history ADD CONSTRAINT FK_FD4BAB285741EEB9 FOREIGN KEY (fk_user_id) REFERENCES tsk_user (user_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_organization ADD CONSTRAINT FK_A7E9F78D17CFEFD9 FOREIGN KEY (fk_contact_id) REFERENCES tsk_contact (contact_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_organization ADD CONSTRAINT FK_A7E9F78DC766BA80 FOREIGN KEY (fk_corporation_id) REFERENCES tsk_corporation (corporation_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_prospective ADD CONSTRAINT FK_4A969AFA17CFEFD9 FOREIGN KEY (fk_contact_id) REFERENCES tsk_contact (contact_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_user ADD CONSTRAINT FK_E41F246317CFEFD9 FOREIGN KEY (fk_contact_id) REFERENCES tsk_contact (contact_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_ref_payment_plan_type ADD CONSTRAINT FK_3F017D5BD42D148B FOREIGN KEY (fk_org_id) REFERENCES tsk_organization (org_id)");
        $this->addSql("ALTER TABLE tsk_program ADD CONSTRAINT FK_65B3F8EFD42D148B FOREIGN KEY (fk_org_id) REFERENCES tsk_organization (org_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_program ADD CONSTRAINT FK_65B3F8EF97052072 FOREIGN KEY (fk_program_type_id) REFERENCES tsk_ref_program_type (program_type_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_program_school ADD CONSTRAINT FK_571B0547CED6CD99 FOREIGN KEY (fk_program_id) REFERENCES tsk_program (program_id)");
        $this->addSql("ALTER TABLE tsk_program_school ADD CONSTRAINT FK_571B0547A273907E FOREIGN KEY (fk_school_id) REFERENCES tsk_school (school_id)");
        $this->addSql("ALTER TABLE tsk_program_payment_plan ADD CONSTRAINT FK_9C4DCB80CED6CD99 FOREIGN KEY (fk_program_id) REFERENCES tsk_program (program_id)");
        $this->addSql("ALTER TABLE tsk_program_payment_plan ADD CONSTRAINT FK_9C4DCB80BB6941ED FOREIGN KEY (fk_payment_plan_type_id) REFERENCES tsk_ref_payment_plan_type (payment_plan_type_id)");
        $this->addSql("ALTER TABLE tsk_ref_program_type ADD CONSTRAINT FK_4A9CF799D42D148B FOREIGN KEY (fk_org_id) REFERENCES tsk_organization (org_id)");
        $this->addSql("ALTER TABLE tsk_class ADD CONSTRAINT FK_36995CBBD42D148B FOREIGN KEY (fk_org_id) REFERENCES tsk_organization (org_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_ref_class_type ADD CONSTRAINT FK_C4A36EDED42D148B FOREIGN KEY (fk_org_id) REFERENCES tsk_organization (org_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_class_type_credit ADD CONSTRAINT FK_76E61F4AD82848CE FOREIGN KEY (fk_class_id) REFERENCES tsk_class (class_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_class_type_credit ADD CONSTRAINT FK_76E61F4A64739EAD FOREIGN KEY (fk_class_type_id) REFERENCES tsk_ref_class_type (class_type_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_school ADD CONSTRAINT FK_C546EC2F17CFEFD9 FOREIGN KEY (fk_contact_id) REFERENCES tsk_contact (contact_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_school ADD CONSTRAINT FK_C546EC2FC766BA80 FOREIGN KEY (fk_corporation_id) REFERENCES tsk_corporation (corporation_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_student ADD CONSTRAINT FK_407D205817CFEFD9 FOREIGN KEY (fk_contact_id) REFERENCES tsk_contact (contact_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_student ADD CONSTRAINT FK_407D2058C2AE4FC5 FOREIGN KEY (fk_student_status_id) REFERENCES tsk_ref_student_status (student_status_id)");
        $this->addSql("ALTER TABLE tsk_student_token ADD CONSTRAINT FK_1DDC1DB83BFA8589 FOREIGN KEY (fk_student_id) REFERENCES tsk_student (student_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_instructor ADD CONSTRAINT FK_45B69F9C17CFEFD9 FOREIGN KEY (fk_contact_id) REFERENCES tsk_contact (contact_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_billee ADD CONSTRAINT FK_496B5C9017CFEFD9 FOREIGN KEY (fk_contact_id) REFERENCES tsk_contact (contact_id)");
        $this->addSql("ALTER TABLE BilleePaymentMethod ADD CONSTRAINT FK_C5AB6038CD6BDB12 FOREIGN KEY (fk_billee_id) REFERENCES tsk_billee (billee_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE BilleePaymentMethod ADD CONSTRAINT FK_C5AB6038949698B7 FOREIGN KEY (fk_payment_method_id) REFERENCES tsk_ref_payment_method (payment_method_id)");
        $this->addSql("ALTER TABLE tsk_schedule ADD CONSTRAINT FK_80AFF7A4A273907E FOREIGN KEY (fk_school_id) REFERENCES tsk_school (school_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_schedule ADD CONSTRAINT FK_80AFF7A4D82848CE FOREIGN KEY (fk_class_id) REFERENCES tsk_class (class_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_schedule ADD CONSTRAINT FK_80AFF7A4DEA2318C FOREIGN KEY (fk_instrutor_id) REFERENCES tsk_instructor (instructor_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_roster ADD CONSTRAINT FK_5C619B6DCDFD6EE1 FOREIGN KEY (fk_schedule_id) REFERENCES tsk_schedule (schedule_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_roster ADD CONSTRAINT FK_5C619B6D3BFA8589 FOREIGN KEY (fk_student_id) REFERENCES tsk_student (student_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_ref_income_type ADD CONSTRAINT FK_83D27AB7D42D148B FOREIGN KEY (fk_org_id) REFERENCES tsk_organization (org_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_ref_payment_method ADD CONSTRAINT FK_1D3254BED42D148B FOREIGN KEY (fk_org_id) REFERENCES tsk_organization (org_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_ref_payment_term ADD CONSTRAINT FK_4F28854ED42D148B FOREIGN KEY (fk_org_id) REFERENCES tsk_organization (org_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_ref_rank ADD CONSTRAINT FK_459D4F3DD42D148B FOREIGN KEY (fk_org_id) REFERENCES tsk_organization (org_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_contract ADD CONSTRAINT FK_3318CE06CED6CD99 FOREIGN KEY (fk_program_id) REFERENCES tsk_program (program_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_contract ADD CONSTRAINT FK_3318CE06A273907E FOREIGN KEY (fk_school_id) REFERENCES tsk_school (school_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tsk_contract ADD CONSTRAINT FK_3318CE063BFA8589 FOREIGN KEY (fk_student_id) REFERENCES tsk_student (student_id)");
        $this->addSql("ALTER TABLE acl_object_identities ADD CONSTRAINT FK_9407E54977FA751A FOREIGN KEY (parent_object_identity_id) REFERENCES acl_object_identities (id)");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE2993D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE299C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806EA000B10 FOREIGN KEY (class_id) REFERENCES acl_classes (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B8063D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806DF9183C9 FOREIGN KEY (security_identity_id) REFERENCES acl_security_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE tsk_contact_school DROP FOREIGN KEY FK_93C90C9C17CFEFD9");
        $this->addSql("ALTER TABLE tsk_corporation_contact DROP FOREIGN KEY FK_AF59780417CFEFD9");
        $this->addSql("ALTER TABLE tsk_emergency_contact DROP FOREIGN KEY FK_8F82B42A17CFEFD9");
        $this->addSql("ALTER TABLE tsk_organization DROP FOREIGN KEY FK_A7E9F78D17CFEFD9");
        $this->addSql("ALTER TABLE tsk_prospective DROP FOREIGN KEY FK_4A969AFA17CFEFD9");
        $this->addSql("ALTER TABLE tsk_user DROP FOREIGN KEY FK_E41F246317CFEFD9");
        $this->addSql("ALTER TABLE tsk_school DROP FOREIGN KEY FK_C546EC2F17CFEFD9");
        $this->addSql("ALTER TABLE tsk_student DROP FOREIGN KEY FK_407D205817CFEFD9");
        $this->addSql("ALTER TABLE tsk_instructor DROP FOREIGN KEY FK_45B69F9C17CFEFD9");
        $this->addSql("ALTER TABLE tsk_billee DROP FOREIGN KEY FK_496B5C9017CFEFD9");
        $this->addSql("ALTER TABLE tsk_corporation_contact DROP FOREIGN KEY FK_AF597804C766BA80");
        $this->addSql("ALTER TABLE tsk_organization DROP FOREIGN KEY FK_A7E9F78DC766BA80");
        $this->addSql("ALTER TABLE tsk_school DROP FOREIGN KEY FK_C546EC2FC766BA80");
        $this->addSql("ALTER TABLE tsk_contact DROP FOREIGN KEY FK_BB3C6953D42D148B");
        $this->addSql("ALTER TABLE tsk_ref_payment_plan_type DROP FOREIGN KEY FK_3F017D5BD42D148B");
        $this->addSql("ALTER TABLE tsk_program DROP FOREIGN KEY FK_65B3F8EFD42D148B");
        $this->addSql("ALTER TABLE tsk_ref_program_type DROP FOREIGN KEY FK_4A9CF799D42D148B");
        $this->addSql("ALTER TABLE tsk_class DROP FOREIGN KEY FK_36995CBBD42D148B");
        $this->addSql("ALTER TABLE tsk_ref_class_type DROP FOREIGN KEY FK_C4A36EDED42D148B");
        $this->addSql("ALTER TABLE tsk_ref_income_type DROP FOREIGN KEY FK_83D27AB7D42D148B");
        $this->addSql("ALTER TABLE tsk_ref_payment_method DROP FOREIGN KEY FK_1D3254BED42D148B");
        $this->addSql("ALTER TABLE tsk_ref_payment_term DROP FOREIGN KEY FK_4F28854ED42D148B");
        $this->addSql("ALTER TABLE tsk_ref_rank DROP FOREIGN KEY FK_459D4F3DD42D148B");
        $this->addSql("ALTER TABLE tsk_login_history DROP FOREIGN KEY FK_FD4BAB285741EEB9");
        $this->addSql("ALTER TABLE tsk_program_payment_plan DROP FOREIGN KEY FK_9C4DCB80BB6941ED");
        $this->addSql("ALTER TABLE tsk_program_school DROP FOREIGN KEY FK_571B0547CED6CD99");
        $this->addSql("ALTER TABLE tsk_program_payment_plan DROP FOREIGN KEY FK_9C4DCB80CED6CD99");
        $this->addSql("ALTER TABLE tsk_contract DROP FOREIGN KEY FK_3318CE06CED6CD99");
        $this->addSql("ALTER TABLE tsk_program DROP FOREIGN KEY FK_65B3F8EF97052072");
        $this->addSql("ALTER TABLE tsk_class_type_credit DROP FOREIGN KEY FK_76E61F4AD82848CE");
        $this->addSql("ALTER TABLE tsk_schedule DROP FOREIGN KEY FK_80AFF7A4D82848CE");
        $this->addSql("ALTER TABLE tsk_class_type_credit DROP FOREIGN KEY FK_76E61F4A64739EAD");
        $this->addSql("ALTER TABLE tsk_contact_school DROP FOREIGN KEY FK_93C90C9CA273907E");
        $this->addSql("ALTER TABLE tsk_program_school DROP FOREIGN KEY FK_571B0547A273907E");
        $this->addSql("ALTER TABLE tsk_schedule DROP FOREIGN KEY FK_80AFF7A4A273907E");
        $this->addSql("ALTER TABLE tsk_contract DROP FOREIGN KEY FK_3318CE06A273907E");
        $this->addSql("ALTER TABLE tsk_student_token DROP FOREIGN KEY FK_1DDC1DB83BFA8589");
        $this->addSql("ALTER TABLE tsk_roster DROP FOREIGN KEY FK_5C619B6D3BFA8589");
        $this->addSql("ALTER TABLE tsk_contract DROP FOREIGN KEY FK_3318CE063BFA8589");
        $this->addSql("ALTER TABLE tsk_student DROP FOREIGN KEY FK_407D2058C2AE4FC5");
        $this->addSql("ALTER TABLE tsk_schedule DROP FOREIGN KEY FK_80AFF7A4DEA2318C");
        $this->addSql("ALTER TABLE BilleePaymentMethod DROP FOREIGN KEY FK_C5AB6038CD6BDB12");
        $this->addSql("ALTER TABLE tsk_roster DROP FOREIGN KEY FK_5C619B6DCDFD6EE1");
        $this->addSql("ALTER TABLE BilleePaymentMethod DROP FOREIGN KEY FK_C5AB6038949698B7");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806EA000B10");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806DF9183C9");
        $this->addSql("ALTER TABLE acl_object_identities DROP FOREIGN KEY FK_9407E54977FA751A");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE2993D9AB4A6");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE299C671CEA1");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B8063D9AB4A6");
        $this->addSql("DROP TABLE tsk_contact");
        $this->addSql("DROP TABLE tsk_contact_school");
        $this->addSql("DROP TABLE tsk_corporation");
        $this->addSql("DROP TABLE tsk_corporation_contact");
        $this->addSql("DROP TABLE tsk_emergency_contact");
        $this->addSql("DROP TABLE tsk_login_history");
        $this->addSql("DROP TABLE tsk_organization");
        $this->addSql("DROP TABLE tsk_prospective");
        $this->addSql("DROP TABLE tsk_ref_states");
        $this->addSql("DROP TABLE tsk_user");
        $this->addSql("DROP TABLE tsk_ref_payment_plan_type");
        $this->addSql("DROP TABLE tsk_program");
        $this->addSql("DROP TABLE tsk_program_school");
        $this->addSql("DROP TABLE tsk_program_payment_plan");
        $this->addSql("DROP TABLE tsk_ref_program_type");
        $this->addSql("DROP TABLE tsk_class");
        $this->addSql("DROP TABLE tsk_ref_class_type");
        $this->addSql("DROP TABLE tsk_class_type_credit");
        $this->addSql("DROP TABLE tsk_school");
        $this->addSql("DROP TABLE tsk_student");
        $this->addSql("DROP TABLE tsk_ref_student_status");
        $this->addSql("DROP TABLE tsk_student_token");
        $this->addSql("DROP TABLE tsk_instructor");
        $this->addSql("DROP TABLE tsk_billee");
        $this->addSql("DROP TABLE BilleePaymentMethod");
        $this->addSql("DROP TABLE tsk_schedule");
        $this->addSql("DROP TABLE tsk_roster");
        $this->addSql("DROP TABLE tsk_charge");
        $this->addSql("DROP TABLE tsk_ref_income_type");
        $this->addSql("DROP TABLE tsk_ref_payment_method");
        $this->addSql("DROP TABLE tsk_ref_payment_term");
        $this->addSql("DROP TABLE tsk_ref_rank");
        $this->addSql("DROP TABLE tsk_contract");
        $this->addSql("DROP TABLE acl_classes");
        $this->addSql("DROP TABLE acl_security_identities");
        $this->addSql("DROP TABLE acl_object_identities");
        $this->addSql("DROP TABLE acl_object_identity_ancestors");
        $this->addSql("DROP TABLE acl_entries");
    }
}
