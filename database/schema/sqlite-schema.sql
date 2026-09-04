CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "sm_academic_years"(
  "id" integer primary key autoincrement not null,
  "year" varchar not null,
  "title" varchar not null,
  "starting_date" date not null,
  "ending_date" date not null,
  "copy_with_academic_year" varchar,
  "active_status" integer not null default '1',
  "created_at" varchar,
  "updated_at" varchar,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "password_resets"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime
);
CREATE INDEX "password_resets_email_index" on "password_resets"("email");
CREATE TABLE IF NOT EXISTS "chat_statuses"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "status" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "infix_roles"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "type" varchar not null default 'System',
  "active_status" integer not null default '1',
  "created_by" varchar default '1',
  "updated_by" varchar default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  "is_saas" integer default '0',
  "saas_schools" varchar,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "roles"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "type" varchar not null default 'System',
  "active_status" integer not null default '1',
  "created_by" varchar default '1',
  "updated_by" varchar default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_base_groups"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_base_setups"(
  "id" integer primary key autoincrement not null,
  "base_setup_name" varchar not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "base_group_id" integer default '1',
  "school_id" integer default '1',
  foreign key("base_group_id") references "sm_base_groups"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_income_heads"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_bank_accounts"(
  "id" integer primary key autoincrement not null,
  "bank_name" varchar,
  "account_name" varchar,
  "account_number" varchar,
  "account_type" varchar,
  "opening_balance" double not null default '0',
  "current_balance" double not null default '0',
  "note" text,
  "active_status" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_payment_gateway_settings"(
  "id" integer primary key autoincrement not null,
  "gateway_name" varchar,
  "gateway_username" varchar,
  "gateway_password" varchar,
  "gateway_signature" varchar,
  "gateway_client_id" varchar,
  "gateway_mode" varchar,
  "gateway_secret_key" varchar,
  "gateway_secret_word" varchar,
  "gateway_publisher_key" varchar,
  "gateway_private_key" varchar,
  "active_status" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "bank_details" text,
  "cheque_details" text,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "service_charge" tinyint(1) default '0',
  "charge_type" varchar,
  "charge" float default '0',
  "mercado_pago_public_key" varchar,
  "mercado_pago_acces_token" varchar,
  "phone_pay_merchant_id" varchar default 'MERCHANTUAT',
  "phone_pay_salt_key" varchar default '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399',
  "phone_pay_salt_index" varchar default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_payment_methhods"(
  "id" integer primary key autoincrement not null,
  "method" varchar not null,
  "type" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "gateway_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("gateway_id") references "sm_payment_gateway_settings"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_sessions"(
  "id" integer primary key autoincrement not null,
  "session" varchar not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_instructions"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "description" text not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_question_levels"(
  "id" integer primary key autoincrement not null,
  "level" varchar not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_marks_grades"(
  "id" integer primary key autoincrement not null,
  "grade_name" varchar,
  "gpa" float,
  "from" float,
  "up" float,
  "percent_from" float,
  "percent_upto" float,
  "description" text,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_hourly_rates"(
  "id" integer primary key autoincrement not null,
  "grade" varchar,
  "rate" integer,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_leave_types"(
  "id" integer primary key autoincrement not null,
  "type" varchar,
  "total_days" integer,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_expense_heads"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "description" text,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_homeworks"(
  "id" integer primary key autoincrement not null,
  "homework_date" date,
  "submission_date" date,
  "description" varchar,
  "percentage" varchar,
  "status" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "evaluated_by" integer,
  "student_id" integer,
  "subject_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("evaluated_by") references "users"("id") on delete cascade,
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_hr_salary_templates"(
  "id" integer primary key autoincrement not null,
  "salary_grades" varchar,
  "salary_basic" varchar,
  "overtime_rate" varchar,
  "house_rent" integer,
  "provident_fund" integer,
  "gross_salary" integer,
  "total_deduction" integer,
  "net_salary" integer,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_hr_payroll_generates"(
  "id" integer primary key autoincrement not null,
  "basic_salary" double,
  "total_earning" double,
  "total_deduction" double,
  "gross_salary" double,
  "tax" double,
  "net_salary" double,
  "payroll_month" varchar,
  "payroll_year" varchar,
  "payroll_status" varchar,
  "payment_mode" varchar,
  "payment_date" date,
  "bank_id" integer,
  "note" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "staff_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "is_partial" integer,
  "paid_amount" integer,
  foreign key("staff_id") references "sm_staffs"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_exam_marks_registers"(
  "id" integer primary key autoincrement not null,
  "obtained_marks" varchar,
  "exam_date" date,
  "comments" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "exam_id" integer not null,
  "student_id" integer,
  "subject_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("exam_id") references "sm_exams"("id") on delete cascade,
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_marks_send_sms"(
  "id" integer primary key autoincrement not null,
  "sms_send_status" integer not null default '1',
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "exam_id" integer,
  "student_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("exam_id") references "sm_exams"("id") on delete cascade,
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_class_routines"(
  "id" integer primary key autoincrement not null,
  "monday" varchar,
  "monday_start_from" varchar,
  "monday_end_to" varchar,
  "monday_room_id" integer,
  "tuesday" varchar,
  "tuesday_start_from" varchar,
  "tuesday_end_to" varchar,
  "tuesday_room_id" integer,
  "wednesday" varchar,
  "wednesday_start_from" varchar,
  "wednesday_end_to" varchar,
  "wednesday_room_id" integer,
  "thursday" varchar,
  "thursday_start_from" varchar,
  "thursday_end_to" varchar,
  "thursday_room_id" integer,
  "friday" varchar,
  "friday_start_from" varchar,
  "friday_end_to" varchar,
  "friday_room_id" integer,
  "saturday" varchar,
  "saturday_start_from" varchar,
  "saturday_end_to" varchar,
  "saturday_room_id" integer,
  "sunday" varchar,
  "sunday_start_from" varchar,
  "sunday_end_to" varchar,
  "sunday_room_id" integer,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "class_id" integer,
  "section_id" integer,
  "subject_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("section_id") references "sm_sections"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_class_times"(
  "id" integer primary key autoincrement not null,
  "type" varchar check("type" in('exam', 'class')),
  "period" varchar,
  "start_time" time,
  "end_time" time,
  "is_break" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "languages"(
  "id" integer primary key autoincrement not null,
  "code" varchar not null,
  "name" varchar not null,
  "native" varchar not null,
  "rtl" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  "active_status" integer not null default '0',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_modules"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "active_status" integer not null default '1',
  "order" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_languages"(
  "id" integer primary key autoincrement not null,
  "language_name" varchar,
  "native" varchar,
  "language_universal" varchar,
  "active_status" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "lang_id" integer default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("lang_id") references "languages"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_date_formats"(
  "id" integer primary key autoincrement not null,
  "format" varchar,
  "normal_view" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_news_categories"(
  "id" integer primary key autoincrement not null,
  "category_name" varchar not null,
  "type" varchar not null default 'news',
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer not null default '1'
);
CREATE TABLE IF NOT EXISTS "oauth_auth_codes"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "client_id" integer not null,
  "scopes" text,
  "revoked" tinyint(1) not null,
  "expires_at" datetime
);
CREATE TABLE IF NOT EXISTS "oauth_access_tokens"(
  "id" varchar not null,
  "user_id" integer,
  "client_id" integer not null,
  "name" varchar,
  "scopes" varchar,
  "revoked" varchar not null,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "oauth_access_tokens_user_id_index" on "oauth_access_tokens"(
  "user_id"
);
CREATE TABLE IF NOT EXISTS "oauth_refresh_tokens"(
  "id" integer primary key autoincrement not null,
  "access_token_id" integer,
  "revoked" tinyint(1) not null,
  "expires_at" datetime
);
CREATE INDEX "oauth_refresh_tokens_access_token_id_index" on "oauth_refresh_tokens"(
  "access_token_id"
);
CREATE TABLE IF NOT EXISTS "oauth_clients"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "provider" varchar,
  "name" varchar not null,
  "secret" varchar not null,
  "redirect" text not null,
  "personal_access_client" tinyint(1) not null,
  "password_client" tinyint(1) not null,
  "revoked" tinyint(1) not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "oauth_clients_user_id_index" on "oauth_clients"("user_id");
CREATE TABLE IF NOT EXISTS "oauth_personal_access_clients"(
  "id" integer primary key autoincrement not null,
  "client_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "oauth_personal_access_clients_client_id_index" on "oauth_personal_access_clients"(
  "client_id"
);
CREATE TABLE IF NOT EXISTS "sm_notice_boards"(
  "id" integer primary key autoincrement not null,
  "notice_title" varchar,
  "notice_message" text,
  "notice_date" date,
  "publish_on" date,
  "inform_to" varchar,
  "active_status" integer not null default '1',
  "is_published" integer default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_send_messages"(
  "id" integer primary key autoincrement not null,
  "message_title" varchar,
  "message_des" varchar,
  "notice_date" date,
  "publish_on" date,
  "message_to" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_holidays"(
  "id" integer primary key autoincrement not null,
  "holiday_title" varchar,
  "details" varchar,
  "from_date" date,
  "to_date" date,
  "upload_image_file" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_fees_assign_discounts"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "student_id" integer,
  "record_id" integer,
  "fees_discount_id" integer,
  "fees_type_id" integer,
  "fees_group_id" integer,
  "applied_amount" double default '0',
  "unapplied_amount" double,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("fees_discount_id") references "sm_fees_discounts"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_homework_students"(
  "id" integer primary key autoincrement not null,
  "marks" varchar,
  "teacher_comments" varchar,
  "complete_status" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "student_id" integer,
  "homework_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("homework_id") references "sm_homeworks"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_upload_contents"(
  "id" integer primary key autoincrement not null,
  "content_title" varchar,
  "content_type" integer,
  "available_for_role" integer,
  "available_for_class" integer,
  "available_for_section" integer,
  "upload_date" date,
  "description" varchar,
  "upload_file" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_content_types"(
  "id" integer primary key autoincrement not null,
  "type_name" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_exam_schedule_subjects"(
  "id" integer primary key autoincrement not null,
  "date" date,
  "start_time" varchar,
  "end_time" varchar,
  "room" varchar,
  "full_mark" integer,
  "pass_mark" integer,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "exam_schedule_id" integer,
  "subject_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("exam_schedule_id") references "sm_exam_schedules"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_marks_registers"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "student_id" integer,
  "exam_id" integer,
  "class_id" integer,
  "section_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("exam_id") references "sm_exams"("id") on delete cascade,
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("section_id") references "sm_sections"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_marks_register_children"(
  "id" integer primary key autoincrement not null,
  "marks" integer,
  "abs" integer not null default '0',
  "gpa_point" float,
  "gpa_grade" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "marks_register_id" integer,
  "subject_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("marks_register_id") references "sm_marks_registers"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_seat_plans"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "exam_id" integer,
  "subject_id" integer,
  "class_id" integer,
  "section_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("exam_id") references "sm_exams"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("section_id") references "sm_sections"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_seat_plan_children"(
  "id" integer primary key autoincrement not null,
  "room_id" integer,
  "assign_students" integer,
  "start_time" time,
  "end_time" time,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "seat_plan_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("seat_plan_id") references "sm_seat_plans"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_exam_attendance_children"(
  "id" integer primary key autoincrement not null,
  "attendance_type" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "exam_attendance_id" integer,
  "student_record_id" integer,
  "class_id" integer,
  "section_id" integer,
  "student_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "shift_id" integer,
  foreign key("exam_attendance_id") references "sm_exam_attendances"("id") on delete cascade,
  foreign key("student_record_id") references "student_records"("id") on delete cascade,
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("section_id") references "sm_sections"("id") on delete cascade,
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_online_exam_questions"(
  "id" integer primary key autoincrement not null,
  "type" varchar,
  "mark" integer,
  "title" text,
  "trueFalse" varchar,
  "suitable_words" text,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "online_exam_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("online_exam_id") references "sm_online_exams"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_online_exam_question_mu_options"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "status" integer,
  "active_status" integer not null default '1',
  "online_exam_question_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("online_exam_question_id") references "sm_online_exam_questions"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_online_exam_marks"(
  "id" integer primary key autoincrement not null,
  "marks" integer,
  "abs" integer not null default '0',
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "student_id" integer,
  "subject_id" integer,
  "exam_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("exam_id") references "sm_exams"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_module_links"(
  "id" integer primary key autoincrement not null,
  "module_id" integer,
  "name" varchar,
  "route" varchar,
  "active_status" integer not null default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("module_id") references "sm_modules"("id") on delete cascade,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_inventory_payments"(
  "id" integer primary key autoincrement not null,
  "item_receive_sell_id" integer,
  "payment_date" date,
  "amount" float,
  "reference_no" varchar,
  "payment_type" varchar,
  "payment_method" integer,
  "notes" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_excel_formats"(
  "roll_no" varchar,
  "first_name" varchar,
  "last_name" varchar,
  "date_of_birth" varchar,
  "religion" varchar,
  "caste" varchar,
  "mobile" varchar,
  "email" varchar,
  "admission_date" varchar,
  "category" varchar,
  "blood_group" varchar,
  "height" varchar,
  "weight" varchar,
  "siblings_id" varchar,
  "father_name" varchar,
  "father_phone" varchar,
  "father_occupation" varchar,
  "mother_name" varchar,
  "mother_phone" varchar,
  "mother_occupation" varchar,
  "guardian_name" varchar,
  "guardian_relation" varchar,
  "guardian_email" varchar,
  "guardian_phone" varchar,
  "guardian_occupation" varchar,
  "guardian_address" varchar,
  "current_address" varchar,
  "permanent_address" varchar,
  "bank_account_no" varchar,
  "bank_name" varchar,
  "national_identification_no" varchar,
  "local_identification_no" varchar,
  "previous_school_details" varchar,
  "note" varchar,
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_item_issues"(
  "id" integer primary key autoincrement not null,
  "issue_to" integer,
  "issue_by" integer,
  "issue_date" date,
  "due_date" date,
  "quantity" integer,
  "issue_status" varchar,
  "note" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "role_id" integer,
  "item_category_id" integer,
  "item_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("role_id") references "roles"("id") on delete cascade,
  foreign key("item_category_id") references "sm_item_categories"("id") on delete cascade,
  foreign key("item_id") references "sm_items"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_sms_gateways"(
  "id" integer primary key autoincrement not null,
  "gateway_name" varchar,
  "type" varchar default 'com',
  "clickatell_username" varchar,
  "clickatell_password" varchar,
  "clickatell_api_id" varchar,
  "twilio_account_sid" varchar,
  "twilio_authentication_token" varchar,
  "twilio_registered_no" varchar,
  "msg91_authentication_key_sid" varchar,
  "msg91_sender_id" varchar,
  "msg91_route" varchar,
  "msg91_country_code" varchar,
  "textlocal_username" varchar,
  "textlocal_hash" varchar,
  "textlocal_sender" varchar,
  "device_info" text,
  "africatalking_username" varchar,
  "africatalking_api_key" varchar,
  "active_status" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "gateway_type" varchar,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_documents"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "student_staff_id" integer,
  "type" varchar,
  "file" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_timelines"(
  "id" integer primary key autoincrement not null,
  "staff_student_id" integer not null,
  "title" varchar,
  "date" date,
  "description" text,
  "file" varchar,
  "type" varchar,
  "visible_to_student" integer not null default '0',
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_question_bank_mu_options"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "status" integer,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "question_bank_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("question_bank_id") references "sm_question_banks"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_online_exam_question_assigns"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "online_exam_id" integer,
  "question_bank_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("online_exam_id") references "sm_online_exams"("id") on delete cascade,
  foreign key("question_bank_id") references "sm_question_banks"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_take_online_exams"(
  "id" integer primary key autoincrement not null,
  "status" integer not null default '0',
  "student_done" integer not null default '0',
  "total_marks" integer,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "record_id" integer,
  "student_id" integer,
  "online_exam_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("online_exam_id") references "sm_online_exams"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_take_online_exam_questions"(
  "id" integer primary key autoincrement not null,
  "trueFalse" varchar,
  "suitable_words" text,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "take_online_exam_id" integer,
  "question_bank_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("take_online_exam_id") references "sm_student_take_online_exams"("id") on delete cascade,
  foreign key("question_bank_id") references "sm_question_banks"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_take_onln_ex_ques_options"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "status" integer,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "take_online_exam_question_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("take_online_exam_question_id") references "sm_student_take_online_exam_questions"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_email_sms_logs"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "description" varchar,
  "send_date" date,
  "send_through" varchar,
  "send_to" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_general_settings"(
  "id" integer primary key autoincrement not null,
  "school_name" varchar,
  "site_title" varchar,
  "school_code" varchar,
  "address" varchar,
  "phone" varchar,
  "email" varchar,
  "file_size" varchar not null default '102400',
  "currency" varchar default 'USD',
  "currency_symbol" varchar default '$',
  "currency_format" varchar default 'symbol_amount',
  "promotionSetting" integer default '0',
  "logo" varchar,
  "favicon" varchar,
  "system_version" varchar default '9.1.6',
  "active_status" integer default '1',
  "currency_code" varchar default 'USD',
  "language_name" varchar default 'en',
  "session_year" varchar default '2026',
  "system_purchase_code" text,
  "system_activated_date" date,
  "last_update" date,
  "envato_user" varchar,
  "envato_item_id" varchar,
  "system_domain" varchar,
  "copyright_text" text,
  "api_url" integer not null default '1',
  "website_btn" integer not null default '1',
  "dashboard_btn" integer not null default '1',
  "report_btn" integer not null default '1',
  "style_btn" integer not null default '1',
  "ltl_rtl_btn" integer not null default '1',
  "lang_btn" integer not null default '1',
  "website_url" varchar,
  "ttl_rtl" integer not null default '2',
  "phone_number_privacy" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "week_start_id" integer,
  "time_zone_id" integer,
  "attendance_layout" integer default '1',
  "session_id" integer,
  "language_id" integer default '1',
  "date_format_id" integer default '1',
  "ss_page_load" integer default '3',
  "sub_topic_enable" tinyint(1) not null default '1',
  "school_id" integer default '1',
  "software_version" varchar,
  "email_driver" varchar not null default 'php',
  "fcm_key" text,
  "multiple_roll" integer default '0',
  "Lesson" integer default '1',
  "Chat" integer default '1',
  "FeesCollection" integer default '0',
  "income_head_id" integer default '0',
  "InfixBiometrics" integer default '0',
  "ResultReports" integer default '0',
  "TemplateSettings" integer default '1',
  "MenuManage" integer default '1',
  "RolePermission" integer default '1',
  "RazorPay" integer default '0',
  "Saas" integer default '1',
  "StudentAbsentNotification" integer default '1',
  "ParentRegistration" integer default '0',
  "Zoom" integer default '0',
  "BBB" integer default '0',
  "VideoWatch" integer default '0',
  "Jitsi" integer default '0',
  "OnlineExam" integer default '0',
  "SaasRolePermission" integer default '0',
  "BulkPrint" integer default '1',
  "HimalayaSms" integer default '1',
  "XenditPayment" integer default '1',
  "Wallet" integer default '1',
  "Lms" integer default '0',
  "ExamPlan" integer default '1',
  "University" integer default '0',
  "Gmeet" integer default '0',
  "KhaltiPayment" integer default '0',
  "Raudhahpay" integer default '0',
  "AppSlider" integer default '1',
  "BehaviourRecords" integer default '0',
  "DownloadCenter" integer default '1',
  "AiContent" integer default '0',
  "WhatsappSupport" integer default '0',
  "InAppLiveClass" integer default '0',
  "fees_status" integer default '1',
  "lms_checkout" integer default '0',
  "academic_id" integer,
  "is_comment" integer default '0',
  "auto_approve" integer default '0',
  "blog_search" integer default '1',
  "recent_blog" integer default '1',
  "un_academic_id" integer default '1',
  "direct_fees_assign" tinyint(1) not null default '0',
  "with_guardian" tinyint(1) not null default '1',
  "result_type" varchar,
  "preloader_status" tinyint(1) not null default '1',
  "preloader_style" integer not null default '3',
  "preloader_type" integer not null default '1',
  "preloader_image" varchar not null default 'public/uploads/settings/preloader/preloader1.gif',
  "due_fees_login" tinyint(1) not null default '0',
  "two_factor" tinyint(1) not null default '0',
  "active_theme" varchar not null default 'edulia',
  "queue_connection" varchar not null default 'database',
  "role_based_sidebar" tinyint(1) not null default '0',
  "AWSS3" tinyint(1) not null default '1',
  "teacher_email_view" integer not null default '1',
  "teacher_phone_view" integer not null default '1',
  "is_custom_saas" integer not null default '0',
  "qr_attendance" integer not null default '0',
  "qr_attendance_camera" integer not null default '0',
  "bio_api_url" varchar,
  "bio_staff_sms" tinyint(1) default '0',
  "bio_parent_sms" tinyint(1) default '0',
  "bio_student_sms" tinyint(1) default '0',
  "bio_api_key" varchar,
  "bio_api_secret" varchar,
  "bio_staff_start_time" varchar,
  "bio_staff_con_start_time" varchar,
  "shift_enable" integer not null default '0',
  "carry_forword_due_day" integer not null default '60',
  "student_grid_view" integer not null default '1',
  "staff_grid_view" integer not null default '1',
  foreign key("session_id") references "sm_academic_years"("id") on delete set null,
  foreign key("language_id") references "sm_languages"("id") on delete set null,
  foreign key("date_format_id") references "sm_date_formats"("id") on delete set null,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "sm_user_logs"(
  "id" integer primary key autoincrement not null,
  "ip_address" varchar,
  "user_agent" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  "role_id" integer,
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("role_id") references "infix_roles"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_email_settings"(
  "id" integer primary key autoincrement not null,
  "email_engine_type" varchar,
  "from_name" varchar,
  "from_email" varchar,
  "mail_driver" varchar,
  "mail_host" varchar,
  "mail_port" varchar,
  "mail_username" varchar,
  "mail_password" varchar,
  "mail_encryption" varchar,
  "school_id" integer default '1',
  "academic_id" integer,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_to_dos"(
  "id" integer primary key autoincrement not null,
  "todo_title" varchar,
  "date" date,
  "complete_status" varchar default 'P',
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_admission_query_followups"(
  "id" integer primary key autoincrement not null,
  "response" text,
  "note" text,
  "date" date,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "admission_query_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("admission_query_id") references "sm_admission_queries"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_backups"(
  "id" integer primary key autoincrement not null,
  "file_name" varchar,
  "source_link" varchar,
  "file_type" integer,
  "active_status" integer not null default '1',
  "lang_type" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_mark_stores"(
  "id" integer primary key autoincrement not null,
  "student_roll_no" integer not null default '1',
  "student_addmission_no" integer not null default '1',
  "total_marks" float not null default '0',
  "is_absent" integer not null default '1',
  "teacher_remarks" text,
  "created_at" datetime,
  "updated_at" datetime,
  "subject_id" integer,
  "exam_term_id" integer,
  "exam_setup_id" integer,
  "student_id" integer,
  "student_record_id" integer,
  "class_id" integer,
  "section_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "active_status" integer default '1',
  "shift_id" integer,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("exam_term_id") references "sm_exam_types"("id") on delete cascade,
  foreign key("exam_setup_id") references "sm_exam_setups"("id") on delete cascade,
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("student_record_id") references "student_records"("id") on delete cascade,
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("section_id") references "sm_sections"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_result_stores"(
  "id" integer primary key autoincrement not null,
  "student_roll_no" integer not null default '1',
  "student_addmission_no" integer not null default '1',
  "is_absent" integer not null default '0',
  "total_marks" float not null default '0',
  "total_gpa_point" float,
  "total_gpa_grade" varchar default '0',
  "teacher_remarks" text,
  "created_at" datetime,
  "updated_at" datetime,
  "exam_type_id" integer,
  "subject_id" integer,
  "active_status" integer default '1',
  "exam_setup_id" integer,
  "student_id" integer,
  "student_record_id" integer,
  "class_id" integer,
  "section_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "shift_id" integer,
  foreign key("exam_type_id") references "sm_exam_types"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("exam_setup_id") references "sm_exam_setups"("id") on delete cascade,
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("student_record_id") references "student_records"("id") on delete cascade,
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("section_id") references "sm_sections"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_weekends"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "order" integer,
  "is_weekend" integer,
  "active_status" integer not null default '1',
  "school_id" integer default '1',
  "created_at" varchar,
  "updated_at" varchar,
  "academic_id" integer,
  "zoom_order" integer,
  "gmeet_day" varchar,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "sm_countries"(
  "id" integer primary key autoincrement not null,
  "code" varchar,
  "name" varchar,
  "native" varchar,
  "phone" varchar,
  "continent" varchar,
  "capital" varchar,
  "currency" varchar,
  "languages" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_language_phrases"(
  "id" integer primary key autoincrement not null,
  "modules" text,
  "default_phrases" text,
  "en" text,
  "es" text,
  "bn" text,
  "fr" text,
  "school_id" integer default '1',
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_notifications"(
  "id" integer primary key autoincrement not null,
  "date" date,
  "message" varchar,
  "url" varchar,
  "is_read" integer not null default '0',
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer default '1',
  "role_id" integer not null default '1',
  "created_by" integer not null default '1',
  "updated_by" integer not null default '1',
  "school_id" integer not null default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "continents"(
  "id" integer primary key autoincrement not null,
  "code" varchar not null,
  "name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "countries"(
  "id" integer primary key autoincrement not null,
  "code" varchar not null,
  "name" varchar not null,
  "native" varchar not null,
  "phone" varchar not null,
  "continent" varchar not null,
  "capital" varchar not null,
  "currency" varchar not null,
  "languages" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_currencies"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "code" varchar,
  "symbol" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "currency_type" varchar default '2',
  "currency_position" varchar default '2',
  "space" tinyint(1) default '1',
  "decimal_digit" integer,
  "decimal_separator" varchar,
  "thousand_separator" varchar,
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_news"(
  "id" integer primary key autoincrement not null,
  "news_title" varchar not null,
  "view_count" integer,
  "active_status" integer,
  "image" varchar,
  "image_thumb" varchar,
  "news_body" text,
  "publish_date" date,
  "status" integer default '1',
  "is_global" integer default '1',
  "auto_approve" integer default '0',
  "is_comment" integer default '0',
  "order" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "category_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "mark_as_archive" integer not null default '0',
  foreign key("category_id") references "sm_news_categories"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_testimonials"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "designation" varchar not null,
  "institution_name" varchar not null,
  "image" varchar not null,
  "description" text not null,
  "star_rating" integer not null default '5',
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_contact_pages"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "description" text,
  "image" varchar,
  "button_text" varchar,
  "button_url" varchar,
  "address" varchar,
  "address_text" varchar,
  "phone" varchar,
  "phone_text" varchar,
  "email" varchar,
  "email_text" varchar,
  "latitude" varchar,
  "longitude" varchar,
  "zoom_level" integer,
  "google_map_address" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_contact_messages"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "phone" varchar,
  "email" varchar,
  "subject" varchar,
  "message" text,
  "view_status" integer not null default '0',
  "reply_status" integer not null default '0',
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_product_purchases"(
  "id" integer primary key autoincrement not null,
  "purchase_date" date not null,
  "expaire_date" date not null,
  "price" float,
  "paid_amount" float,
  "due_amount" float,
  "package" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  "staff_id" integer,
  "school_id" integer default '1',
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("staff_id") references "sm_staffs"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_about_pages"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "title" varchar,
  "description" text,
  "main_title" varchar,
  "main_description" text,
  "image" varchar,
  "main_image" varchar,
  "button_text" varchar,
  "button_url" varchar,
  "active_status" integer not null default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_courses"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "image" text not null,
  "category_id" integer,
  "overview" text,
  "outline" text,
  "prerequisites" text,
  "resources" text,
  "stats" text,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_dashboard_settings"(
  "id" integer primary key autoincrement not null,
  "dashboard_sec_id" integer not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "role_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("role_id") references "roles"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_background_settings"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "type" varchar,
  "image" varchar,
  "color" varchar,
  "is_default" integer not null default '0',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_custom_links"(
  "id" integer primary key autoincrement not null,
  "title1" varchar,
  "title2" varchar,
  "title3" varchar,
  "title4" varchar,
  "link_label1" varchar,
  "link_href1" varchar,
  "link_label2" varchar,
  "link_href2" varchar,
  "link_label3" varchar,
  "link_href3" varchar,
  "link_label4" varchar,
  "link_href4" varchar,
  "link_label5" varchar,
  "link_href5" varchar,
  "link_label6" varchar,
  "link_href6" varchar,
  "link_label7" varchar,
  "link_href7" varchar,
  "link_label8" varchar,
  "link_href8" varchar,
  "link_label9" varchar,
  "link_href9" varchar,
  "link_label10" varchar,
  "link_href10" varchar,
  "link_label11" varchar,
  "link_href11" varchar,
  "link_label12" varchar,
  "link_href12" varchar,
  "link_label13" varchar,
  "link_href13" varchar,
  "link_label14" varchar,
  "link_href14" varchar,
  "link_label15" varchar,
  "link_href15" varchar,
  "link_label16" varchar,
  "link_href16" varchar,
  "facebook_url" varchar,
  "twitter_url" varchar,
  "dribble_url" varchar,
  "linkedin_url" varchar,
  "behance_url" varchar,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_frontend_persmissions"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "parent_id" integer not null default '0',
  "is_published" integer not null default '0',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_home_page_settings"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "long_title" varchar,
  "short_description" text,
  "link_label" varchar,
  "link_url" varchar,
  "image" varchar,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_system_versions"(
  "id" integer primary key autoincrement not null,
  "version_name" varchar not null,
  "title" varchar not null,
  "features" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "continets"(
  "id" integer primary key autoincrement not null,
  "code" varchar,
  "name" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_styles"(
  "id" integer primary key autoincrement not null,
  "style_name" varchar,
  "path_main_style" varchar,
  "path_infix_style" varchar,
  "primary_color" varchar,
  "primary_color2" varchar,
  "title_color" varchar,
  "text_color" varchar,
  "white" varchar,
  "black" varchar,
  "sidebar_bg" varchar,
  "barchart1" varchar,
  "barchart2" varchar,
  "barcharttextcolor" varchar,
  "barcharttextfamily" varchar,
  "areachartlinecolor1" varchar,
  "areachartlinecolor2" varchar,
  "dashboardbackground" varchar,
  "active_status" integer not null default '1',
  "is_active" integer not null default '0',
  "is_default" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_module_permissions"(
  "id" integer primary key autoincrement not null,
  "dashboard_id" integer,
  "name" varchar,
  "active_status" integer not null default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_module_permission_assigns"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "module_id" integer,
  "role_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("module_id") references "sm_module_permissions"("id") on delete cascade,
  foreign key("role_id") references "roles"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_time_zones"(
  "id" integer primary key autoincrement not null,
  "code" varchar,
  "time_zone" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_student_attendance_imports"(
  "id" integer primary key autoincrement not null,
  "attendance_date" date,
  "in_time" varchar,
  "out_time" varchar,
  "attendance_type" varchar,
  "notes" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "student_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_staff_attendance_imports"(
  "id" integer primary key autoincrement not null,
  "attendence_date" date,
  "in_time" varchar,
  "out_time" varchar,
  "attendance_type" varchar,
  "notes" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "staff_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("staff_id") references "sm_staffs"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_bio_settings"(
  "id" integer primary key autoincrement not null,
  "school_id" integer,
  "created_by" integer,
  "updated_by" integer,
  "start_time" varchar default '00:00',
  "exit_time" varchar default '16:00',
  "consider_start_time" varchar default '00:00',
  "created_at" datetime,
  "updated_at" datetime,
  "class_id" integer,
  "section_id" integer
);
CREATE TABLE IF NOT EXISTS "infix_module_infos"(
  "id" integer primary key autoincrement not null,
  "module_id" integer,
  "module_name" varchar,
  "parent_id" integer default '0',
  "name" varchar,
  "is_saas" integer not null default '0',
  "route" varchar,
  "parent_route" varchar,
  "lang_name" varchar,
  "icon_class" varchar,
  "active_status" integer not null default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer,
  "type" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_news_pages"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "title" varchar,
  "description" text,
  "main_title" varchar,
  "main_description" text,
  "image" varchar,
  "main_image" varchar,
  "button_text" varchar,
  "button_url" varchar,
  "active_status" integer not null default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_course_pages"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "title" varchar,
  "description" text,
  "main_title" varchar,
  "main_description" text,
  "image" varchar,
  "main_image" varchar,
  "button_text" varchar,
  "button_url" varchar,
  "active_status" integer not null default '1',
  "is_parent" tinyint(1) not null default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "custom_result_settings"(
  "id" integer primary key autoincrement not null,
  "exam_type_id" integer,
  "exam_percentage" float,
  "merit_list_setting" varchar,
  "print_status" varchar,
  "profile_image" varchar,
  "header_background" varchar,
  "body_background" varchar,
  "academic_year" integer,
  "school_id" integer default '1',
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "vertical_boarder" varchar,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_custom_temporary_results"(
  "id" integer primary key autoincrement not null,
  "student_id" integer,
  "admission_no" varchar,
  "full_name" varchar,
  "term1" varchar,
  "gpa1" varchar,
  "term2" varchar,
  "gpa2" varchar,
  "term3" varchar,
  "gpa3" varchar,
  "final_result" varchar,
  "final_grade" varchar,
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete restrict,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "device_log"(
  "id" integer primary key autoincrement not null,
  "userid" varchar,
  "role_id" varchar,
  "class_id" varchar,
  "record_id" integer,
  "school_id" integer,
  "section_id" varchar,
  "profile_id" varchar,
  "checktime" varchar,
  "terminalid" varchar,
  "name" varchar,
  "area_id" varchar,
  "device_ip" varchar,
  "log_type" varchar,
  "cloud_upload" varchar,
  "active_status" tinyint(1) not null default '1',
  "is_attendance" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_add_ons"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sms_templates"(
  "id" integer primary key autoincrement not null,
  "type" varchar not null,
  "purpose" text,
  "subject" text not null,
  "body" text not null,
  "module" varchar,
  "variable" text,
  "status" integer not null default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_social_media_icons"(
  "id" integer primary key autoincrement not null,
  "url" varchar,
  "icon" varchar,
  "status" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_upload_homework_contents"(
  "id" integer primary key autoincrement not null,
  "student_id" integer default '1',
  "homework_id" integer default '1',
  "description" text,
  "file" text,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("homework_id") references "sm_homeworks"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "infix_permission_assigns"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "module_id" integer,
  "module_info" varchar,
  "role_id" integer,
  "saas_schools" text,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("role_id") references "infix_roles"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_module_student_parent_infos"(
  "id" integer primary key autoincrement not null,
  "module_id" integer,
  "parent_id" integer default '0',
  "name" varchar,
  "route" varchar,
  "lang_name" varchar,
  "icon_class" varchar,
  "active_status" integer not null default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "type" integer,
  "user_type" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "admin_section" varchar,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "billing__information"(
  "id" integer primary key autoincrement not null,
  "first_name" varchar not null,
  "last_name" varchar not null,
  "full_name" varchar not null,
  "company" varchar not null,
  "billing_email" varchar not null,
  "address" varchar not null,
  "country" varchar not null,
  "city" varchar not null,
  "state" varchar not null,
  "zip" varchar not null,
  "payment_status" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "additional_services"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "description" text not null,
  "image" varchar not null,
  "price" numeric not null,
  "active_status" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "sm_saas_packages"(
  "id" integer primary key autoincrement not null,
  "package_name" varchar not null,
  "monthly_price" double,
  "quarterly_price" double,
  "yearly_price" double,
  "lifetime_price" double,
  "active_status" integer not null default '1',
  "feature" text,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "subscriptions"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "name" varchar not null,
  "stripe_id" varchar not null,
  "stripe_plan" varchar not null,
  "quantity" integer not null,
  "trial_ends_at" datetime,
  "ends_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "categories"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "active_status" varchar not null default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "priorities"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "comments"(
  "id" integer primary key autoincrement not null,
  "file" varchar,
  "comment" text not null,
  "comment_id" integer default '1',
  "client_id" integer default '1',
  "ticket_id" integer default '1',
  "user_id" integer default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("client_id") references "users"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "infix_invoices"(
  "id" integer primary key autoincrement not null,
  "customer_id" integer,
  "invoice_number" varchar,
  "invoice_date" date,
  "invoice_due_date" date,
  "currency_id" integer,
  "project_id" integer,
  "payment_method_id" integer,
  "recurring_cycle" varchar check("recurring_cycle" in('M', 'Q', 'SA', 'A', 'OT')),
  "is_recurring_invoice" integer not null default '0',
  "payment_status" varchar check("payment_status" in('UP', 'P', 'PP', 'PR')) not null,
  "partial_paymemt" double,
  "invoice_for" varchar check("invoice_for" in('P', 'S', 'C')) not null,
  "discount_type" varchar check("discount_type" in('P', 'F')),
  "discount_amount" double,
  "tax_percentage" varchar,
  "purchase_order" varchar,
  "private_note" text,
  "public_note" text,
  "terms_note" text,
  "footer_note" text,
  "signature_person" varchar,
  "signature_company" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "infix_invoice_categories"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "link_ids" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "infix_invoice_settings"(
  "id" integer primary key autoincrement not null,
  "tax" float,
  "tax_type" varchar not null default 'AD',
  "prefix" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "infix_invoice_category_links"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "infix_invoice_products"(
  "id" integer primary key autoincrement not null,
  "invoice_id" integer not null,
  "product_id" integer,
  "description" text,
  "quantity" integer,
  "price" float,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("invoice_id") references "infix_invoices"("id") on delete cascade,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "verify_users"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "token" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "academic_id" integer,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "sm_administrator_notices"(
  "id" integer primary key autoincrement not null,
  "notice_title" varchar,
  "notice_message" text,
  "notice_date" date,
  "publish_on" date,
  "inform_to" varchar,
  "active_status" integer not null default '1',
  "is_published" integer default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "saas_school_module_permission_assigns"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "module_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("module_id") references "sm_module_permissions"("id") on delete cascade,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_registration_settings"(
  "id" integer primary key autoincrement not null,
  "position" integer not null default '1',
  "registration_permission" integer not null default '1',
  "registration_after_mail" integer not null default '1',
  "approve_after_mail" integer not null default '1',
  "recaptcha" integer not null default '1',
  "nocaptcha_sitekey" varchar,
  "nocaptcha_secret" varchar,
  "academic" integer not null default '1',
  "class" integer not null default '1',
  "first_name" integer not null default '1',
  "last_name" integer not null default '1',
  "gender" integer not null default '1',
  "date_of_birth" integer not null default '1',
  "age" integer not null default '1',
  "student_email" integer not null default '1',
  "student_mobile" integer not null default '1',
  "guardian_name" integer not null default '1',
  "guardian_realtion" integer not null default '1',
  "guardian_email" integer not null default '1',
  "guardian_mobile" integer not null default '1',
  "how_know" integer not null default '1',
  "notice_board" integer not null default '1',
  "notice_text" text,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "footer_note_status" integer,
  "footer_note_text" text,
  "start_date" datetime,
  "before_start_msg" text,
  "end_date" datetime,
  "after_end_msg" text,
  "url" varchar not null default 'registration',
  "always_enable" integer not null default '0',
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "zoom_meetings"(
  "id" integer primary key autoincrement not null,
  "meeting_id" varchar,
  "password" varchar,
  "start_time" datetime,
  "end_time" datetime,
  "topic" varchar,
  "description" varchar,
  "attached_file" varchar,
  "date_of_meeting" varchar,
  "time_of_meeting" varchar,
  "meeting_duration" varchar,
  "time_before_start" integer,
  "join_before_host" tinyint(1),
  "host_video" tinyint(1),
  "participant_video" tinyint(1),
  "mute_upon_entry" tinyint(1),
  "waiting_room" tinyint(1),
  "audio" varchar not null default 'both',
  "auto_recording" varchar not null default 'none',
  "approval_type" varchar not null default '0',
  "is_recurring" tinyint(1),
  "recurring_type" integer,
  "recurring_repect_day" integer,
  "weekly_days" varchar,
  "recurring_end_date" varchar,
  "status" tinyint(1) not null default '1',
  "local_video" text,
  "vedio_link" text,
  "created_by" integer,
  "updated_by" integer,
  "school_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "join_url" text,
  "is_custom" integer not null default '0',
  "custom_url" text
);
CREATE TABLE IF NOT EXISTS "zoom_meeting_users"(
  "id" integer primary key autoincrement not null,
  "meeting_id" integer,
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "infix_module_managers"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "email" varchar,
  "notes" varchar,
  "version" varchar,
  "update_url" varchar,
  "purchase_code" varchar,
  "checksum" varchar,
  "installed_domain" varchar,
  "is_default" tinyint(1) not null default '0',
  "addon_url" varchar,
  "activated_date" date,
  "lang_type" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "zoom_settings"(
  "id" integer primary key autoincrement not null,
  "package_id" integer not null default '1',
  "host_video" tinyint(1) not null default '0',
  "participant_video" tinyint(1) not null default '0',
  "join_before_host" tinyint(1) not null default '0',
  "audio" varchar not null default 'both',
  "auto_recording" varchar not null default 'none',
  "approval_type" integer not null default '0',
  "mute_upon_entry" tinyint(1) not null default '0',
  "waiting_room" tinyint(1) not null default '0',
  "api_use_for" integer not null default '0',
  "api_key" varchar,
  "secret_key" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "account_id" varchar,
  "school_id" varchar default '1'
);
CREATE TABLE IF NOT EXISTS "student_bulk_temporaries"(
  "id" integer primary key autoincrement not null,
  "admission_number" varchar,
  "roll_no" varchar,
  "first_name" varchar,
  "last_name" varchar,
  "date_of_birth" varchar,
  "religion" varchar,
  "gender" varchar,
  "caste" varchar,
  "mobile" varchar,
  "email" varchar,
  "admission_date" varchar,
  "blood_group" varchar,
  "height" varchar,
  "weight" varchar,
  "father_name" varchar,
  "father_phone" varchar,
  "father_occupation" varchar,
  "mother_name" varchar,
  "mother_phone" varchar,
  "mother_occupation" varchar,
  "guardian_name" varchar,
  "guardian_relation" varchar,
  "guardian_email" varchar,
  "guardian_phone" varchar,
  "guardian_occupation" varchar,
  "guardian_address" varchar,
  "current_address" varchar,
  "permanent_address" varchar,
  "bank_account_no" varchar,
  "bank_name" varchar,
  "national_identification_no" varchar,
  "local_identification_no" varchar,
  "previous_school_details" varchar,
  "note" text,
  "user_id" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "zoom_virtual_class"(
  "id" integer primary key autoincrement not null,
  "meeting_id" varchar,
  "password" varchar,
  "start_time" datetime,
  "end_time" datetime,
  "class_id" varchar,
  "section_id" varchar,
  "course_id" integer,
  "chapter_id" integer,
  "lesson_id" integer,
  "topic" varchar,
  "description" varchar,
  "attached_file" varchar,
  "date_of_meeting" varchar,
  "time_of_meeting" varchar,
  "meeting_duration" varchar,
  "time_before_start" integer,
  "join_before_host" tinyint(1),
  "host_video" tinyint(1),
  "participant_video" tinyint(1),
  "mute_upon_entry" tinyint(1),
  "waiting_room" tinyint(1),
  "audio" varchar not null default 'both',
  "auto_recording" varchar not null default 'none',
  "approval_type" varchar not null default '0',
  "is_recurring" tinyint(1),
  "recurring_type" integer,
  "recurring_repect_day" integer,
  "weekly_days" varchar,
  "recurring_end_date" varchar,
  "status" tinyint(1) not null default '1',
  "local_video" text,
  "vedio_link" text,
  "created_by" integer,
  "updated_by" integer,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "join_url" text,
  "shift_id" integer,
  "un_session_id" integer,
  "un_faculty_id" integer,
  "un_department_id" integer,
  "un_academic_id" integer,
  "un_semester_id" integer,
  "un_semester_label_id" integer,
  "un_section_id" integer,
  "is_custom" integer not null default '0',
  "custom_url" text
);
CREATE TABLE IF NOT EXISTS "zoom_virtual_class_teachers"(
  "id" integer primary key autoincrement not null,
  "meeting_id" integer,
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "student_attendance_bulks"(
  "id" integer primary key autoincrement not null,
  "attendance_date" varchar,
  "attendance_type" varchar,
  "note" varchar,
  "student_id" integer,
  "student_record_id" integer,
  "class_id" integer,
  "section_id" integer,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_package_plans"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "duration_days" integer,
  "price" double,
  "trial_days" integer,
  "active_status" integer not null default '1',
  "features" text,
  "student_quantity" integer,
  "staff_quantity" integer,
  "modules" text,
  "menus" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_package_plan_features"(
  "id" integer primary key autoincrement not null,
  "feature" varchar,
  "package_plan_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("package_plan_id") references "sm_package_plans"("id")
);
CREATE TABLE IF NOT EXISTS "sm_saas_payment_gateway_settings"(
  "id" integer primary key autoincrement not null,
  "gateway_name" varchar,
  "gateway_username" varchar,
  "gateway_password" varchar,
  "gateway_signature" varchar,
  "gateway_client_id" varchar,
  "gateway_mode" varchar,
  "gateway_secret_key" varchar,
  "gateway_secret_word" varchar,
  "gateway_publisher_key" varchar,
  "gateway_private_key" varchar,
  "bank_details" text,
  "cheque_details" text,
  "active_status" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1'
);
CREATE TABLE IF NOT EXISTS "sm_saas_payment_methods"(
  "id" integer primary key autoincrement not null,
  "method" varchar not null,
  "type" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "gateway_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  foreign key("gateway_id") references "sm_saas_payment_gateway_settings"("id") on delete restrict
);
CREATE TABLE IF NOT EXISTS "sm_saas_subscription_settings"(
  "id" integer primary key autoincrement not null,
  "amount" integer,
  "is_auto_approve" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_subscription_payments"(
  "id" integer primary key autoincrement not null,
  "package_id" integer default '1',
  "payment_type" varchar check("payment_type" in('paid', 'trial')),
  "approve_status" varchar check("approve_status" in('approved', 'pending', 'cancelled')),
  "bank_name" varchar,
  "account_holder" varchar,
  "payment_date" date,
  "payment_method" varchar,
  "file" varchar,
  "amount" double,
  "school_id" integer default '1',
  "start_date" date,
  "end_date" date,
  "buy_type" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("package_id") references "sm_package_plans"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "check_classes"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_role_permissions"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "module_link_id" integer,
  "role_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("module_link_id") references "sm_module_links"("id") on delete cascade,
  foreign key("role_id") references "roles"("id") on delete cascade on update cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_lesson_details"(
  "id" integer primary key autoincrement not null,
  "lesson_id" integer not null,
  "lesson_title" varchar,
  "user_id" integer,
  "active_status" integer not null default '1',
  "class_id" integer,
  "section_id" integer,
  "subject_id" integer,
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("section_id") references "sm_sections"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_lesson_topic_details"(
  "id" integer primary key autoincrement not null,
  "lesson_id" integer,
  "topic_title" varchar not null,
  "completed_status" varchar,
  "competed_date" date,
  "active_status" integer not null default '1',
  "topic_id" integer default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("topic_id") references "sm_lesson_topics"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "bbb_settings"(
  "id" integer primary key autoincrement not null,
  "password_length" integer not null default '6',
  "welcome_message" text,
  "dial_number" text,
  "max_participants" integer not null default '0',
  "logout_url" text,
  "record" tinyint(1) not null default '0',
  "duration" integer not null default '0',
  "is_breakout" tinyint(1) not null default '0',
  "moderator_only_message" text,
  "auto_start_recording" tinyint(1) not null default '0',
  "allow_start_stop_recording" tinyint(1) not null default '1',
  "webcams_only_ror_moderator" tinyint(1) not null default '0',
  "copyright" text not null default '',
  "mute_on_start" tinyint(1) not null default '0',
  "webcams_only_for_moderator" tinyint(1) not null default '0',
  "lock_settings_disable_cam" tinyint(1) not null default '0',
  "lock_settings_disable_mic" tinyint(1) not null default '0',
  "lock_settings_lock_on_join" tinyint(1) not null default '0',
  "lock_settings_lock_on_join_configurable" tinyint(1) not null default '0',
  "join_via_html5" tinyint(1) not null default '1',
  "lock_settings_disable_private_chat" tinyint(1) not null default '0',
  "lock_settings_disable_public_chat" tinyint(1) not null default '0',
  "lock_settings_disable_note" tinyint(1) not null default '0',
  "lock_settings_locked_layout" tinyint(1) not null default '0',
  "lock_settings_lock_on_oin" tinyint(1) not null default '0',
  "lock_settings_sock_on_join_configurable" tinyint(1) not null default '0',
  "guest_policy" varchar check("guest_policy" in('ALWAYS_ACCEPT', 'ALWAYS_DENY', 'ASK_MODERATOR')) not null default 'ALWAYS_ACCEPT',
  "redirect" tinyint(1) not null default '1',
  "join_via_html_5" tinyint(1) not null default '1',
  "state" varchar check("state" in('any', 'published', 'unpublished')) not null default 'any',
  "security_salt" text,
  "server_base_url" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "bbb_meetings"(
  "id" integer primary key autoincrement not null,
  "created_by" integer default '1',
  "instructor_id" integer default '1',
  "start_time" datetime,
  "end_time" datetime,
  "class_id" integer,
  "meeting_id" text,
  "topic" text,
  "description" text,
  "attendee_password" text,
  "moderator_password" text,
  "date" text,
  "time" text,
  "datetime" text,
  "time_start_before" text,
  "welcome_message" text,
  "dial_number" text,
  "max_participants" integer default '0',
  "logout_url" text,
  "record" tinyint(1) default '0',
  "duration" integer default '0',
  "is_breakout" tinyint(1) default '0',
  "moderator_only_message" text,
  "auto_start_recording" tinyint(1) default '0',
  "allow_start_stop_recording" tinyint(1) default '1',
  "webcams_only_ror_moderator" tinyint(1) default '0',
  "logo" text default '',
  "copyright" text default '',
  "mute_on_start" tinyint(1) default '0',
  "webcams_only_for_moderator" tinyint(1) default '0',
  "lock_settings_disable_cam" tinyint(1) default '0',
  "lock_settings_disable_mic" tinyint(1) default '0',
  "lock_settings_lock_on_join" tinyint(1) default '0',
  "lock_settings_lock_on_join_configurable" tinyint(1) default '0',
  "join_via_html5" tinyint(1) default '1',
  "lock_settings_disable_private_chat" tinyint(1) default '0',
  "lock_settings_disable_public_chat" tinyint(1) default '0',
  "lock_settings_disable_note" tinyint(1) default '0',
  "lock_settings_locked_layout" tinyint(1) default '0',
  "lock_settings_lock_on_oin" tinyint(1) default '0',
  "lock_settings_sock_on_join_configurable" tinyint(1) default '0',
  "guest_policy" varchar check("guest_policy" in('ALWAYS_ACCEPT', 'ALWAYS_DENY', 'ASK_MODERATOR')) default 'ALWAYS_ACCEPT',
  "redirect" tinyint(1) default '1',
  "join_via_html_5" tinyint(1) default '1',
  "state" varchar check("state" in('any', 'published', 'unpublished')) default 'any',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "bbb_meeting_users"(
  "id" integer primary key autoincrement not null,
  "meeting_id" integer not null default '1',
  "user_id" integer not null default '1',
  "moderator" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "online_exam_student_answer_markings"(
  "id" integer primary key autoincrement not null,
  "online_exam_id" integer,
  "student_id" integer,
  "question_id" integer,
  "user_answer" varchar,
  "answer_status" varchar,
  "obtain_marks" integer,
  "school_id" integer,
  "marked_by" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "bbb_virtual_class_teachers"(
  "id" integer primary key autoincrement not null,
  "meeting_id" integer,
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "bbb_virtual_classes"(
  "id" integer primary key autoincrement not null,
  "created_by" integer default '1',
  "meeting_id" text,
  "start_time" datetime,
  "end_time" datetime,
  "class_id" integer,
  "section_id" varchar,
  "subject_id" varchar,
  "topic" text,
  "description" text,
  "attendee_password" text,
  "moderator_password" text,
  "date" text,
  "time" text,
  "datetime" text,
  "time_start_before" text,
  "welcome_message" text,
  "dial_number" text,
  "max_participants" integer default '0',
  "logout_url" text,
  "record" tinyint(1) default '0',
  "duration" integer default '0',
  "is_breakout" tinyint(1) default '0',
  "moderator_only_message" text,
  "auto_start_recording" tinyint(1) default '0',
  "allow_start_stop_recording" tinyint(1) default '1',
  "webcams_only_ror_moderator" tinyint(1) default '0',
  "logo" text default '',
  "copyright" text default '',
  "mute_on_start" tinyint(1) default '0',
  "webcams_only_for_moderator" tinyint(1) default '0',
  "lock_settings_disable_cam" tinyint(1) default '0',
  "lock_settings_disable_mic" tinyint(1) default '0',
  "lock_settings_lock_on_join" tinyint(1) default '0',
  "lock_settings_lock_on_join_configurable" tinyint(1) default '0',
  "join_via_html5" tinyint(1) default '1',
  "lock_settings_disable_private_chat" tinyint(1) default '0',
  "lock_settings_disable_public_chat" tinyint(1) default '0',
  "lock_settings_disable_note" tinyint(1) default '0',
  "lock_settings_locked_layout" tinyint(1) default '0',
  "lock_settings_lock_on_oin" tinyint(1) default '0',
  "lock_settings_sock_on_join_configurable" tinyint(1) default '0',
  "guest_policy" varchar check("guest_policy" in('ALWAYS_ACCEPT', 'ALWAYS_DENY', 'ASK_MODERATOR')) default 'ALWAYS_ACCEPT',
  "redirect" tinyint(1) default '1',
  "join_via_html_5" tinyint(1) default '1',
  "state" varchar check("state" in('any', 'published', 'unpublished')) default 'any',
  "is_recurring" tinyint(1),
  "recurring_type" integer,
  "recurring_repect_day" integer,
  "recurring_end_date" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "shift_id" integer,
  "un_session_id" integer,
  "un_faculty_id" integer,
  "un_department_id" integer,
  "un_academic_id" integer,
  "un_semester_id" integer,
  "un_semester_label_id" integer,
  "un_section_id" integer
);
CREATE TABLE IF NOT EXISTS "sm_exam_settings"(
  "id" integer primary key autoincrement not null,
  "exam_type" integer,
  "title" varchar,
  "publish_date" date,
  "start_date" date,
  "end_date" date,
  "file" varchar,
  "active_status" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "absent_notification_time_setups"(
  "id" integer primary key autoincrement not null,
  "time_from" varchar,
  "time_to" varchar,
  "active_status" integer not null default '1',
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "chat_invitations"(
  "id" integer primary key autoincrement not null,
  "from" integer not null,
  "to" integer not null,
  "status" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "chat_conversations"(
  "id" integer primary key autoincrement not null,
  "from_id" integer,
  "to_id" integer,
  "message" text,
  "status" integer not null default '0',
  "message_type" integer not null default '0',
  "file_name" text,
  "original_file_name" text,
  "initial" tinyint(1) not null default '0',
  "reply" integer,
  "forward" integer,
  "deleted_by_to" tinyint(1) not null default '0',
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "chat_block_users"(
  "id" integer primary key autoincrement not null,
  "block_by" integer not null,
  "block_to" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "notifications"(
  "id" varchar not null,
  "type" varchar not null,
  "notifiable_type" varchar not null,
  "notifiable_id" integer not null,
  "data" text not null,
  "read_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  primary key("id")
);
CREATE INDEX "notifications_notifiable_type_notifiable_id_index" on "notifications"(
  "notifiable_type",
  "notifiable_id"
);
CREATE TABLE IF NOT EXISTS "chat_groups"(
  "id" varchar not null,
  "name" varchar not null,
  "description" varchar,
  "photo_url" varchar,
  "privacy" integer,
  "read_only" tinyint(1) not null default '0',
  "group_type" integer not null default '1',
  "created_by" integer not null,
  "class_id" integer,
  "section_id" integer,
  "subject_id" integer,
  "teacher_id" integer,
  "school_id" integer,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "shift_id" integer,
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("section_id") references "sm_sections"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("teacher_id") references "sm_staffs"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "chat_group_users"(
  "id" integer primary key autoincrement not null,
  "group_id" varchar not null,
  "user_id" integer not null,
  "role" integer not null default '1',
  "added_by" integer not null,
  "removed_by" integer,
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "chat_group_message_recipients"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "conversation_id" integer not null,
  "group_id" varchar not null,
  "read_at" datetime,
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_pages"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "sub_title" varchar,
  "slug" varchar,
  "header_image" text,
  "details" text,
  "active_status" integer not null default '1',
  "is_dynamic" integer not null default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE UNIQUE INDEX "sm_pages_sub_title_unique" on "sm_pages"("sub_title");
CREATE TABLE IF NOT EXISTS "sm_header_menu_managers"(
  "id" integer primary key autoincrement not null,
  "type" varchar not null,
  "element_id" integer,
  "title" varchar,
  "link" varchar,
  "parent_id" integer,
  "position" integer not null default '0',
  "show" tinyint(1) not null default '0',
  "is_newtab" tinyint(1) not null default '0',
  "theme" varchar not null default 'default',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "chat_group_message_removes"(
  "id" integer primary key autoincrement not null,
  "group_message_recipient_id" integer not null,
  "user_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_course_categories"(
  "id" integer primary key autoincrement not null,
  "category_name" varchar,
  "category_image" text,
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "jitsi_virtual_class_teachers"(
  "id" integer primary key autoincrement not null,
  "meeting_id" integer,
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "jitsi_settings"(
  "id" integer primary key autoincrement not null,
  "jitsi_server" varchar not null default 'https://meet.jit.si/',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "jitsi_meetings"(
  "id" integer primary key autoincrement not null,
  "created_by" integer default '1',
  "instructor_id" integer default '1',
  "member_type" integer,
  "meeting_id" text,
  "topic" text,
  "description" text,
  "file" text default '',
  "start_time" datetime,
  "end_time" datetime,
  "time_start_before" text,
  "date" text,
  "time" text,
  "datetime" text,
  "duration" integer default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "chat_invitation_types"(
  "id" integer primary key autoincrement not null,
  "invitation_id" integer not null,
  "type" varchar check("type" in('one-to-one', 'group', 'class-teacher')) not null default 'one-to-one',
  "section_id" integer,
  "class_teacher_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "jitsi_meeting_users"(
  "id" integer primary key autoincrement not null,
  "meeting_id" integer not null default '1',
  "user_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "infix_question_groups"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "subject_id" integer,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_question_banks"(
  "id" integer primary key autoincrement not null,
  "type" varchar not null,
  "answer_type" varchar,
  "question" text,
  "question_image" text,
  "marks" integer,
  "time" integer,
  "trueFalse" varchar,
  "suitable_words" text,
  "number_of_option" varchar,
  "active_status" integer not null default '1',
  "per_match_mark" integer not null default '0',
  "privacy" integer not null default '1',
  "number_of_qus" integer not null default '0',
  "number_of_ans" integer not null default '0',
  "connection" text,
  "data" text,
  "created_at" datetime,
  "updated_at" datetime,
  "q_group_id" integer,
  "class_id" integer,
  "chapter_id" integer,
  "lesson_id" integer,
  "section_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "subject_id" integer,
  foreign key("q_group_id") references "infix_question_groups"("id") on delete cascade,
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("section_id") references "sm_sections"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_online_exams"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "start_date" date,
  "end_date" date,
  "start_time" varchar,
  "end_time" varchar,
  "end_date_time" varchar,
  "percentage" integer,
  "exam_type" integer,
  "selected_sections" varchar,
  "unique_id" varchar,
  "instruction" text,
  "status" integer,
  "is_taken" integer default '0',
  "is_closed" integer default '0',
  "is_waiting" integer default '0',
  "is_running" integer default '0',
  "auto_mark" integer default '0',
  "negative_marking" integer default '0',
  "active_status" integer not null default '1',
  "duration_type" varchar default 'exam',
  "duration" integer not null default '0',
  "default_question_time" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "class_id" integer,
  "section_id" integer,
  "subject_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "course_id" integer,
  "chapter_id" integer,
  "lesson_id" integer,
  "question_groups" text,
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("section_id") references "sm_sections"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_question_bank_mu_options"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "image_title" varchar,
  "status" integer,
  "active_status" integer not null default '1',
  "type" integer not null default '1',
  "option_index" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "question_bank_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("question_bank_id") references "infix_question_banks"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_student_take_online_exams"(
  "id" integer primary key autoincrement not null,
  "status" integer not null default '0',
  "student_done" integer not null default '0',
  "total_marks" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "student_id" integer,
  "online_exam_id" integer,
  "right_answer" integer,
  "wrong_answer" integer,
  "deduct_marks" numeric,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "student_record_id" integer,
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("online_exam_id") references "infix_online_exams"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade,
  foreign key("student_record_id") references "student_records"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_student_take_online_exam_questions"(
  "id" integer primary key autoincrement not null,
  "trueFalse" varchar,
  "suitable_words" text,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "take_online_exam_id" integer,
  "question_bank_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("take_online_exam_id") references "infix_online_exams"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_online_exam_student_answer_markings"(
  "id" integer primary key autoincrement not null,
  "online_exam_id" integer,
  "student_id" integer,
  "question_id" integer,
  "option_id" integer,
  "user_answer" text,
  "answer_status" varchar,
  "obtain_marks" numeric,
  "school_id" integer,
  "marked_by" integer not null default '0',
  "student_record_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("online_exam_id") references "infix_online_exams"("id") on delete cascade,
  foreign key("student_record_id") references "student_records"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_student_take_onln_ex_ques_options"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "status" integer,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "take_online_exam_question_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_online_exam_question_assigns"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "online_exam_id" integer,
  "question_bank_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("online_exam_id") references "infix_online_exams"("id") on delete cascade,
  foreign key("question_bank_id") references "infix_question_banks"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_online_exam_settings"(
  "id" integer primary key autoincrement not null,
  "auto_marking_default" integer default '1',
  "negative_marking" integer default '1',
  "deduct_marks_per_wrong" double default '0.5',
  "submit_from_last_page" integer default '1',
  "any_question_access" integer default '1',
  "random_question" integer default '1',
  "single_page" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "infix_online_exam_marks"(
  "id" integer primary key autoincrement not null,
  "marks" integer,
  "abs" integer not null default '0',
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "student_id" integer,
  "subject_id" integer,
  "exam_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_question_bank_bulk_temporaries"(
  "id" integer primary key autoincrement not null,
  "type" varchar not null,
  "answer_type" varchar,
  "question" text,
  "question_image" text,
  "marks" integer,
  "trueFalse" varchar,
  "suitable_words" text,
  "number_of_option" varchar,
  "time" integer,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "options" text,
  "q_group_id" integer,
  "class_id" integer,
  "section_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1'
);
CREATE TABLE IF NOT EXISTS "sm_custom_fields"(
  "id" integer primary key autoincrement not null,
  "form_name" varchar not null,
  "label" varchar,
  "type" varchar,
  "min_max_length" varchar,
  "min_max_value" varchar,
  "name_value" varchar,
  "width" varchar,
  "required" integer,
  "school_id" integer default '1',
  "academic_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "is_showing" integer default '0'
);
CREATE TABLE IF NOT EXISTS "infix_written_exams"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "question_type" integer default '1',
  "question" text,
  "scan_upload" integer default '1',
  "audio_file" varchar,
  "audio_status" integer default '1',
  "calculator" integer default '1',
  "question_written" varchar,
  "written_resource" varchar,
  "external_resource" varchar,
  "start_date" date,
  "end_date" date,
  "start_time" time,
  "end_time" time,
  "total_exam_marks" double,
  "percentage" integer,
  "instruction" text,
  "status" integer,
  "is_taken" integer default '0',
  "is_closed" integer default '0',
  "is_waiting" integer default '0',
  "is_running" integer default '0',
  "auto_mark" integer default '0',
  "active_status" integer not null default '1',
  "class_id" integer,
  "section_id" integer,
  "subject_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("section_id") references "sm_sections"("id") on delete cascade,
  foreign key("subject_id") references "sm_subjects"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infix_student_take_written_exams"(
  "id" integer primary key autoincrement not null,
  "status" integer not null default '0',
  "student_done" integer not null default '0',
  "total_marks" varchar,
  "answer" text,
  "answer_upload" varchar,
  "active_status" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "student_id" integer,
  "written_exam_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "invoice_settings"(
  "id" integer primary key autoincrement not null,
  "per_th" integer not null default '2',
  "prefix" varchar,
  "student_name" integer not null default '1',
  "student_section" integer not null default '1',
  "student_class" integer not null default '1',
  "student_roll" integer not null default '1',
  "student_group" integer not null default '1',
  "student_admission_no" integer not null default '1',
  "footer_1" varchar default 'Parent/Student',
  "footer_2" varchar not null default 'Casier',
  "footer_3" varchar not null default 'Officer',
  "signature_p" integer not null default '1',
  "signature_c" integer not null default '1',
  "signature_o" integer not null default '1',
  "c_signature_p" integer not null default '1',
  "c_signature_c" integer not null default '0',
  "c_signature_o" integer not null default '1',
  "copy_s" varchar default 'Parent/Student',
  "copy_o" varchar not null default 'Office',
  "copy_c" varchar not null default 'Casier',
  "created_at" datetime,
  "updated_at" datetime,
  "copy_write_msg" text,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fm_fees_invoice_settings"(
  "id" integer primary key autoincrement not null,
  "invoice_positions" text,
  "uniq_id_start" varchar,
  "prefix" varchar,
  "class_limit" integer,
  "section_limit" integer,
  "admission_limit" integer,
  "weaver" varchar,
  "school_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "student_academic_histories"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "description" text,
  "active_status" tinyint(1) not null default '1',
  "occurance_date" date not null,
  "student_id" integer,
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "fm_fees_invoice_chields"(
  "id" integer primary key autoincrement not null,
  "fees_invoice_id" integer,
  "fees_type" integer,
  "amount" float,
  "weaver" float,
  "fine" float,
  "sub_total" float,
  "paid_amount" float,
  "service_charge" float,
  "due_amount" float,
  "note" varchar,
  "school_id" integer,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("fees_invoice_id") references "fm_fees_invoices"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fm_fees_weavers"(
  "id" integer primary key autoincrement not null,
  "fees_invoice_id" integer,
  "fees_type" integer,
  "student_id" integer,
  "weaver" float,
  "note" varchar,
  "school_id" integer,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("fees_invoice_id") references "fm_fees_invoices"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "course_categories"(
  "id" integer primary key autoincrement not null,
  "category_name" varchar not null,
  "parent_id" integer,
  "position_order" integer not null default '0',
  "active_status" integer not null default '1',
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer not null default '1',
  "title" varchar,
  "description" text,
  "url" varchar,
  "show_home" tinyint(1) not null default '0',
  "image" varchar,
  "thumbnail" varchar,
  "main_id" integer
);
CREATE TABLE IF NOT EXISTS "course_lessons"(
  "id" integer primary key autoincrement not null,
  "lesson_name" varchar not null,
  "lesson_no" integer not null,
  "overview" text,
  "video_url" varchar,
  "video_file" varchar,
  "host" integer,
  "duration" varchar,
  "course_id" integer,
  "parent_course_id" integer,
  "chapter_id" integer not null,
  "position_order" integer not null default '0',
  "user_id" integer not null,
  "active_status" integer not null default '1',
  "publish" integer not null default '0',
  "is_lock" integer not null default '1',
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "unlock_date" date,
  "unlock_days" integer,
  "is_quiz" integer default '0',
  "quiz_id" integer
);
CREATE TABLE IF NOT EXISTS "course_chapters"(
  "id" integer primary key autoincrement not null,
  "chapter_name" varchar not null,
  "overview" text,
  "course_id" integer,
  "parent_course_id" integer,
  "chapter_no" integer not null default '1',
  "active_status" integer not null default '1',
  "publish" integer not null default '0',
  "is_lock" integer not null default '1',
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "unlock_date" date,
  "unlock_days" integer
);
CREATE TABLE IF NOT EXISTS "course_quizzes"(
  "id" integer primary key autoincrement not null,
  "quiz_id" varchar not null,
  "course_id" integer,
  "parent_course_id" integer,
  "chapter_id" integer,
  "lesson_id" integer,
  "active_status" integer not null default '1',
  "publish" integer not null default '0',
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "assigned_by" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "wallet_transactions"(
  "id" integer primary key autoincrement not null,
  "amount" float,
  "payment_method" varchar,
  "user_id" integer,
  "bank_id" integer,
  "note" varchar,
  "type" varchar,
  "file" text,
  "reject_note" text,
  "expense" float,
  "status" varchar not null default 'pending',
  "created_by" integer,
  "academic_id" integer not null default '1',
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "course_teachers"(
  "id" integer primary key autoincrement not null,
  "course_id" integer not null,
  "staff_id" integer not null,
  "user_id" integer not null,
  "permission" text,
  "created_by" integer,
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "transcations"(
  "id" integer not null,
  "title" text,
  "type" varchar not null default 'debit',
  "payment_method" varchar,
  "reference" varchar,
  "description" text,
  "morphable_id" integer,
  "morphable_type" varchar,
  "amount" integer not null default '0',
  "transaction_date" date,
  "user_id" integer,
  "school_id" integer not null default '1',
  "academic_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "course_reviews"(
  "id" integer primary key autoincrement not null,
  "course_id" integer,
  "parent_course_id" integer,
  "user_id" integer not null,
  "review_comment" text,
  "star" float not null default '5',
  "active_status" integer not null default '1',
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "course_questions"(
  "id" integer primary key autoincrement not null,
  "course_id" integer,
  "parent_course_id" integer,
  "user_id" integer not null,
  "type" varchar not null default 'Q',
  "active_status" integer not null default '1',
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "course_settings"(
  "id" integer primary key autoincrement not null,
  "show_rating" integer default '1',
  "show_cart" tinyint(1) default '1',
  "show_enrolled_or_level_section" tinyint(1) default '1',
  "show_cql_left_sidebar" tinyint(1) default '1',
  "enrolled_or_level" integer default '1',
  "size_of_grid" integer default '4',
  "show_review_option" integer default '1',
  "show_qa_option" integer default '1',
  "approve_system" integer default '0',
  "show_instructor_review" integer default '1',
  "show_instructor_enrolled" integer default '1',
  "show_instructor_courses" integer default '1',
  "student_can_complete" integer default '0',
  "seekbar_status" integer default '0',
  "assign_fees" integer default '0',
  "teacher_commission" float not null default '0',
  "admin_commission" float not null default '100',
  "pay_latter_message" varchar,
  "pay_latter_due_day" integer,
  "host_id" text,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "show_drip" integer default '0',
  "course_approval" integer default '0',
  "youtube_default_player" integer default '0',
  "send_mail_before_expire" integer,
  "lms_checkout" tinyint(1) not null default '1'
);
CREATE TABLE IF NOT EXISTS "sm_student_fields"(
  "id" integer primary key autoincrement not null,
  "field_name" varchar,
  "label_name" varchar,
  "active_status" integer not null default '1',
  "is_required" integer not null default '0',
  "position" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "admin_section" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_student_registrations"(
  "id" integer primary key autoincrement not null,
  "first_name" varchar,
  "last_name" varchar,
  "class_id" integer,
  "section_id" integer,
  "date_of_birth" date,
  "age" varchar,
  "academic_year" integer,
  "gender_id" integer,
  "student_email" varchar,
  "student_mobile" varchar,
  "guardian_name" varchar,
  "guardian_mobile" varchar,
  "guardian_email" varchar,
  "guardian_relation" varchar,
  "how_do_know_us" text,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "un_session_id" integer,
  "un_faculty_id" integer,
  "un_department_id" integer,
  "un_academic_id" integer,
  "un_semester_id" integer,
  "un_semester_label_id" integer,
  "un_section_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "shift_id" integer,
  "caste" varchar,
  "id_number" varchar,
  "student_photo" varchar,
  "fathers_name" varchar,
  "fathers_mobile" varchar,
  "fathers_occupation" varchar,
  "fathers_photo" varchar,
  "mothers_name" varchar,
  "mothers_mobile" varchar,
  "mothers_occupation" varchar,
  "mothers_photo" varchar,
  "relation" varchar,
  "guardians_occupation" varchar,
  "guardians_photo" varchar,
  "guardians_address" varchar,
  "height" varchar,
  "weight" varchar,
  "current_address" text,
  "permanent_address" text,
  "driver_id" varchar,
  "national_id_no" varchar,
  "local_id_no" varchar,
  "bank_account_no" varchar,
  "bank_name" varchar,
  "previous_school_details" varchar,
  "aditional_notes" varchar,
  "ifsc_code" varchar,
  "document_title_1" varchar,
  "document_file_1" varchar,
  "document_title_2" varchar,
  "document_file_2" varchar,
  "document_title_3" varchar,
  "document_file_3" varchar,
  "document_title_4" varchar,
  "document_file_4" varchar,
  "bloodgroup_id" integer,
  "religion_id" integer,
  "route_list_id" integer,
  "dormitory_id" integer,
  "vechile_id" integer,
  "room_id" integer,
  "student_category_id" integer,
  "student_group_id" integer,
  "custom_field" text,
  "custom_field_form_name" varchar,
  foreign key("bloodgroup_id") references "sm_base_setups"("id") on delete cascade,
  foreign key("religion_id") references "sm_base_setups"("id") on delete cascade,
  foreign key("route_list_id") references "sm_routes"("id") on delete cascade,
  foreign key("dormitory_id") references "sm_dormitory_lists"("id") on delete cascade,
  foreign key("vechile_id") references "sm_vehicles"("id") on delete cascade,
  foreign key("room_id") references "sm_room_lists"("id") on delete cascade,
  foreign key("student_category_id") references "sm_student_categories"("id") on delete cascade,
  foreign key("student_group_id") references "sm_student_groups"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "course_purchase_logs"(
  "id" integer primary key autoincrement not null,
  "course_id" integer,
  "parent_course_id" integer,
  "student_id" integer not null,
  "instructor_id" integer not null,
  "parent_id" integer,
  "academic_id" integer not null,
  "school_id" integer not null,
  "assigned_by" integer,
  "active_status" varchar not null default 'pending',
  "amount" float not null,
  "payment_method" varchar not null,
  "note" varchar,
  "file" text,
  "bank_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "is_free" integer not null default '0'
);
CREATE TABLE IF NOT EXISTS "raudhahpay_collections"(
  "id" integer primary key autoincrement not null,
  "collection_name" varchar not null,
  "collection_id" varchar not null,
  "school_id" integer default '1',
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "raudhahpay_bills"(
  "id" integer primary key autoincrement not null,
  "fees_payment_id" integer not null,
  "bill_no" varchar not null,
  "account_id" varchar not null,
  "bill_id" varchar not null,
  "student_id" varchar not null,
  "fees_type_id" varchar not null,
  "amount" float not null,
  "payment_url" varchar not null,
  "bill_url" varchar not null,
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "saas_settings"(
  "id" integer primary key autoincrement not null,
  "lang_name" varchar not null,
  "active_status" integer not null default '1',
  "saas_status" integer not null default '1',
  "infix_module_id" integer,
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "route" varchar
);
CREATE TABLE IF NOT EXISTS "course_comments"(
  "id" integer primary key autoincrement not null,
  "course_id" integer,
  "parent_course_id" integer,
  "instructor_id" integer not null,
  "user_id" integer not null,
  "comment" text not null,
  "type" varchar not null default 'Q',
  "active_status" integer not null default '1',
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "course_comment_replies"(
  "id" integer primary key autoincrement not null,
  "comment_id" integer not null,
  "course_id" integer,
  "parent_course_id" integer,
  "instructor_id" integer not null,
  "user_id" integer not null,
  "reply" text not null,
  "active_status" integer not null default '1',
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "school_modules"(
  "id" integer primary key autoincrement not null,
  "modules" text,
  "menus" text,
  "module_name" varchar,
  "active_status" integer not null default '1',
  "updated_by" integer,
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fm_fees_transaction_chields"(
  "id" integer primary key autoincrement not null,
  "fees_type" varchar,
  "paid_amount" float,
  "service_charge" float,
  "fine" float,
  "weaver" float,
  "note" varchar,
  "fees_transaction_id" integer,
  "school_id" integer,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("fees_transaction_id") references "fm_fees_transactions"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "quiz_question_groups"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "active_status" integer not null default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "quiz_question_banks"(
  "id" integer primary key autoincrement not null,
  "type" varchar not null,
  "question" text,
  "marks" integer,
  "trueFalse" varchar,
  "suitable_words" text,
  "number_of_option" varchar,
  "active_status" integer not null default '1',
  "question_image" varchar,
  "q_group_id" integer,
  "answer_type" varchar,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "quiz_question_bank_mu_options"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "status" integer,
  "active_status" integer not null default '1',
  "question_bank_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "quizzes"(
  "id" integer primary key autoincrement not null,
  "quiz_title" varchar not null,
  "minimum_percentange" float not null default '50',
  "total_mark" float not null default '0',
  "instruction" text,
  "course_id" integer,
  "course_lesson_id" integer,
  "course_chapter_id" integer,
  "quiz_question" text,
  "active_status" tinyint(1) not null default '1',
  "school_id" integer not null default '1',
  "academic_id" integer not null,
  "created_by" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "parent_course_id" integer,
  foreign key("course_id") references "courses"("id") on delete cascade,
  foreign key("course_lesson_id") references "course_lessons"("id") on delete cascade,
  foreign key("course_chapter_id") references "course_chapters"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "quiz_exam_question_assigns"(
  "id" integer primary key autoincrement not null,
  "quiz_id" integer,
  "question_bank_id" integer,
  "created_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "parent_course_id" integer
);
CREATE TABLE IF NOT EXISTS "lms_hosts"(
  "id" integer primary key autoincrement not null,
  "host_name" varchar not null,
  "active_status" integer not null default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "course_files"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "file" text not null,
  "active_status" integer not null default '1',
  "file_lock" integer not null,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "course_id" integer,
  "parent_course_id" integer,
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("created_by") references "sm_staffs"("id") on delete cascade,
  foreign key("updated_by") references "sm_staffs"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "vimeo_videos"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "file" text,
  "url" text,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("created_by") references "sm_staffs"("id") on delete set null,
  foreign key("updated_by") references "sm_staffs"("id") on delete set null,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "quiz_participents"(
  "id" integer primary key autoincrement not null,
  "course_quiz_id" integer not null,
  "quiz_id" integer not null,
  "course_id" integer,
  "parent_course_id" integer,
  "student_id" integer not null,
  "active_status" tinyint(1) not null default '1',
  "total_mark" float not null,
  "obtained_mark" float,
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "assigned_by" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "quiz_answers"(
  "id" integer primary key autoincrement not null,
  "status" integer not null default '0',
  "student_done" integer not null default '0',
  "total_marks" varchar,
  "active_status" integer not null default '1',
  "student_id" integer,
  "quiz_id" integer,
  "right_answer" integer,
  "wrong_answer" integer,
  "deduct_marks" numeric,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "parent_course_id" integer
);
CREATE TABLE IF NOT EXISTS "quiz_student_answer_markings"(
  "id" integer primary key autoincrement not null,
  "quiz_id" integer,
  "student_id" integer,
  "option_id" integer,
  "question_id" integer,
  "user_answer" varchar,
  "answer_status" varchar,
  "obtain_marks" integer,
  "school_id" integer,
  "marked_by" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "parent_course_id" integer
);
CREATE TABLE IF NOT EXISTS "student_take_quiz_quesitons"(
  "id" integer primary key autoincrement not null,
  "trueFalse" varchar,
  "suitable_words" text,
  "active_status" integer not null default '1',
  "take_quiz_id" integer,
  "question_bank_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "student_take_quiz_que_options"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "status" integer,
  "active_status" integer not null default '1',
  "take_quiz_question_id" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "parent_course_id" integer,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_registration_fields"(
  "id" integer primary key autoincrement not null,
  "field_name" varchar,
  "label_name" varchar,
  "is_show" integer default '1',
  "active_status" integer default '1',
  "is_required" integer default '0',
  "student_edit" integer default '0',
  "parent_edit" integer default '0',
  "staff_edit" integer default '0',
  "type" integer,
  "is_system_required" integer default '0',
  "required_type" integer,
  "position" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "admin_section" varchar,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "lesson_plan_topics"(
  "id" integer primary key autoincrement not null,
  "sub_topic_title" varchar not null,
  "topic_id" integer,
  "lesson_planner_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("topic_id") references "sm_lesson_topic_details"("id") on delete cascade,
  foreign key("lesson_planner_id") references "lesson_planners"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "lms_certificate_generates"(
  "id" integer primary key autoincrement not null,
  "course_id" integer,
  "parent_course_id" integer,
  "student_id" integer not null,
  "quiz_id" integer not null,
  "certificate_id" integer not null,
  "active_status" tinyint(1) not null default '1',
  "school_id" integer not null default '1',
  "academic_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "admit_card_settings"(
  "id" integer primary key autoincrement not null,
  "student_photo" tinyint(1),
  "student_name" tinyint(1),
  "admission_no" tinyint(1),
  "class_section" tinyint(1),
  "exam_name" tinyint(1),
  "academic_year" tinyint(1),
  "principal_signature" tinyint(1),
  "class_teacher_signature" tinyint(1),
  "gaurdian_name" tinyint(1),
  "school_address" tinyint(1),
  "student_download" tinyint(1),
  "parent_download" tinyint(1),
  "student_notification" tinyint(1),
  "parent_notification" tinyint(1),
  "principal_signature_photo" varchar,
  "teacher_signature_photo" varchar,
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "admit_layout" integer not null default '1',
  "admit_sub_title" varchar,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "seat_plan_settings"(
  "id" integer primary key autoincrement not null,
  "school_name" tinyint(1),
  "student_photo" tinyint(1),
  "student_name" tinyint(1),
  "admission_no" tinyint(1),
  "class_section" tinyint(1),
  "exam_name" tinyint(1),
  "roll_no" tinyint(1),
  "academic_year" tinyint(1),
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "admit_cards"(
  "id" integer primary key autoincrement not null,
  "student_record_id" integer not null,
  "exam_type_id" integer not null,
  "created_by" integer not null,
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "active_status" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "seat_plans"(
  "id" integer primary key autoincrement not null,
  "student_record_id" integer not null,
  "exam_type_id" integer not null,
  "created_by" integer not null,
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "active_status" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_staff_registration_fields"(
  "id" integer primary key autoincrement not null,
  "field_name" varchar,
  "label_name" varchar,
  "active_status" integer default '1',
  "is_required" integer default '0',
  "staff_edit" integer default '0',
  "required_type" integer,
  "position" integer,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "fees_invoice_settings"(
  "id" integer primary key autoincrement not null,
  "per_th" integer not null default '2',
  "invoice_type" varchar not null default 'invoice',
  "student_name" integer not null default '1',
  "student_section" integer not null default '1',
  "student_class" integer not null default '1',
  "student_roll" integer not null default '1',
  "student_group" integer not null default '1',
  "student_admission_no" integer not null default '1',
  "footer_1" varchar default 'Parent/Student',
  "footer_2" varchar not null default 'Casier',
  "footer_3" varchar not null default 'Officer',
  "signature_p" integer not null default '1',
  "signature_c" integer not null default '1',
  "signature_o" integer not null default '1',
  "c_signature_p" integer not null default '1',
  "c_signature_c" integer not null default '0',
  "c_signature_o" integer not null default '1',
  "copy_s" varchar default 'Parent/Student',
  "copy_o" varchar not null default 'Office',
  "copy_c" varchar not null default 'Casier',
  "created_at" datetime,
  "updated_at" datetime,
  "copy_write_msg" text,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("updated_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "direct_fees_installments"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "fees_master_id" integer not null,
  "percentange" float not null,
  "amount" float not null,
  "due_date" date not null,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "direct_fees_installment_assigns"(
  "id" integer primary key autoincrement not null,
  "fees_installment_id" integer not null,
  "fees_master_ids" text,
  "amount" float,
  "paid_amount" float,
  "due_date" date,
  "payment_date" date,
  "payment_mode" varchar,
  "note" text,
  "slip" varchar,
  "active_status" integer not null default '0',
  "assign_ids" text,
  "bank_id" integer,
  "discount_amount" float default '0',
  "fees_discount_id" integer,
  "fees_type_id" integer,
  "student_id" integer,
  "record_id" integer,
  "collected_by" integer default '1',
  "academic_id" integer default '1',
  "created_by" integer,
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("bank_id") references "sm_bank_accounts"("id") on delete cascade,
  foreign key("fees_discount_id") references "sm_fees_discounts"("id") on delete cascade,
  foreign key("fees_type_id") references "sm_fees_types"("id") on delete cascade,
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "dire_fees_installment_child_payments"(
  "id" integer primary key autoincrement not null,
  "direct_fees_installment_assign_id" integer not null,
  "invoice_no" integer not null default '1',
  "amount" float,
  "paid_amount" float,
  "balance_amount" float,
  "payment_date" date,
  "payment_mode" varchar,
  "note" text,
  "slip" varchar,
  "active_status" integer not null default '0',
  "bank_id" integer,
  "discount_amount" float default '0',
  "fees_type_id" integer,
  "student_id" integer,
  "record_id" integer,
  "created_by" integer,
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("bank_id") references "sm_bank_accounts"("id") on delete cascade,
  foreign key("fees_type_id") references "sm_fees_types"("id") on delete cascade,
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fees_invoices"(
  "id" integer primary key autoincrement not null,
  "prefix" varchar,
  "start_form" integer,
  "un_academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "direct_fees_settings"(
  "id" integer primary key autoincrement not null,
  "fees_installment" tinyint(1) not null default '0',
  "fees_reminder" tinyint(1) not null default '0',
  "reminder_before" integer not null default '5',
  "no_installment" integer not null default '0',
  "due_date_from_sem" integer not null default '10',
  "end_day" integer,
  "academic_id" integer,
  "school_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "direct_fees_reminders"(
  "id" integer primary key autoincrement not null,
  "due_date_before" integer not null,
  "notification_types" varchar not null,
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "parent_courses"(
  "id" integer primary key autoincrement not null,
  "course_title" varchar not null,
  "category_id" integer not null,
  "sub_category_id" integer,
  "subject_id" integer,
  "instructor_id" integer not null,
  "slug" varchar not null,
  "overview" text,
  "description" text,
  "prerequisites" text,
  "video_link" varchar,
  "preview_image" varchar not null,
  "total_duration" varchar,
  "price" float not null default '0',
  "discount_price" float,
  "avg_rating" float not null default '0',
  "avaiable_for" varchar,
  "position_order" integer not null default '0',
  "active_status" integer not null default '1',
  "is_free" integer not null default '0',
  "related_course" text,
  "certificate_id" integer,
  "publish" integer not null default '0',
  "filename" text,
  "host_type" integer,
  "video_url" text,
  "vimeo" integer,
  "url" text,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("created_by") references "users"("id") on delete set null,
  foreign key("updated_by") references "users"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "gmeet_settings"(
  "id" integer primary key autoincrement not null,
  "api_key" varchar,
  "api_secret" varchar,
  "email_notification" integer default '60',
  "popup_notification" integer default '10',
  "is_main" integer default '0',
  "use_api" integer default '0',
  "user_id" integer,
  "individual_login" integer default '0',
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "gmeet_virtual_classes"(
  "id" integer primary key autoincrement not null,
  "meeting_id" varchar,
  "password" varchar,
  "start_time" datetime,
  "end_time" datetime,
  "class_id" varchar,
  "section_id" varchar,
  "course_id" integer,
  "chapter_id" integer,
  "lesson_id" integer,
  "gmeet_url" text,
  "event_id" varchar,
  "recurring_event_id" varchar,
  "calendar_link" text,
  "calendar_status" varchar,
  "visibility" varchar default 'private',
  "topic" varchar,
  "description" varchar,
  "attached_file" varchar,
  "date_of_meeting" varchar,
  "time_of_meeting" varchar,
  "meeting_duration" varchar,
  "time_before_start" integer,
  "is_recurring" tinyint(1),
  "recurring_type" integer,
  "recurring_repeat_day" integer,
  "weekly_days" varchar,
  "recurring_end_date" varchar,
  "status" integer not null default '1',
  "local_video" text,
  "video_link" text,
  "created_by" integer,
  "updated_by" integer,
  "school_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "shift_id" integer,
  "un_session_id" integer,
  "un_faculty_id" integer,
  "un_department_id" integer,
  "un_academic_id" integer,
  "un_semester_id" integer,
  "un_semester_label_id" integer,
  "un_section_id" integer
);
CREATE TABLE IF NOT EXISTS "gmeet_virtual_class_teachers"(
  "id" integer primary key autoincrement not null,
  "meeting_id" integer,
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "gmeet_virtual_meetings"(
  "id" integer primary key autoincrement not null,
  "meeting_id" varchar,
  "password" varchar,
  "start_time" datetime,
  "end_time" datetime,
  "topic" varchar,
  "description" varchar,
  "attached_file" varchar,
  "date_of_meeting" varchar,
  "time_of_meeting" varchar,
  "meeting_duration" varchar,
  "time_before_start" integer,
  "gmeet_url" text,
  "event_id" varchar,
  "recurring_event_id" varchar,
  "calendar_link" text,
  "calendar_status" varchar,
  "visibility" varchar default 'private',
  "is_recurring" tinyint(1),
  "recurring_type" integer,
  "recurring_repeat_day" integer,
  "weekly_days" varchar,
  "recurring_end_date" varchar,
  "status" integer not null default '1',
  "local_video" text,
  "video_link" text,
  "created_by" integer,
  "updated_by" integer,
  "school_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "gmeet_virtual_meeting_users"(
  "id" integer primary key autoincrement not null,
  "meeting_id" integer,
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "google_accounts"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "google_id" varchar,
  "name" varchar,
  "email" varchar,
  "token" text,
  "login_at" integer,
  "created_by" integer,
  "updated_by" integer,
  "school_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "infixedu__settings"(
  "section" varchar not null,
  "key" varchar not null,
  "value" text
);
CREATE TABLE IF NOT EXISTS "custom_sms_settings"(
  "id" integer primary key autoincrement not null,
  "gateway_id" integer not null,
  "gateway_name" varchar not null,
  "set_auth" varchar,
  "gateway_url" varchar not null,
  "request_method" varchar not null,
  "send_to_parameter_name" varchar not null,
  "messege_to_parameter_name" varchar not null,
  "param_key_1" varchar,
  "param_value_1" varchar,
  "param_key_2" varchar,
  "param_value_2" varchar,
  "param_key_3" varchar,
  "param_value_3" varchar,
  "param_key_4" varchar,
  "param_value_4" varchar,
  "param_key_5" varchar,
  "param_value_5" varchar,
  "param_key_6" varchar,
  "param_value_6" varchar,
  "param_key_7" varchar,
  "param_value_7" varchar,
  "param_key_8" varchar,
  "param_value_8" varchar,
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "colors"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "is_color" tinyint(1) default '1',
  "status" tinyint(1) default '1',
  "default_value" varchar,
  "lawn_green" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "themes"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "path_main_style" varchar,
  "path_infix_style" varchar,
  "replicate_theme" varchar,
  "color_mode" varchar not null default 'gradient',
  "box_shadow" tinyint(1) default '1',
  "background_type" varchar not null default 'image',
  "background_color" varchar,
  "background_image" varchar,
  "is_default" tinyint(1) not null default '0',
  "is_system" tinyint(1) not null default '0',
  "created_by" integer,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "color_theme"(
  "id" integer primary key autoincrement not null,
  "color_id" integer,
  "value" varchar,
  "theme_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("color_id") references "colors"("id") on delete cascade,
  foreign key("theme_id") references "themes"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "exam_step_skips"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "academic_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fees_installment_credits"(
  "id" integer primary key autoincrement not null,
  "student_id" integer not null,
  "student_record_id" integer not null,
  "active_status" tinyint(1) not null default '1',
  "school_id" integer not null,
  "amount" float not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "staff_import_bulk_temporaries"(
  "id" integer primary key autoincrement not null,
  "staff_no" integer,
  "first_name" varchar,
  "last_name" varchar,
  "full_name" varchar,
  "fathers_name" varchar,
  "mothers_name" varchar,
  "date_of_birth" date default '2026-03-12',
  "date_of_joining" date default '2026-03-12',
  "email" varchar,
  "mobile" varchar,
  "emergency_mobile" varchar,
  "marital_status" varchar,
  "staff_photo" varchar,
  "current_address" varchar,
  "permanent_address" varchar,
  "qualification" varchar,
  "experience" varchar,
  "epf_no" varchar,
  "basic_salary" varchar,
  "contract_type" varchar,
  "location" varchar,
  "casual_leave" varchar,
  "medical_leave" varchar,
  "maternity_leave" varchar,
  "bank_account_name" varchar,
  "bank_account_no" varchar,
  "bank_name" varchar,
  "bank_brach" varchar,
  "facebook_url" varchar,
  "twitter_url" varchar,
  "linkedin_url" varchar,
  "instagram_url" varchar,
  "joining_letter" varchar,
  "resume" varchar,
  "other_document" varchar,
  "notes" varchar,
  "active_status" integer not null default '1',
  "driving_license" varchar,
  "driving_license_ex_date" date,
  "role" varchar,
  "department" varchar,
  "designation" varchar,
  "gender_id" integer,
  "user_id" integer default '1',
  "parent_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "graduates"(
  "id" integer primary key autoincrement not null,
  "record_id" integer,
  "student_id" integer,
  "created_by" integer,
  "un_department_id" integer,
  "un_faculty_id" integer,
  "graduation_date" integer,
  "un_session_id" integer default '1',
  "school_id" integer not null default '1',
  "session_id" integer,
  "class_id" integer,
  "section_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "shift_id" integer,
  foreign key("student_id") references "sm_students"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("session_id") references "sm_sessions"("id") on delete cascade,
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("section_id") references "sm_sections"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "student_record_temporaries"(
  "id" integer primary key autoincrement not null,
  "sm_student_id" integer not null,
  "student_record_id" integer not null,
  "user_id" integer,
  "school_id" integer not null default '1',
  "active_status" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("sm_student_id") references "sm_students"("id") on delete cascade,
  foreign key("student_record_id") references "student_records"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "infixedu__pages"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "title" varchar not null,
  "description" text,
  "slug" varchar,
  "settings" text,
  "home_page" tinyint(1) default '0',
  "is_default" tinyint(1) default '0',
  "status" varchar check("status" in('draft', 'published')) not null default 'draft',
  "created_by" integer,
  "updated_by" integer,
  "published_by" integer,
  "school_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE INDEX "infixedu__pages_status_index" on "infixedu__pages"("status");
CREATE TABLE IF NOT EXISTS "matching_type_question_assigns"(
  "id" integer primary key autoincrement not null,
  "question_id" integer not null default '0',
  "option_id" integer not null default '0',
  "answer_id" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "comment_multi_attachments"(
  "id" integer primary key autoincrement not null,
  "comment_id" integer,
  "file" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("comment_id") references "comments"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "ticket_multi_attachments"(
  "id" integer primary key autoincrement not null,
  "ticket_id" integer,
  "file" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("ticket_id") references "tickets"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "permission_sections"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "position" integer not null default '9999',
  "user_id" integer not null default '1',
  "school_id" integer default '1',
  "saas" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sidebars"(
  "id" integer primary key autoincrement not null,
  "permission_id" integer,
  "position" integer,
  "section_id" integer default '1',
  "parent" integer,
  "parent_route" integer,
  "level" integer,
  "user_id" integer,
  "is_saas" integer not null default '0',
  "ignore" integer not null default '0',
  "role_id" integer,
  "active_status" integer not null default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "permissions"(
  "id" integer primary key autoincrement not null,
  "module" varchar,
  "sidebar_menu" varchar,
  "old_id" integer,
  "section_id" integer default '1',
  "parent_id" integer default '0',
  "name" varchar,
  "route" varchar,
  "parent_route" varchar,
  "type" integer,
  "lang_name" varchar,
  "icon" text,
  "svg" text,
  "status" integer not null default '1',
  "menu_status" integer not null default '1',
  "position" integer not null default '1',
  "is_saas" integer not null default '0',
  "relate_to_child" integer default '0',
  "is_menu" integer,
  "is_admin" integer default '0',
  "is_teacher" integer default '0',
  "is_student" integer default '0',
  "is_parent" integer default '0',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "permission_section" integer,
  "alternate_module" varchar,
  "user_id" integer,
  "role_id" integer,
  "school_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "is_alumni" integer default '0',
  "custom_menu_id" integer,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "assign_permissions"(
  "id" integer primary key autoincrement not null,
  "permission_id" integer,
  "role_id" integer,
  "status" tinyint(1) not null default '1',
  "menu_status" tinyint(1) not null default '1',
  "saas_schools" text,
  "created_by" integer not null default '1',
  "updated_by" integer not null default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "tickets"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "subject" varchar not null,
  "description" text,
  "active_status" integer,
  "assign_user" integer,
  "priority_id" integer,
  "category_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("academic_id") references sm_academic_years("id") on delete set null on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("updated_by") references users("id") on delete cascade on update no action,
  foreign key("created_by") references users("id") on delete cascade on update no action,
  foreign key("category_id") references categories("id") on delete cascade on update no action,
  foreign key("priority_id") references priorities("id") on delete cascade on update no action,
  foreign key("assign_user") references users("id") on delete cascade on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "sm_exam_signatures"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "signature" text not null,
  "active_status" integer not null default '1',
  "school_id" integer default '1',
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "user_otp_codes"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "otp_code" varchar not null,
  "expired_time" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "two_factor_settings"(
  "id" integer primary key autoincrement not null,
  "via_sms" tinyint(1) not null default '0',
  "via_email" tinyint(1) not null default '1',
  "for_student" integer not null default '2',
  "for_parent" integer not null default '3',
  "for_teacher" integer not null default '4',
  "for_staff" integer not null default '6',
  "for_admin" integer not null default '1',
  "expired_time" float not null default '300',
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "maintenance_settings"(
  "id" integer primary key autoincrement not null,
  "title" varchar default 'We will be back soon!',
  "sub_title" varchar default 'Sorry for the inconvenience but we are performing some maintenance at the moment.',
  "image" varchar,
  "applicable_for" varchar,
  "maintenance_mode" tinyint(1) default '0',
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "pulse_values"(
  "id" integer primary key autoincrement not null,
  "timestamp" integer not null,
  "type" varchar not null,
  "key" text not null,
  "key_hash" varchar not null,
  "value" text not null
);
CREATE INDEX "pulse_values_timestamp_index" on "pulse_values"("timestamp");
CREATE INDEX "pulse_values_type_index" on "pulse_values"("type");
CREATE UNIQUE INDEX "pulse_values_type_key_hash_unique" on "pulse_values"(
  "type",
  "key_hash"
);
CREATE TABLE IF NOT EXISTS "pulse_entries"(
  "id" integer primary key autoincrement not null,
  "timestamp" integer not null,
  "type" varchar not null,
  "key" text not null,
  "key_hash" varchar not null,
  "value" integer
);
CREATE INDEX "pulse_entries_timestamp_index" on "pulse_entries"("timestamp");
CREATE INDEX "pulse_entries_type_index" on "pulse_entries"("type");
CREATE INDEX "pulse_entries_key_hash_index" on "pulse_entries"("key_hash");
CREATE INDEX "pulse_entries_timestamp_type_key_hash_value_index" on "pulse_entries"(
  "timestamp",
  "type",
  "key_hash",
  "value"
);
CREATE TABLE IF NOT EXISTS "pulse_aggregates"(
  "id" integer primary key autoincrement not null,
  "bucket" integer not null,
  "period" integer not null,
  "type" varchar not null,
  "key" text not null,
  "key_hash" varchar not null,
  "aggregate" varchar not null,
  "value" numeric not null,
  "count" integer
);
CREATE UNIQUE INDEX "pulse_aggregates_bucket_period_type_aggregate_key_hash_unique" on "pulse_aggregates"(
  "bucket",
  "period",
  "type",
  "aggregate",
  "key_hash"
);
CREATE INDEX "pulse_aggregates_period_bucket_index" on "pulse_aggregates"(
  "period",
  "bucket"
);
CREATE INDEX "pulse_aggregates_type_index" on "pulse_aggregates"("type");
CREATE INDEX "pulse_aggregates_period_type_aggregate_bucket_index" on "pulse_aggregates"(
  "period",
  "type",
  "aggregate",
  "bucket"
);
CREATE TABLE IF NOT EXISTS "fees_carry_forward_settings"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "fees_due_days" integer not null,
  "payment_gateway" varchar not null,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "due_fees_login_prevents"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "role_id" integer,
  "school_id" integer not null default '1',
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("role_id") references "infix_roles"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "payroll_payments"(
  "id" integer primary key autoincrement not null,
  "sm_hr_payroll_generate_id" integer,
  "amount" double,
  "payment_mode" varchar,
  "payment_method_id" integer,
  "payment_date" date,
  "bank_id" integer,
  "note" varchar,
  "created_by" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("sm_hr_payroll_generate_id") references "sm_hr_payroll_generates"("id")
);
CREATE TABLE IF NOT EXISTS "in_app_live_class_settings"(
  "id" integer primary key autoincrement not null,
  "agora_app_id" varchar not null,
  "agora_app_certificate" varchar not null,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "in_app_live_classes"(
  "id" integer primary key autoincrement not null,
  "class_id" integer default '1',
  "section_id" integer,
  "created_by" integer default '1',
  "meeting_id" integer default '1',
  "instructor_id" integer default '1',
  "topic" text,
  "description" text,
  "date" text,
  "time" text,
  "datetime" text,
  "end_at" text,
  "duration" integer not null default '0',
  "settings" text,
  "school_id" integer default '1',
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "shift_id" integer,
  "un_session_id" integer,
  "un_faculty_id" integer,
  "un_department_id" integer,
  "un_academic_id" integer,
  "un_semester_id" integer,
  "un_semester_label_id" integer,
  "un_section_id" integer,
  foreign key("class_id") references "sm_classes"("id") on delete cascade,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "version_histories"(
  "id" integer primary key autoincrement not null,
  "version" varchar,
  "release_date" varchar,
  "url" varchar,
  "notes" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "behaviour_record_settings"(
  "id" integer primary key autoincrement not null,
  "student_comment" integer,
  "parent_comment" integer,
  "student_view" integer,
  "parent_view" integer,
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "assign_incident_comments"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "comment" text,
  "incident_id" integer not null,
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "teacher_evaluations"(
  "id" integer primary key autoincrement not null,
  "rating" text,
  "comment" varchar,
  "status" tinyint(1) default '0',
  "record_id" integer,
  "subject_id" integer,
  "teacher_id" integer,
  "student_id" integer,
  "role_id" integer,
  "parent_id" integer,
  "academic_id" integer,
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "teacher_evaluation_settings"(
  "id" integer primary key autoincrement not null,
  "is_enable" tinyint(1) not null default '0',
  "submitted_by" varchar not null default '[]', "rating_submission_time" varchar not null default 'any',
  "auto_approval" tinyint(1) not null default '1',
  "from_date" date,
  "to_date" date,
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "in_app_live_meetings"(
  "id" integer primary key autoincrement not null,
  "role_id" integer,
  "user_ids" text,
  "created_by" integer default '1',
  "meeting_id" integer default '1',
  "topic" text,
  "description" text,
  "date" text,
  "time" text,
  "datetime" text,
  "end_at" text,
  "duration" integer not null default '0',
  "settings" text,
  "school_id" integer default '1',
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("role_id") references "roles"("id") on delete cascade,
  foreign key("created_by") references "users"("id") on delete cascade,
  foreign key("school_id") references "sm_schools"("id") on delete cascade,
  foreign key("academic_id") references "sm_academic_years"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "frontend_exam_results"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "description" text,
  "main_title" varchar,
  "main_description" text,
  "image" varchar,
  "main_image" varchar,
  "button_text" varchar,
  "button_url" varchar,
  "active_status" integer not null default '1',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_calendar_settings"(
  "id" integer primary key autoincrement not null,
  "menu_name" varchar not null,
  "status" integer not null default '0',
  "font_color" varchar not null,
  "bg_color" varchar not null,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_class_exam_routine_pages"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "title" varchar,
  "description" text,
  "main_title" varchar,
  "main_description" text,
  "image" varchar,
  "main_image" varchar,
  "button_text" varchar,
  "button_url" varchar,
  "active_status" integer not null default '1',
  "is_parent" tinyint(1) not null default '1',
  "class_routine" varchar not null default 'show',
  "exam_routine" varchar not null default 'show',
  "created_by" integer default '1',
  "updated_by" integer default '1',
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "regions"(
  "id" integer primary key autoincrement not null,
  "region_name" varchar not null,
  "zip_code" varchar,
  "active_status" tinyint(1) not null default '1',
  "description" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "home_sliders"(
  "id" integer primary key autoincrement not null,
  "image" varchar not null,
  "link" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_photo_galleries"(
  "id" integer primary key autoincrement not null,
  "parent_id" integer,
  "name" varchar,
  "description" text,
  "feature_image" varchar,
  "gallery_image" varchar,
  "is_publish" tinyint(1) not null default '1',
  "position" integer not null default '0',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_video_galleries"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "description" text,
  "video_link" text,
  "is_publish" tinyint(1) not null default '1',
  "position" integer not null default '0',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "front_results"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "publish_date" varchar,
  "result_file" varchar,
  "link" varchar,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "front_exam_routines"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "publish_date" varchar,
  "result_file" varchar,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "front_class_routines"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "publish_date" varchar,
  "result_file" varchar,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "certificate_types"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "short_code" text not null,
  "role_id" integer not null,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "certificate_templates"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "certificate_type_id" integer not null,
  "layout" integer not null,
  "width" varchar,
  "height" varchar,
  "margin_top" double,
  "margin_bottom" double,
  "margin_left" double,
  "margin_right" double,
  "user_photo_style" integer default '1',
  "user_image_size" varchar,
  "qr_code" text,
  "qr_image_size" varchar,
  "background_image" varchar,
  "logo_image" varchar,
  "signature_image" varchar,
  "signature_name" varchar,
  "content" text,
  "status" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "certificate_settings"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "value" text,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1'
);
CREATE TABLE IF NOT EXISTS "front_academic_calendars"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "publish_date" varchar,
  "calendar_file" varchar,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "certificate_template_designs"(
  "id" integer primary key autoincrement not null,
  "certificate_template_id" integer,
  "design_content" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_news_comments"(
  "id" integer primary key autoincrement not null,
  "message" text not null,
  "news_id" integer,
  "user_id" integer,
  "parent_id" integer,
  "status" integer default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("news_id") references "sm_news"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "certificate_records"(
  "id" integer primary key autoincrement not null,
  "certificate_number" varchar not null,
  "certificate_path" varchar not null,
  "user_id" integer not null,
  "template_id" integer,
  "class_id" integer,
  "section_id" integer,
  "exam_id" integer,
  "academic_id" integer default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "certificate_records_certificate_number_unique" on "certificate_records"(
  "certificate_number"
);
CREATE TABLE IF NOT EXISTS "ai_templates"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar,
  "icon" varchar,
  "type" integer not null default '1',
  "status" integer not null default '1',
  "created_by" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "ai_template_contents"(
  "id" integer primary key autoincrement not null,
  "template_id" integer,
  "content" text,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "ai_generated_contents"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "input_text" varchar,
  "output_text" text,
  "model" varchar,
  "tokens" integer,
  "template_id" integer,
  "words" integer,
  "temperature" integer,
  "frequency_penalty" integer,
  "lang" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "ai_content_settings"(
  "id" integer primary key autoincrement not null,
  "ai_default_model" varchar,
  "ai_default_language" varchar,
  "ai_default_tone" varchar,
  "ai_max_result_length" varchar,
  "ai_default_creativity" varchar,
  "open_ai_secret_key" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "agents"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "designation" varchar not null,
  "avatar" varchar,
  "number" varchar not null,
  "status" tinyint(1) not null default '1',
  "always_available" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "agent_times"(
  "id" integer primary key autoincrement not null,
  "agent_id" integer not null,
  "day" varchar not null,
  "start" time not null,
  "end" time not null,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("agent_id") references "agents"("id"),
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "messages"(
  "id" integer primary key autoincrement not null,
  "message" text,
  "ip" varchar,
  "number" varchar,
  "device_type" varchar,
  "os" varchar,
  "browser" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "settings"(
  "id" integer primary key autoincrement not null,
  "agent_type" varchar not null default 'single',
  "availability" varchar not null default 'both',
  "showing_page" varchar not null default 'all',
  "color" varchar not null default '#0dc152',
  "intro_text" text,
  "welcome_message" text,
  "homepage_url" text not null,
  "primary_number" varchar not null,
  "open_popup" tinyint(1) not null default '0',
  "disable_for_admin_panel" tinyint(1) not null default '0',
  "show_unavailable_agent" tinyint(1) not null default '1',
  "layout" integer not null default '1',
  "bubble_logo" varchar,
  "layout_preview_url" varchar not null default 'whatsapp-support/preview-1.png',
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "course_levels"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "status" integer not null default '1',
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "main_id" integer
);
CREATE TABLE IF NOT EXISTS "speech_sliders"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "designation" varchar,
  "title" varchar,
  "speech" text,
  "image" varchar,
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "plugins"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "is_enable" tinyint(1) not null default '0',
  "availability" varchar not null default 'both',
  "show_admin_panel" tinyint(1) not null default '0',
  "show_website" tinyint(1) not null default '1',
  "showing_page" varchar not null default 'all',
  "applicable_for" varchar,
  "position" varchar,
  "short_code" varchar,
  "school_id" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_donors"(
  "id" integer primary key autoincrement not null,
  "full_name" varchar,
  "profession" varchar,
  "date_of_birth" date,
  "email" varchar,
  "mobile" varchar,
  "photo" varchar,
  "age" varchar,
  "current_address" varchar,
  "permanent_address" varchar,
  "show_public" integer not null default '1',
  "custom_field" text,
  "custom_field_form_name" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "bloodgroup_id" integer,
  "religion_id" integer,
  "gender_id" integer,
  "school_id" integer default '1',
  foreign key("bloodgroup_id") references "sm_base_setups"("id") on delete set null,
  foreign key("religion_id") references "sm_base_setups"("id") on delete set null,
  foreign key("gender_id") references "sm_base_setups"("id") on delete set null,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_form_downloads"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "short_description" varchar,
  "publish_date" date,
  "link" varchar,
  "file" varchar,
  "show_public" integer not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default '1',
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "exam_merit_positions"(
  "id" integer primary key autoincrement not null,
  "class_id" integer,
  "section_id" integer,
  "exam_term_id" integer,
  "total_mark" double not null default('0'),
  "position" integer,
  "admission_no" integer,
  "gpa" float,
  "grade" varchar,
  "record_id" integer,
  "school_id" integer not null,
  "academic_id" integer not null,
  "active_status" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "shift_id" integer
);
CREATE TABLE IF NOT EXISTS "sm_notification_settings"(
  "id" integer primary key autoincrement not null,
  "event" varchar,
  "destination" varchar,
  "recipient" varchar,
  "subject" varchar,
  "template" text,
  "school_id" integer default('1'),
  "shortcode" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "sm_events"(
  "id" integer primary key autoincrement not null,
  "event_title" varchar,
  "for_whom" varchar,
  "role_ids" text,
  "url" text,
  "event_location" varchar,
  "event_des" text not null,
  "from_date" date,
  "to_date" date,
  "uplad_image_file" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "qr_attendance_settings"(
  "id" integer primary key autoincrement not null,
  "class_id" integer,
  "section_id" integer,
  "time" time,
  "school_id" integer,
  "subject_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "vimeo_settings"(
  "id" integer primary key autoincrement not null,
  "vimeo_app_id" varchar,
  "vimeo_client" varchar,
  "vimeo_secret" varchar,
  "vimeo_access" varchar,
  "api_use" tinyint(1),
  "upload_type" varchar not null default('Direct'),
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("updated_by") references sm_staffs("id") on delete set null on update no action,
  foreign key("created_by") references sm_staffs("id") on delete set null on update no action
);
CREATE TABLE IF NOT EXISTS "terms"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "code" varchar not null,
  "description" varchar,
  "school_id" integer not null,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "observation_parameters"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "school_id" integer,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "observations"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" varchar,
  "school_id" integer not null,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "parameters_of_observation"(
  "id" integer primary key autoincrement not null,
  "parameter_id" integer not null,
  "observation_id" integer not null,
  "max_mark" double not null default '0',
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "marksheet_templates"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "class_id" integer,
  "marksheet_type" integer,
  "header_image" varchar,
  "left_sign" varchar,
  "right_sign" varchar,
  "middle_sign" varchar,
  "background_image" varchar,
  "school_name" varchar,
  "center_name" varchar,
  "print_date" date,
  "description" text,
  "footer_text" text,
  "student_name" integer,
  "father_name" integer,
  "mother_name" integer,
  "academic_session" integer,
  "admission_no" integer,
  "roll_no" integer,
  "photo" integer,
  "class" integer,
  "section" integer,
  "birth_date" integer,
  "teacher_remarks" integer,
  "cbse_show" integer,
  "school_id" integer,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "marksheet_sections"(
  "id" integer primary key autoincrement not null,
  "section_id" integer,
  "template_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "cbse_exam_assignments"(
  "id" integer primary key autoincrement not null,
  "assignment" varchar not null,
  "description" text,
  "school_id" integer not null,
  "academic_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "cbse_exam_assignment_attributes"(
  "id" integer primary key autoincrement not null,
  "assignment_type" varchar not null,
  "code" varchar not null,
  "description" text,
  "maximum_mark" integer not null default '0',
  "pass_percentage" double not null default '0',
  "cbse_exam_assignment_id" integer,
  "school_id" integer,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "assign_observations"(
  "id" integer primary key autoincrement not null,
  "observation_id" integer not null,
  "term_id" integer not null,
  "description" text,
  "school_id" integer not null,
  "academic_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "cbse_exam_grades"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "description" varchar not null,
  "school_id" integer,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "cbse_exams"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "is_published" integer not null default '0',
  "description" text,
  "term_id" integer not null,
  "class_id" integer not null,
  "assignment_id" integer not null,
  "grade_id" integer not null,
  "total_attendance_day" integer not null default '0',
  "school_id" integer,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "result_publish" integer not null default '0'
);
CREATE TABLE IF NOT EXISTS "cbse_exam_sections"(
  "id" integer primary key autoincrement not null,
  "section_id" integer not null,
  "cbse_exam_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_menus"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "module" varchar,
  "route" varchar,
  "lang_name" varchar,
  "section_id" integer,
  "icon" varchar,
  "status" integer,
  "is_saas" integer,
  "role_id" integer,
  "is_alumni" integer,
  "menu_status" integer,
  "permission_section" integer,
  "position" integer,
  "default_position" integer,
  "parent" integer,
  "parent_id" integer,
  "school_id" integer,
  "alternate_module" varchar,
  "permission_id" integer,
  "ignore" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "default_menus"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "module" varchar,
  "route" varchar,
  "lang_name" varchar,
  "section_id" integer,
  "icon" varchar,
  "status" integer,
  "is_saas" integer,
  "role_id" integer,
  "is_alumni" integer,
  "menu_status" integer,
  "permission_section" integer,
  "position" integer,
  "default_position" integer,
  "parent" integer,
  "parent_id" integer,
  "school_id" integer,
  "alternate_module" varchar,
  "permission_id" integer,
  "ignore" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "all_exam_wise_positions"(
  "id" integer primary key autoincrement not null,
  "class_id" integer,
  "section_id" integer,
  "total_mark" float,
  "position" integer,
  "roll_no" integer,
  "admission_no" integer,
  "gpa" float,
  "grade" varchar,
  "record_id" integer,
  "school_id" integer not null,
  "academic_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  "shift_id" integer
);
CREATE TABLE IF NOT EXISTS "jitsi_virtual_classes"(
  "id" integer primary key autoincrement not null,
  "created_by" integer default('1'),
  "meeting_id" text,
  "start_time" datetime,
  "end_time" datetime,
  "class_id" integer,
  "section_id" varchar,
  "subject_id" varchar,
  "topic" text,
  "description" text,
  "time_start_before" text,
  "duration" integer default('0'),
  "date" text,
  "time" text,
  "datetime" text,
  "attached_file" text,
  "created_at" datetime,
  "updated_at" datetime,
  "un_session_id" integer,
  "un_faculty_id" integer,
  "un_department_id" integer,
  "un_academic_id" integer,
  "un_semester_id" integer,
  "un_semester_label_id" integer,
  "un_section_id" integer,
  "shift_id" integer,
  foreign key("shift_id") references "shifts"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "courses"(
  "id" integer primary key autoincrement not null,
  "course_title" varchar not null,
  "parent_course_id" integer,
  "category_id" integer,
  "sub_category_id" integer,
  "subject_id" integer,
  "instructor_id" integer not null,
  "slug" varchar not null,
  "overview" text,
  "description" text,
  "prerequisites" text,
  "video_link" varchar,
  "preview_image" varchar,
  "total_duration" varchar,
  "price" float not null default('0'),
  "discount_price" float,
  "avg_rating" float not null default('0'),
  "avaiable_for" varchar,
  "class_id" integer,
  "section_id" integer,
  "position_order" integer not null default('0'),
  "active_status" integer not null default('1'),
  "is_free" integer not null default('0'),
  "related_course" text,
  "certificate_id" integer,
  "publish" integer not null default('0'),
  "filename" text,
  "host_type" integer,
  "video_url" text,
  "vimeo" integer,
  "url" text,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "academic_id" integer default('1'),
  "school_id" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "type" integer default('1'),
  "drip" integer,
  "online_exam" integer,
  "level" integer,
  "complete_order" integer,
  "scope" integer,
  "access_limit" integer,
  "meta_keywords" text,
  "meta_description" text,
  "un_academic_id" integer default('1'),
  "un_semester_id" integer,
  "un_semester_label_id" integer,
  "un_section_id" integer,
  "un_subject_id" integer,
  "un_faculty_id" integer,
  "un_department_id" integer,
  "un_session_id" integer,
  "shift_id" integer,
  "delivery_mode" integer not null default '1',
  "main_id" integer,
  foreign key("created_by") references users("id") on delete set null on update no action,
  foreign key("updated_by") references users("id") on delete set null on update no action,
  foreign key("shift_id") references "shifts"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "lesson_completes"(
  "id" integer primary key autoincrement not null,
  "course_id" integer,
  "chapter_id" integer,
  "lesson_id" integer,
  "virtual_class_id" integer,
  "homework_id" integer,
  "online_exam_id" integer,
  "quiz_id" integer,
  "student_id" integer,
  "duration" varchar,
  "active_status" integer not null default('0'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "parent_course_id" integer
);
CREATE TABLE IF NOT EXISTS "cbse_exam_students"(
  "id" integer primary key autoincrement not null,
  "exam_id" integer,
  "class_id" integer,
  "section_id" integer,
  "record_id" integer,
  "academic_id" integer,
  "student_id" integer,
  "remarks" varchar,
  "attendance_percentage" integer not null default '0',
  "school_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "cbse_exam_subjects"(
  "id" integer primary key autoincrement not null,
  "subject_id" integer not null,
  "exam_id" integer not null,
  "class_id" integer not null,
  "assessment_id" integer not null,
  "academic_id" integer,
  "assesment_attributes" text,
  "date" date,
  "start_time" time,
  "duration" varchar,
  "end_time" time,
  "room_no" varchar,
  "school_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "cbse_exam_assesment_marks"(
  "id" integer primary key autoincrement not null,
  "assessment_attribute_id" integer,
  "cbse_exam_mark_id" integer,
  "exam_id" integer,
  "class_id" integer,
  "cbse_exam_subject_id" integer,
  "academic_id" integer,
  "mark" double not null default '0',
  "school_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "cbse_exam_marks"(
  "id" integer primary key autoincrement not null,
  "subject_id" integer,
  "cbse_exam_subject_id" integer,
  "cbse_exam_student_id" integer,
  "record_id" integer,
  "exam_id" integer,
  "class_id" integer,
  "academic_id" integer,
  "is_absens" integer,
  "total_mark" double,
  "school_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "note" varchar
);
CREATE TABLE IF NOT EXISTS "template_links"(
  "id" integer primary key autoincrement not null,
  "template_id" integer,
  "remarks" integer not null,
  "grading" integer,
  "type" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "template_link_terms"(
  "id" integer primary key autoincrement not null,
  "link_id" integer,
  "term_id" integer,
  "term_weightage" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "template_link_exams"(
  "id" integer primary key autoincrement not null,
  "link_id" integer,
  "term_id" integer,
  "exam_id" integer,
  "weightage" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "cbse_exam_student_ranks"(
  "id" integer primary key autoincrement not null,
  "student_id" integer,
  "exam_id" integer,
  "class_id" integer,
  "section_id" integer,
  "total_marks" double,
  "rank" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "cbse_exam_results"(
  "id" integer primary key autoincrement not null,
  "student_id" integer,
  "exam_id" integer,
  "subject_id" integer,
  "mark" integer,
  "rank" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "cbse_exam_grade_marks"(
  "id" integer primary key autoincrement not null,
  "grade_id" integer not null,
  "grade" varchar not null,
  "max_percentage" integer not null,
  "min_percentage" integer not null,
  "remarks" varchar,
  "school_id" integer not null,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "observation_marks"(
  "id" integer primary key autoincrement not null,
  "student_id" integer,
  "assign_observation_id" integer,
  "param_id" integer,
  "mark" double not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "template_wise_results"(
  "id" integer primary key autoincrement not null,
  "student_id" integer,
  "template_id" integer,
  "mark" integer,
  "rank" integer,
  "academic_id" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "cbse_exam_subject_wise_ranks"(
  "id" integer primary key autoincrement not null,
  "subject_id" integer,
  "cbse_exam_subject_id" integer,
  "template_id" integer,
  "student_id" integer,
  "mark" double not null default '0',
  "rank" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sm_leave_deduction_infos"(
  "id" integer primary key autoincrement not null,
  "staff_id" integer,
  "payroll_id" integer,
  "extra_leave" integer,
  "salary_deduct" double,
  "pay_month" varchar,
  "pay_year" varchar,
  "active_status" integer default('0'),
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete restrict on update no action
);
CREATE TABLE IF NOT EXISTS "sm_hr_payroll_earn_deducs"(
  "id" integer primary key autoincrement not null,
  "type_name" varchar,
  "amount" double,
  "earn_dedc_type" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "payroll_generate_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("payroll_generate_id") references sm_hr_payroll_generates("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "sm_add_incomes"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "date" date,
  "amount" double,
  "file" varchar,
  "description" text,
  "item_sell_id" integer,
  "fees_collection_id" integer,
  "inventory_id" integer,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "income_head_id" integer,
  "account_id" integer,
  "payment_method_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer,
  "installment_payment_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("payment_method_id") references sm_payment_methhods("id") on delete cascade on update no action,
  foreign key("account_id") references sm_bank_accounts("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "sm_add_expenses"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "date" date,
  "amount" double,
  "file" varchar,
  "description" text,
  "item_receive_id" integer,
  "inventory_id" integer,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "expense_head_id" integer,
  "account_id" integer,
  "payment_method_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "payroll_payment_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "sm_bank_statements"(
  "id" integer primary key autoincrement not null,
  "bank_id" integer,
  "after_balance" integer,
  "amount" double,
  "type" varchar,
  "payment_method" integer,
  "details" varchar,
  "item_receive_id" integer,
  "item_receive_bank_statement_id" integer,
  "item_sell_bank_statement_id" integer,
  "item_sell_id" integer,
  "payment_date" date,
  "active_status" integer not null default('1'),
  "school_id" integer default('1'),
  "academic_id" integer,
  "fees_payment_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "payroll_payment_id" integer
);
CREATE TABLE IF NOT EXISTS "sm_fees_assigns"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "fees_amount" double,
  "applied_discount" float,
  "fees_master_id" integer,
  "fees_discount_id" integer,
  "record_id" integer,
  "student_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "class_id" integer,
  "section_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("student_id") references sm_students("id") on delete cascade on update no action,
  foreign key("fees_discount_id") references sm_fees_discounts("id") on delete cascade on update no action,
  foreign key("fees_master_id") references sm_fees_masters("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "sm_item_receives"(
  "id" integer primary key autoincrement not null,
  "receive_date" date,
  "reference_no" varchar,
  "grand_total" double,
  "total_quantity" numeric not null,
  "total_paid" double,
  "total_due" double,
  "expense_head_id" integer,
  "account_id" integer,
  "payment_method" varchar,
  "paid_status" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "supplier_id" integer,
  "store_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  foreign key("supplier_id") references sm_suppliers("id") on delete cascade on update no action,
  foreign key("store_id") references sm_item_stores("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "sm_item_receive_children"(
  "id" integer primary key autoincrement not null,
  "unit_price" double,
  "quantity" numeric not null,
  "sub_total" double,
  "description" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "item_id" integer,
  "item_receive_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("item_receive_id") references sm_item_receives("id") on delete cascade on update no action,
  foreign key("item_id") references sm_items("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "sm_item_sells"(
  "id" integer primary key autoincrement not null,
  "student_staff_id" integer,
  "sell_date" date,
  "reference_no" varchar,
  "grand_total" double,
  "total_quantity" numeric not null,
  "total_paid" double,
  "total_due" double,
  "income_head_id" integer,
  "account_id" integer,
  "payment_method" varchar,
  "paid_status" varchar,
  "description" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "role_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  foreign key("role_id") references roles("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "sm_item_sell_children"(
  "id" integer primary key autoincrement not null,
  "sell_price" double,
  "quantity" numeric not null,
  "sub_total" double,
  "description" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "item_sell_id" integer,
  "item_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "sm_amount_transfers"(
  "id" integer primary key autoincrement not null,
  "amount" double,
  "purpose" varchar,
  "from_payment_method" integer,
  "from_bank_name" integer,
  "to_payment_method" integer,
  "to_bank_name" integer,
  "transfer_date" date,
  "active_status" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "branches"(
  "id" integer primary key autoincrement not null,
  "branch_name" varchar not null,
  "contact_number" varchar,
  "email" varchar,
  "address" varchar,
  "status" integer not null default '1',
  "school_id" integer default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("school_id") references "sm_schools"("id") on delete cascade
);
CREATE UNIQUE INDEX "branches_branch_name_unique" on "branches"("branch_name");
CREATE UNIQUE INDEX "branches_contact_number_unique" on "branches"(
  "contact_number"
);
CREATE UNIQUE INDEX "branches_email_unique" on "branches"("email");
CREATE TABLE IF NOT EXISTS "sm_admission_queries"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "phone" varchar,
  "email" varchar,
  "address" text,
  "description" text,
  "date" date,
  "follow_up_date" date,
  "next_follow_up_date" date,
  "assigned" varchar,
  "reference" integer,
  "source" integer,
  "no_of_child" integer,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "class" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("class") references sm_classes("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_visitors"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "phone" varchar,
  "visitor_id" varchar,
  "no_of_person" integer,
  "purpose" varchar,
  "date" date,
  "in_time" varchar,
  "out_time" varchar,
  "file" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_complaints"(
  "id" integer primary key autoincrement not null,
  "complaint_by" varchar,
  "complaint_type" integer,
  "complaint_source" integer,
  "phone" varchar,
  "date" date,
  "description" text,
  "action_taken" varchar,
  "assigned" varchar,
  "file" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_postal_receives"(
  "id" integer primary key autoincrement not null,
  "from_title" varchar,
  "to_title" varchar,
  "reference_no" varchar,
  "address" varchar,
  "date" date,
  "note" text,
  "file" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_postal_dispatches"(
  "id" integer primary key autoincrement not null,
  "to_title" varchar,
  "from_title" varchar,
  "reference_no" varchar,
  "address" varchar,
  "date" date,
  "note" text,
  "file" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_phone_call_logs"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "phone" varchar,
  "date" date,
  "description" text,
  "next_follow_up_date" date,
  "call_duration" varchar,
  "call_type" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_setup_admins"(
  "id" integer primary key autoincrement not null,
  "type" integer,
  "name" varchar,
  "description" text,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_id_cards"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "logo" varchar,
  "signature" varchar,
  "background_img" varchar,
  "profile_image" varchar,
  "role_id" text,
  "page_layout_style" varchar,
  "user_photo_style" varchar,
  "user_photo_width" varchar,
  "user_photo_height" varchar,
  "pl_width" integer,
  "pl_height" integer,
  "t_space" integer,
  "b_space" integer,
  "r_space" integer,
  "l_space" integer,
  "admission_no" varchar,
  "student_name" varchar,
  "class" varchar,
  "father_name" varchar,
  "mother_name" varchar,
  "student_address" varchar,
  "phone_number" varchar,
  "dob" varchar,
  "blood" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "photo" integer not null default('1'),
  "signature_status" integer not null default('1'),
  "staff_department" integer not null default('0'),
  "staff_designation" integer not null default('0'),
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_certificates"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "header_left_text" varchar,
  "date" date,
  "body" text,
  "body_two" text,
  "certificate_no" text,
  "type" varchar,
  "footer_left_text" varchar,
  "footer_center_text" varchar,
  "footer_right_text" varchar,
  "student_photo" integer not null default('1'),
  "file" varchar,
  "layout" integer,
  "body_font_family" varchar,
  "body_font_size" varchar,
  "height" varchar,
  "width" varchar,
  "default_for" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_sections"(
  "id" integer primary key autoincrement not null,
  "parent_id" integer,
  "section_name" varchar not null,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "un_academic_id" integer,
  "academic_id" integer default('1'),
  "main_id" integer,
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_classes"(
  "id" integer primary key autoincrement not null,
  "class_name" varchar not null,
  "pass_mark" float,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer,
  "parent_id" integer,
  "shift_id" integer,
  "main_id" integer,
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_class_sections"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "class_id" integer,
  "section_id" integer,
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "parent_id" integer,
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE INDEX "sm_class_sections_class_id_section_id_index" on "sm_class_sections"(
  "class_id",
  "section_id"
);
CREATE TABLE IF NOT EXISTS "shifts"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "start_time" varchar not null,
  "end_time" varchar not null,
  "active_status" integer not null default('1'),
  "school_id" integer default('1'),
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  "academic_id" integer,
  "branch_id" integer,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_subjects"(
  "id" integer primary key autoincrement not null,
  "subject_name" varchar not null,
  "subject_code" varchar,
  "pass_mark" float,
  "subject_type" varchar not null default('T'),
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "parent_id" integer,
  "main_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_assign_subjects"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "teacher_id" integer,
  "class_id" integer,
  "section_id" integer,
  "subject_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "parent_id" integer,
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("teacher_id") references sm_staffs("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_assign_class_teachers"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "class_id" integer,
  "section_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_class_teachers"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "teacher_id" integer,
  "assign_class_teacher_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("assign_class_teacher_id") references sm_assign_class_teachers("id") on delete cascade on update no action,
  foreign key("teacher_id") references sm_staffs("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_class_rooms"(
  "id" integer primary key autoincrement not null,
  "room_no" varchar,
  "capacity" integer,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_optional_subject_assigns"(
  "id" integer primary key autoincrement not null,
  "student_id" integer,
  "record_id" integer,
  "subject_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "session_id" integer not null,
  "academic_id" integer default('1'),
  "active_status" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("session_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("student_id") references sm_students("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_class_optional_subject"(
  "id" integer primary key autoincrement not null,
  "class_id" integer not null,
  "gpa_above" float not null,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_class_routine_updates"(
  "id" integer primary key autoincrement not null,
  "day" integer,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "start_time" time,
  "end_time" time,
  "is_break" integer,
  "room_id" integer,
  "teacher_id" integer,
  "class_period_id" integer,
  "subject_id" integer,
  "class_id" integer,
  "section_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("class_period_id") references sm_class_times("id") on delete cascade on update no action,
  foreign key("teacher_id") references sm_staffs("id") on delete cascade on update no action,
  foreign key("room_id") references sm_class_rooms("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_students"(
  "id" integer primary key autoincrement not null,
  "admission_no" integer,
  "roll_no" integer,
  "first_name" varchar,
  "last_name" varchar,
  "full_name" varchar,
  "date_of_birth" date,
  "caste" varchar,
  "email" varchar,
  "mobile" varchar,
  "admission_date" date,
  "student_photo" varchar,
  "age" varchar,
  "height" varchar,
  "weight" varchar,
  "current_address" text,
  "permanent_address" text,
  "driver_id" varchar,
  "national_id_no" varchar,
  "local_id_no" varchar,
  "bank_account_no" varchar,
  "bank_name" varchar,
  "previous_school_details" varchar,
  "aditional_notes" text,
  "ifsc_code" varchar,
  "document_title_1" varchar,
  "document_file_1" varchar,
  "document_title_2" varchar,
  "document_file_2" varchar,
  "document_title_3" varchar,
  "document_file_3" varchar,
  "document_title_4" varchar,
  "document_file_4" varchar,
  "active_status" integer not null default('1'),
  "custom_field" text,
  "custom_field_form_name" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "bloodgroup_id" integer,
  "religion_id" integer,
  "route_list_id" integer,
  "dormitory_id" integer,
  "vechile_id" integer,
  "room_id" integer,
  "student_category_id" integer,
  "student_group_id" integer,
  "class_id" integer,
  "section_id" integer,
  "session_id" integer,
  "parent_id" integer,
  "user_id" integer,
  "role_id" integer,
  "gender_id" integer,
  "school_id" integer not null default('1'),
  "academic_id" integer,
  "un_academic_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("gender_id") references sm_base_setups("id") on delete set null on update no action,
  foreign key("role_id") references infix_roles("id") on delete cascade on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("parent_id") references sm_parents("id") on delete set null on update no action,
  foreign key("session_id") references sm_academic_years("id") on delete set null on update no action,
  foreign key("section_id") references sm_sections("id") on delete set null on update no action,
  foreign key("class_id") references sm_classes("id") on delete set null on update no action,
  foreign key("student_group_id") references sm_student_groups("id") on delete set null on update no action,
  foreign key("student_category_id") references sm_student_categories("id") on delete set null on update no action,
  foreign key("room_id") references sm_room_lists("id") on delete set null on update no action,
  foreign key("vechile_id") references sm_vehicles("id") on delete set null on update no action,
  foreign key("dormitory_id") references sm_dormitory_lists("id") on delete set null on update no action,
  foreign key("route_list_id") references sm_routes("id") on delete set null on update no action,
  foreign key("religion_id") references sm_base_setups("id") on delete set null on update no action,
  foreign key("bloodgroup_id") references sm_base_setups("id") on delete set null on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "student_records"(
  "id" integer primary key autoincrement not null,
  "class_id" integer,
  "section_id" integer,
  "roll_no" varchar,
  "is_promote" tinyint(1) default('0'),
  "is_default" integer default('0'),
  "session_id" integer,
  "school_id" integer not null default('1'),
  "academic_id" integer,
  "student_id" integer,
  "active_status" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "is_graduate" tinyint(1) default('0'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("student_id") references sm_students("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("session_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_parents"(
  "id" integer primary key autoincrement not null,
  "fathers_name" varchar,
  "fathers_mobile" varchar,
  "fathers_occupation" varchar,
  "fathers_photo" varchar,
  "mothers_name" varchar,
  "mothers_mobile" varchar,
  "mothers_occupation" varchar,
  "mothers_photo" varchar,
  "relation" varchar,
  "guardians_name" varchar,
  "guardians_mobile" varchar,
  "guardians_email" varchar,
  "guardians_occupation" varchar,
  "guardians_relation" varchar,
  "guardians_photo" varchar,
  "guardians_address" varchar,
  "is_guardian" integer,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "full_name" varchar,
  "username" varchar,
  "phone_number" varchar,
  "email" varchar,
  "password" varchar,
  "usertype" varchar,
  "active_status" integer not null default('1'),
  "random_code" text,
  "notificationToken" text,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "language" varchar default('en'),
  "style_id" integer default('1'),
  "rtl_ltl" integer default('2'),
  "selected_session" integer default('1'),
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "access_status" integer default('1'),
  "school_id" integer default('1'),
  "role_id" integer,
  "is_administrator" varchar not null default('no'),
  "is_registered" integer not null default('0'),
  "device_token" text,
  "stripe_id" varchar,
  "card_brand" varchar,
  "card_last_four" varchar,
  "verified" varchar,
  "trial_ends_at" datetime,
  "is_saas" tinyint(1) not null default('0'),
  "zoom_api_key_of_user" text,
  "zoom_api_serect_of_user" text,
  "wallet_balance" float not null default('0'),
  "staff_bio" text,
  "zoom_account_id" varchar,
  "branch_id" integer,
  foreign key("role_id") references infix_roles("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_categories"(
  "id" integer primary key autoincrement not null,
  "category_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_groups"(
  "id" integer primary key autoincrement not null,
  "group" varchar not null,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_leave_defines"(
  "id" integer primary key autoincrement not null,
  "days" integer,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "role_id" integer,
  "user_id" integer,
  "type_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "total_days" integer default('0'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("type_id") references sm_leave_types("id") on delete cascade on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("role_id") references roles("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_attendances"(
  "id" integer primary key autoincrement not null,
  "attendance_type" varchar,
  "notes" varchar,
  "attendance_date" date,
  "created_at" datetime,
  "updated_at" datetime,
  "student_id" integer,
  "record_id" integer,
  "student_record_id" integer,
  "class_id" integer,
  "section_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "un_academic_id" integer,
  "academic_id" integer default('1'),
  "active_status" integer default('1'),
  "exit_time" varchar,
  "come_from" varchar,
  "entry_time" varchar,
  "source" varchar,
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("student_id") references sm_students("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_student_promotions"(
  "id" integer primary key autoincrement not null,
  "result_status" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "previous_class_id" integer,
  "current_class_id" integer,
  "previous_section_id" integer,
  "current_section_id" integer,
  "previous_session_id" integer,
  "current_session_id" integer,
  "student_id" integer,
  "admission_number" integer,
  "student_info" text,
  "merit_student_info" text,
  "previous_roll_number" integer,
  "current_roll_number" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "previous_shift_id" integer,
  "current_shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("student_id") references sm_students("id") on delete cascade on update no action,
  foreign key("current_session_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("previous_session_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("current_section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("previous_section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("current_class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("previous_class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_temporary_meritlists"(
  "id" integer primary key autoincrement not null,
  "iid" varchar,
  "student_id" varchar,
  "merit_order" float,
  "student_name" varchar,
  "admission_no" varchar,
  "subjects_id_string" varchar,
  "subjects_string" varchar,
  "marks_string" varchar,
  "total_marks" float,
  "average_mark" float,
  "gpa_point" float,
  "result" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "exam_id" integer,
  "class_id" integer,
  "section_id" integer,
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "roll_no" integer,
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("exam_id") references sm_exams("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_subject_attendances"(
  "id" integer primary key autoincrement not null,
  "attendance_type" varchar,
  "notes" varchar,
  "attendance_date" date,
  "notify" tinyint(1) not null default('0'),
  "created_at" datetime,
  "updated_at" datetime,
  "class_id" integer,
  "section_id" integer,
  "subject_id" integer,
  "student_id" integer,
  "student_record_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "active_status" integer default('1'),
  "source" varchar,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("student_record_id") references student_records("id") on delete cascade on update no action,
  foreign key("student_id") references sm_students("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_teacher_upload_contents"(
  "id" integer primary key autoincrement not null,
  "content_title" varchar,
  "content_type" varchar,
  "available_for_admin" integer default('0'),
  "available_for_all_classes" integer not null default('0'),
  "upload_date" date,
  "description" varchar,
  "source_url" varchar,
  "upload_file" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "course_id" integer,
  "parent_course_id" integer,
  "class" integer,
  "section" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "chapter_id" integer,
  "lesson_id" integer,
  "parent_id" integer,
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("class") references sm_classes("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_lessons"(
  "id" integer primary key autoincrement not null,
  "lesson_title" varchar,
  "active_status" integer not null default('1'),
  "class_id" integer,
  "section_id" integer,
  "subject_id" integer,
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_lesson_topics"(
  "id" integer primary key autoincrement not null,
  "lesson_id" integer not null,
  "class_id" integer,
  "section_id" integer,
  "subject_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "active_status" integer not null default('1'),
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "lesson_planners"(
  "id" integer primary key autoincrement not null,
  "day" integer,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "lesson_id" integer,
  "topic_id" integer,
  "lesson_detail_id" integer not null,
  "topic_detail_id" integer,
  "sub_topic" varchar,
  "lecture_youube_link" text,
  "lecture_vedio" text,
  "attachment" text,
  "teaching_method" text,
  "general_objectives" text,
  "previous_knowlege" text,
  "comp_question" text,
  "zoom_setup" text,
  "presentation" text,
  "note" text,
  "lesson_date" date not null,
  "competed_date" date,
  "completed_status" varchar,
  "room_id" integer,
  "teacher_id" integer,
  "class_period_id" integer,
  "subject_id" integer,
  "class_id" integer,
  "section_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "routine_id" integer,
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("room_id") references sm_class_rooms("id") on delete cascade on update no action,
  foreign key("teacher_id") references sm_staffs("id") on delete cascade on update no action,
  foreign key("class_period_id") references sm_class_times("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_staffs"(
  "id" integer primary key autoincrement not null,
  "staff_no" integer,
  "first_name" varchar,
  "last_name" varchar,
  "full_name" varchar,
  "fathers_name" varchar,
  "mothers_name" varchar,
  "date_of_birth" date default('2026-03-12'),
  "date_of_joining" date default('2026-03-12'),
  "email" varchar,
  "mobile" varchar,
  "emergency_mobile" varchar,
  "marital_status" varchar,
  "merital_status" varchar,
  "staff_photo" varchar,
  "current_address" varchar,
  "permanent_address" varchar,
  "qualification" varchar,
  "experience" varchar,
  "epf_no" varchar,
  "basic_salary" double,
  "contract_type" varchar,
  "location" varchar,
  "casual_leave" varchar,
  "medical_leave" varchar,
  "metarnity_leave" varchar,
  "bank_account_name" varchar,
  "bank_account_no" varchar,
  "bank_name" varchar,
  "bank_brach" varchar,
  "facebook_url" varchar,
  "twiteer_url" varchar,
  "linkedin_url" varchar,
  "instragram_url" varchar,
  "joining_letter" varchar,
  "resume" varchar,
  "other_document" varchar,
  "notes" varchar,
  "active_status" integer not null default('1'),
  "show_public" integer not null default('0'),
  "driving_license" varchar,
  "driving_license_ex_date" date,
  "custom_field" text,
  "custom_field_form_name" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "designation_id" integer default('1'),
  "department_id" integer default('1'),
  "user_id" integer default('1'),
  "parent_id" integer,
  "role_id" integer default('1'),
  "previous_role_id" integer,
  "gender_id" integer default('1'),
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "is_saas" integer default('0'),
  "lms_balance" float not null default('0'),
  "staff_bio" text,
  "branch_id" integer,
  foreign key("designation_id") references sm_designations("id") on delete set null on update no action,
  foreign key("department_id") references sm_human_departments("id") on delete set null on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("role_id") references infix_roles("id") on delete set null on update no action,
  foreign key("gender_id") references sm_base_setups("id") on delete set null on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_expert_teachers"(
  "id" integer primary key autoincrement not null,
  "staff_id" integer not null,
  "created_by" integer,
  "updated_by" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default('1'),
  "position" integer not null default('0'),
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_designations"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "is_saas" integer default('0'),
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_human_departments"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "is_saas" integer default('0'),
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("updated_by") references users("id") on delete cascade on update no action,
  foreign key("created_by") references users("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_staff_attendences"(
  "id" integer primary key autoincrement not null,
  "attendence_type" varchar,
  "notes" varchar,
  "attendence_date" date,
  "created_at" datetime,
  "updated_at" datetime,
  "staff_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer,
  "exit_time" varchar,
  "come_from" varchar,
  "entry_time" varchar,
  "source" varchar,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("staff_id") references sm_staffs("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_leave_requests"(
  "id" integer primary key autoincrement not null,
  "apply_date" date,
  "leave_from" date,
  "leave_to" date,
  "reason" text,
  "note" text,
  "file" varchar,
  "approve_status" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "leave_define_id" integer,
  "staff_id" integer,
  "role_id" integer,
  "type_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("type_id") references sm_leave_types("id") on delete cascade on update no action,
  foreign key("role_id") references roles("id") on delete cascade on update no action,
  foreign key("staff_id") references users("id") on delete cascade on update no action,
  foreign key("leave_define_id") references sm_leave_defines("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_room_types"(
  "id" integer primary key autoincrement not null,
  "type" varchar not null,
  "description" text,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_dormitory_lists"(
  "id" integer primary key autoincrement not null,
  "dormitory_name" varchar not null,
  "type" varchar not null,
  "address" varchar,
  "intake" integer,
  "description" text,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_room_lists"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "number_of_bed" integer not null,
  "cost_per_bed" double,
  "description" text,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "dormitory_id" integer default('1'),
  "room_type_id" integer default('1'),
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("room_type_id") references sm_room_types("id") on delete cascade on update no action,
  foreign key("dormitory_id") references sm_dormitory_lists("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_routes"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "far" double,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "un_academic_id" integer,
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_vehicles"(
  "id" integer primary key autoincrement not null,
  "vehicle_no" varchar not null,
  "vehicle_model" varchar not null,
  "made_year" integer,
  "note" text,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "driver_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "un_academic_id" integer,
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_assign_vehicles"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "vehicle_id" integer,
  "route_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "un_academic_id" integer,
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("route_id") references sm_routes("id") on delete cascade on update no action,
  foreign key("vehicle_id") references sm_vehicles("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "content_types"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  "academic_id" integer,
  "school_id" integer default('1'),
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "contents"(
  "id" integer primary key autoincrement not null,
  "file_name" varchar,
  "file_size" integer,
  "content_type_id" integer not null default('1'),
  "youtube_link" varchar,
  "upload_file" varchar,
  "uploaded_by" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "academic_id" integer,
  "school_id" integer default('1'),
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "content_share_lists"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "share_date" date,
  "valid_upto" date,
  "description" text,
  "send_type" varchar,
  "content_ids" text,
  "gr_role_ids" text,
  "ind_user_ids" text,
  "class_id" integer,
  "section_ids" text,
  "url" text,
  "shared_by" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "academic_id" integer,
  "school_id" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "video_uploads"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "description" text,
  "youtube_link" varchar not null,
  "class_id" integer not null,
  "section_id" integer not null,
  "created_by" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  "academic_id" integer,
  "school_id" integer default('1'),
  "un_session_id" integer,
  "un_faculty_id" integer,
  "un_department_id" integer,
  "un_academic_id" integer,
  "un_semester_id" integer,
  "un_semester_label_id" integer,
  "un_section_id" integer,
  "shift_id" integer,
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_book_categories"(
  "id" integer primary key autoincrement not null,
  "category_name" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "library_subjects"(
  "id" integer primary key autoincrement not null,
  "subject_name" varchar not null,
  "sb_category_id" varchar,
  "subject_code" varchar,
  "subject_type" varchar not null default('T'),
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_books"(
  "id" integer primary key autoincrement not null,
  "book_title" varchar,
  "book_number" varchar,
  "isbn_no" varchar,
  "publisher_name" varchar,
  "author_name" varchar,
  "rack_number" varchar,
  "quantity" integer default('0'),
  "book_price" double,
  "post_date" date,
  "details" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "book_subject_id" integer,
  "book_category_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("book_category_id") references sm_book_categories("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_library_members"(
  "id" integer primary key autoincrement not null,
  "member_ud_id" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "member_type" integer,
  "student_staff_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("student_staff_id") references users("id") on delete cascade on update no action,
  foreign key("member_type") references roles("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_book_issues"(
  "id" integer primary key autoincrement not null,
  "quantity" integer,
  "given_date" date,
  "due_date" date,
  "issue_status" varchar,
  "note" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "book_id" integer,
  "member_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("member_id") references users("id") on delete cascade on update no action,
  foreign key("book_id") references sm_books("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "incidents"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "point" double,
  "description" text,
  "school_id" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "assign_incidents"(
  "id" integer primary key autoincrement not null,
  "point" integer,
  "incident_id" integer not null,
  "record_id" integer not null,
  "student_id" integer,
  "added_by" integer not null,
  "academic_id" integer,
  "school_id" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "branch_id" integer,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_homeworks"(
  "id" integer primary key autoincrement not null,
  "homework_date" date,
  "submission_date" date,
  "evaluation_date" date,
  "file" varchar,
  "marks" varchar,
  "description" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "evaluated_by" integer,
  "class_id" integer,
  "record_id" integer,
  "section_id" integer,
  "subject_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "course_id" integer,
  "lesson_id" integer,
  "chapter_id" integer,
  "parent_course_id" integer,
  "student_ids" text,
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("evaluated_by") references users("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_fees_groups"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "type" varchar,
  "start_date" date,
  "end_date" date,
  "due_date" date,
  "description" text,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "un_semester_label_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_fees_types"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "description" text,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "fees_group_id" integer default('1'),
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "un_semester_label_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("fees_group_id") references sm_fees_groups("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_fees_masters"(
  "id" integer primary key autoincrement not null,
  "date" date,
  "amount" double,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "fees_group_id" integer,
  "fees_type_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "class_id" integer,
  "section_id" integer,
  "un_semester_label_id" integer,
  "branch_id" integer,
  foreign key("fees_group_id") references sm_fees_groups("id") on delete cascade on update no action,
  foreign key("fees_type_id") references sm_fees_types("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_fees_discounts"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "code" varchar,
  "type" varchar,
  "amount" double,
  "description" text,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "record_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fm_fees_invoices"(
  "id" integer primary key autoincrement not null,
  "invoice_id" varchar not null,
  "student_id" integer,
  "class_id" integer,
  "create_date" date,
  "due_date" date,
  "payment_status" varchar,
  "payment_method" varchar,
  "bank_id" integer,
  "type" varchar default('fees'),
  "school_id" integer,
  "academic_id" integer,
  "active_status" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "record_id" integer,
  "shift_id" integer,
  "branch_id" integer,
  foreign key("student_id") references sm_students("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fm_fees_transactions"(
  "id" integer primary key autoincrement not null,
  "invoice_number" varchar not null,
  "student_id" integer,
  "user_id" integer,
  "payment_method" varchar not null,
  "bank_id" integer,
  "add_wallet_money" float,
  "payment_note" text,
  "file" text,
  "paid_status" varchar not null,
  "fees_invoice_id" integer,
  "school_id" integer,
  "academic_id" integer,
  "service_charge" float,
  "active_status" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "record_id" integer,
  "total_paid_amount" varchar,
  "branch_id" integer,
  foreign key("fees_invoice_id") references fm_fees_invoices("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_fees_carry_forwards"(
  "id" integer primary key autoincrement not null,
  "balance" double not null,
  "active_status" integer not null default('1'),
  "notes" varchar not null default('Fees Carry Forward'),
  "balance_type" varchar,
  "due_date" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "student_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("student_id") references sm_students("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fees_carry_forward_logs"(
  "id" integer primary key autoincrement not null,
  "student_record_id" integer not null,
  "note" text not null,
  "amount" float not null,
  "amount_type" varchar not null,
  "created_by" integer,
  "updated_by" integer,
  "type" varchar not null,
  "date" datetime not null,
  "school_id" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_fees_payments"(
  "id" integer primary key autoincrement not null,
  "discount_month" integer,
  "discount_amount" double,
  "fine" double,
  "amount" double,
  "payment_date" date,
  "payment_mode" varchar,
  "note" text,
  "slip" varchar,
  "fine_title" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "assign_id" integer,
  "bank_id" integer,
  "fees_discount_id" integer,
  "fees_type_id" integer,
  "record_id" integer,
  "student_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "direct_fees_installment_assign_id" integer,
  "installment_payment_id" integer,
  "branch_id" integer,
  foreign key("assign_id") references sm_fees_assigns("id") on delete cascade on update no action,
  foreign key("bank_id") references sm_bank_accounts("id") on delete cascade on update no action,
  foreign key("fees_discount_id") references sm_fees_discounts("id") on delete cascade on update no action,
  foreign key("fees_type_id") references sm_fees_types("id") on delete cascade on update no action,
  foreign key("student_id") references sm_students("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_bank_payment_slips"(
  "id" integer primary key autoincrement not null,
  "date" date not null,
  "amount" float,
  "slip" varchar,
  "note" text,
  "bank_id" integer,
  "approve_status" integer not null default('0'),
  "payment_mode" varchar not null,
  "reason" text,
  "fees_discount_id" integer,
  "fees_type_id" integer,
  "record_id" integer,
  "student_id" integer,
  "class_id" integer,
  "assign_id" integer,
  "section_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "child_payment_id" integer,
  "installment_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "active_status" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("school_id") references sm_schools("id") on delete restrict on update no action,
  foreign key("student_id") references sm_students("id") on delete cascade on update no action,
  foreign key("fees_discount_id") references sm_fees_discounts("id") on delete restrict on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_item_categories"(
  "id" integer primary key autoincrement not null,
  "category_name" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default('1'),
  "un_academic_id" integer,
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_items"(
  "id" integer primary key autoincrement not null,
  "item_name" varchar,
  "total_in_stock" float,
  "description" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "item_category_id" integer,
  "school_id" integer default('1'),
  "un_academic_id" integer,
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("item_category_id") references sm_item_categories("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_item_stores"(
  "id" integer primary key autoincrement not null,
  "store_name" varchar,
  "store_no" varchar,
  "description" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "school_id" integer default('1'),
  "un_academic_id" integer,
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_suppliers"(
  "id" integer primary key autoincrement not null,
  "company_name" varchar,
  "company_address" varchar,
  "contact_person_name" varchar,
  "contact_person_mobile" varchar,
  "contact_person_email" varchar,
  "cotact_person_address" varchar,
  "description" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_chart_of_accounts"(
  "id" integer primary key autoincrement not null,
  "head" varchar,
  "type" varchar,
  "active_status" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fm_fees_groups"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "branch_id" integer,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "fm_fees_types"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "description" text,
  "fees_group_id" integer default('1'),
  "type" varchar not null default('fees'),
  "course_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "branch_id" integer,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_exam_types"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default('1'),
  "title" varchar not null,
  "is_average" integer not null default('0'),
  "percentage" float,
  "average_mark" float not null default('0'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "parent_id" integer default('0'),
  "percantage" float default('100'),
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_exams"(
  "id" integer primary key autoincrement not null,
  "parent_id" integer default('0'),
  "exam_mark" float,
  "pass_mark" float,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "exam_type_id" integer,
  "class_id" integer,
  "section_id" integer,
  "subject_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("exam_type_id") references sm_exam_types("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_exam_setups"(
  "id" integer primary key autoincrement not null,
  "exam_title" varchar,
  "exam_mark" float,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "exam_id" integer,
  "class_id" integer,
  "subject_id" integer,
  "section_id" integer,
  "exam_term_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("exam_term_id") references sm_exam_types("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("exam_id") references sm_exams("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_exam_schedules"(
  "id" integer primary key autoincrement not null,
  "date" date,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "exam_period_id" integer,
  "room_id" integer,
  "subject_id" integer,
  "exam_term_id" integer,
  "exam_id" integer,
  "class_id" integer,
  "section_id" integer,
  "start_time" time,
  "end_time" time,
  "teacher_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("teacher_id") references sm_staffs("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("exam_id") references sm_exams("id") on delete cascade on update no action,
  foreign key("exam_term_id") references sm_exam_types("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("exam_period_id") references sm_class_times("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_exam_attendances"(
  "id" integer primary key autoincrement not null,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "subject_id" integer,
  "exam_id" integer,
  "class_id" integer,
  "section_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("exam_id") references sm_exams("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_question_groups"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_question_banks"(
  "id" integer primary key autoincrement not null,
  "type" varchar not null,
  "question" text,
  "marks" integer,
  "trueFalse" varchar,
  "suitable_words" text,
  "number_of_option" varchar,
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "q_group_id" integer,
  "class_id" integer,
  "section_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("q_group_id") references sm_question_groups("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_online_exams"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "date" date,
  "start_time" varchar,
  "end_time" varchar,
  "end_date_time" varchar,
  "percentage" integer,
  "instruction" text,
  "status" integer,
  "is_taken" integer default('0'),
  "is_closed" integer default('0'),
  "is_waiting" integer default('0'),
  "is_running" integer default('0'),
  "auto_mark" integer default('0'),
  "active_status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "class_id" integer,
  "section_id" integer,
  "subject_id" integer,
  "created_by" integer default('1'),
  "updated_by" integer default('1'),
  "school_id" integer default('1'),
  "academic_id" integer default('1'),
  "shift_id" integer,
  "branch_id" integer,
  foreign key("academic_id") references sm_academic_years("id") on delete cascade on update no action,
  foreign key("school_id") references sm_schools("id") on delete cascade on update no action,
  foreign key("subject_id") references sm_subjects("id") on delete cascade on update no action,
  foreign key("section_id") references sm_sections("id") on delete cascade on update no action,
  foreign key("class_id") references sm_classes("id") on delete cascade on update no action,
  foreign key("branch_id") references "branches"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sm_schools"(
  "id" integer primary key autoincrement not null,
  "school_name" varchar,
  "created_by" integer not null default('1'),
  "updated_by" integer not null default('1'),
  "email" varchar,
  "domain" varchar not null default('school'),
  "address" text,
  "phone" varchar,
  "school_code" varchar,
  "is_email_verified" tinyint(1) not null default('0'),
  "starting_date" date,
  "ending_date" date,
  "package_id" integer,
  "plan_type" varchar,
  "region" varchar,
  "contact_type" varchar,
  "active_status" integer not null default('1'),
  "is_enabled" varchar not null default('yes'),
  "created_at" datetime,
  "updated_at" datetime,
  "custom_domain" varchar,
  "custom_field" text,
  "custom_field_form_name" varchar
);

INSERT INTO migrations VALUES(1,'2014_11_01_000001_create_sm_schools_table',1);
INSERT INTO migrations VALUES(2,'2014_11_01_000021_create_sm_academic_years_table',1);
INSERT INTO migrations VALUES(3,'2014_11_12_100000_create_password_resets_table',1);
INSERT INTO migrations VALUES(4,'2014_11_13_000003_create_statuses_table',1);
INSERT INTO migrations VALUES(5,'2014_12_01_000001_create_infix_roles_table',1);
INSERT INTO migrations VALUES(6,'2014_12_01_000002_create_roles_table',1);
INSERT INTO migrations VALUES(7,'2014_12_01_000003_create_users_table',1);
INSERT INTO migrations VALUES(8,'2014_12_01_000004_create_sm_base_groups_table',1);
INSERT INTO migrations VALUES(9,'2014_12_01_000005_create_sm_base_setups_table',1);
INSERT INTO migrations VALUES(10,'2014_12_01_000006_create_sm_classes_table',1);
INSERT INTO migrations VALUES(11,'2014_12_01_000007_create_sm_sections_table',1);
INSERT INTO migrations VALUES(12,'2014_12_01_000008_create_sm_class_sections_table',1);
INSERT INTO migrations VALUES(13,'2014_12_01_000009_create_sm_subjects_table',1);
INSERT INTO migrations VALUES(14,'2014_12_01_000010_create_sm_visitors_table',1);
INSERT INTO migrations VALUES(15,'2014_12_01_000011_create_sm_fees_groups_table',1);
INSERT INTO migrations VALUES(16,'2014_12_01_000012_create_sm_fees_types_table',1);
INSERT INTO migrations VALUES(17,'2014_12_01_000013_create_sm_fees_discounts_table',1);
INSERT INTO migrations VALUES(18,'2014_12_01_000014_create_sm_income_heads_table',1);
INSERT INTO migrations VALUES(19,'2014_12_01_000015_create_sm_chart_of_accounts_table',1);
INSERT INTO migrations VALUES(20,'2014_12_01_000016_create_sm_bank_accounts_table',1);
INSERT INTO migrations VALUES(21,'2014_12_01_000017_create_sm_payment_gateway_settings_table',1);
INSERT INTO migrations VALUES(22,'2014_12_01_000018_create_sm_payment_methhods_table',1);
INSERT INTO migrations VALUES(23,'2014_12_01_000019_create_sm_add_incomes_table',1);
INSERT INTO migrations VALUES(24,'2014_12_01_000020_create_sm_student_groups_table',1);
INSERT INTO migrations VALUES(25,'2014_12_01_000022_create_sm_sessions_table',1);
INSERT INTO migrations VALUES(26,'2014_12_01_000023_create_sm_dormitory_lists_table',1);
INSERT INTO migrations VALUES(27,'2014_12_01_000024_create_sm_room_types_table',1);
INSERT INTO migrations VALUES(28,'2014_12_01_000025_create_sm_room_lists_table',1);
INSERT INTO migrations VALUES(29,'2014_12_01_000026_create_sm_designations_table',1);
INSERT INTO migrations VALUES(30,'2014_12_01_000027_create_sm_human_departments_table',1);
INSERT INTO migrations VALUES(31,'2014_12_01_000028_create_sm_staffs_table',1);
INSERT INTO migrations VALUES(32,'2014_12_01_000029_create_sm_vehicles_table',1);
INSERT INTO migrations VALUES(33,'2014_12_01_000030_create_sm_routes_table',1);
INSERT INTO migrations VALUES(34,'2014_12_01_000031_create_sm_instructions_table',1);
INSERT INTO migrations VALUES(35,'2014_12_01_000032_create_sm_question_levels_table',1);
INSERT INTO migrations VALUES(36,'2014_12_01_000033_create_sm_question_groups_table',1);
INSERT INTO migrations VALUES(37,'2014_12_01_000034_create_sm_question_banks_table',1);
INSERT INTO migrations VALUES(38,'2014_12_01_000035_create_sm_online_exams_table',1);
INSERT INTO migrations VALUES(39,'2014_12_01_000036_create_sm_exam_types_table',1);
INSERT INTO migrations VALUES(40,'2014_12_01_000037_create_sm_marks_grades_table',1);
INSERT INTO migrations VALUES(41,'2014_12_01_000038_create_sm_exams_table',1);
INSERT INTO migrations VALUES(42,'2014_12_01_000039_create_sm_hourly_rates_table',1);
INSERT INTO migrations VALUES(43,'2014_12_01_000040_create_sm_leave_types_table',1);
INSERT INTO migrations VALUES(44,'2014_12_01_000041_create_sm_leave_defines_table',1);
INSERT INTO migrations VALUES(45,'2014_12_01_000042_create_sm_leave_requests_table',1);
INSERT INTO migrations VALUES(46,'2014_12_01_000043_create_sm_expense_heads_table',1);
INSERT INTO migrations VALUES(47,'2014_12_01_000044_create_sm_add_expenses_table',1);
INSERT INTO migrations VALUES(48,'2014_12_01_000045_create_sm_fees_masters_table',1);
INSERT INTO migrations VALUES(49,'2014_12_01_000046_create_sm_setup_admins_table',1);
INSERT INTO migrations VALUES(50,'2014_12_01_000047_create_sm_complaints_table',1);
INSERT INTO migrations VALUES(51,'2014_12_01_000048_create_sm_postal_receives_table',1);
INSERT INTO migrations VALUES(52,'2014_12_01_000049_create_sm_postal_dispatches_table',1);
INSERT INTO migrations VALUES(53,'2014_12_01_000050_create_sm_phone_call_logs_table',1);
INSERT INTO migrations VALUES(54,'2014_12_01_000051_create_sm_student_categories_table',1);
INSERT INTO migrations VALUES(55,'2014_12_01_000052_create_sm_parents_table',1);
INSERT INTO migrations VALUES(56,'2014_12_01_000054_create_sm_students_table',1);
INSERT INTO migrations VALUES(57,'2014_12_01_000055_create_sm_student_attendances_table',1);
INSERT INTO migrations VALUES(58,'2014_12_01_000056_create_sm_student_promotions_table',1);
INSERT INTO migrations VALUES(59,'2014_12_01_000057_create_sm_staff_attendences_table',1);
INSERT INTO migrations VALUES(60,'2014_12_01_000058_create_sm_student_homeworks_table',1);
INSERT INTO migrations VALUES(61,'2014_12_01_000059_create_sm_teacher_upload_contents_table',1);
INSERT INTO migrations VALUES(62,'2014_12_01_000060_create_sm_hr_salary_templates_table',1);
INSERT INTO migrations VALUES(63,'2014_12_01_000061_create_sm_hr_payroll_generates_table',1);
INSERT INTO migrations VALUES(64,'2014_12_01_000062_create_sm_exam_marks_registers_table',1);
INSERT INTO migrations VALUES(65,'2014_12_01_000063_create_sm_marks_send_sms_table',1);
INSERT INTO migrations VALUES(66,'2014_12_01_000064_create_sm_class_routines_table',1);
INSERT INTO migrations VALUES(67,'2014_12_01_000064_create_sm_class_times_table',1);
INSERT INTO migrations VALUES(68,'2014_12_01_000065_create_languages_table',1);
INSERT INTO migrations VALUES(69,'2014_12_01_000065_create_sm_assign_subjects_table',1);
INSERT INTO migrations VALUES(70,'2014_12_01_000066_create_sm_modules_table',1);
INSERT INTO migrations VALUES(71,'2014_12_01_000067_create_sm_languages_table',1);
INSERT INTO migrations VALUES(72,'2014_12_01_000068_create_sm_date_formats_table',1);
INSERT INTO migrations VALUES(73,'2014_12_01_000069_create_sm_news_categories_table',1);
INSERT INTO migrations VALUES(74,'2016_06_01_000001_create_oauth_auth_codes_table',1);
INSERT INTO migrations VALUES(75,'2016_06_01_000002_create_oauth_access_tokens_table',1);
INSERT INTO migrations VALUES(76,'2016_06_01_000003_create_oauth_refresh_tokens_table',1);
INSERT INTO migrations VALUES(77,'2016_06_01_000004_create_oauth_clients_table',1);
INSERT INTO migrations VALUES(78,'2016_06_01_000005_create_oauth_personal_access_clients_table',1);
INSERT INTO migrations VALUES(79,'2018_01_04_105604_create_student_records_table',1);
INSERT INTO migrations VALUES(80,'2018_12_04_050352_create_sm_notice_boards_table',1);
INSERT INTO migrations VALUES(81,'2018_12_04_051648_create_sm_send_messages_table',1);
INSERT INTO migrations VALUES(82,'2018_12_04_060828_create_sm_events_table',1);
INSERT INTO migrations VALUES(83,'2018_12_04_062330_create_sm_holidays_table',1);
INSERT INTO migrations VALUES(84,'2018_12_04_062714_create_sm_book_categories_table',1);
INSERT INTO migrations VALUES(85,'2018_12_04_063012_create_sm_books_table',1);
INSERT INTO migrations VALUES(86,'2018_12_04_075138_create_sm_library_members_table',1);
INSERT INTO migrations VALUES(87,'2018_12_04_075911_create_sm_book_issues_table',1);
INSERT INTO migrations VALUES(88,'2018_12_13_093741_create_sm_fees_carry_forwards_table',1);
INSERT INTO migrations VALUES(89,'2018_12_17_104146_create_sm_fees_assigns_table',1);
INSERT INTO migrations VALUES(90,'2018_12_17_111529_create_sm_hr_payroll_earn_deducs_table',1);
INSERT INTO migrations VALUES(91,'2018_12_20_064256_create_sm_fees_assign_discounts_table',1);
INSERT INTO migrations VALUES(92,'2018_12_20_090159_create_sm_fees_payments_table',1);
INSERT INTO migrations VALUES(93,'2018_12_24_052328_create_sm_homeworks_table',1);
INSERT INTO migrations VALUES(94,'2018_12_26_084518_create_sm_homework_students_table',1);
INSERT INTO migrations VALUES(95,'2018_12_28_054159_create_sm_upload_contents_table',1);
INSERT INTO migrations VALUES(96,'2018_12_28_075918_create_sm_content_types_table',1);
INSERT INTO migrations VALUES(97,'2018_12_28_122146_create_sm_assign_class_teachers_table',1);
INSERT INTO migrations VALUES(98,'2018_12_28_122205_create_sm_class_teachers_table',1);
INSERT INTO migrations VALUES(99,'2018_12_31_112538_create_sm_exam_schedules_table',1);
INSERT INTO migrations VALUES(100,'2018_12_31_112600_create_sm_exam_schedule_subjects_table',1);
INSERT INTO migrations VALUES(101,'2019_01_02_061148_create_sm_marks_registers_table',1);
INSERT INTO migrations VALUES(102,'2019_01_02_061238_create_sm_marks_register_children_table',1);
INSERT INTO migrations VALUES(103,'2019_01_03_105718_create_sm_class_rooms_table',1);
INSERT INTO migrations VALUES(104,'2019_01_06_060144_create_sm_seat_plans_table',1);
INSERT INTO migrations VALUES(105,'2019_01_06_060213_create_sm_seat_plan_children_table',1);
INSERT INTO migrations VALUES(106,'2019_01_07_132108_create_sm_exam_attendances_table',1);
INSERT INTO migrations VALUES(107,'2019_01_07_132220_create_sm_exam_attendance_children_table',1);
INSERT INTO migrations VALUES(108,'2019_01_09_101421_create_sm_online_exam_questions_table',1);
INSERT INTO migrations VALUES(109,'2019_01_09_101533_create_sm_online_exam_question_mu_options_table',1);
INSERT INTO migrations VALUES(110,'2019_01_10_050231_create_sm_item_categories_table',1);
INSERT INTO migrations VALUES(111,'2019_01_10_050645_create_sm_items_table',1);
INSERT INTO migrations VALUES(112,'2019_01_10_054622_create_sm_item_stores_table',1);
INSERT INTO migrations VALUES(113,'2019_01_10_070859_create_sm_suppliers_table',1);
INSERT INTO migrations VALUES(114,'2019_01_10_112518_create_sm_item_receives_table',1);
INSERT INTO migrations VALUES(115,'2019_01_12_104449_create_sm_item_receive_children_table',1);
INSERT INTO migrations VALUES(116,'2019_01_13_113100_create_sm_online_exam_marks_table',1);
INSERT INTO migrations VALUES(117,'2019_01_14_061003_create_sm_assign_vehicles_table',1);
INSERT INTO migrations VALUES(118,'2019_01_16_065238_create_sm_module_links_table',1);
INSERT INTO migrations VALUES(119,'2019_01_19_094137_create_sm_inventory_payments_table',1);
INSERT INTO migrations VALUES(120,'2019_01_21_063031_create_sm_student_excel_formats_table',1);
INSERT INTO migrations VALUES(121,'2019_01_21_131008_create_sm_item_sells_table',1);
INSERT INTO migrations VALUES(122,'2019_01_22_104243_create_sm_item_sell_children_table',1);
INSERT INTO migrations VALUES(123,'2019_01_23_121931_create_sm_item_issues_table',1);
INSERT INTO migrations VALUES(124,'2019_01_26_054046_create_sm_sms_gateways_table',1);
INSERT INTO migrations VALUES(125,'2019_01_30_122524_create_sm_student_documents_table',1);
INSERT INTO migrations VALUES(126,'2019_01_31_052142_create_sm_student_timelines_table',1);
INSERT INTO migrations VALUES(127,'2019_01_31_101401_create_sm_question_bank_mu_options_table',1);
INSERT INTO migrations VALUES(128,'2019_02_02_043028_create_sm_online_exam_question_assigns_table',1);
INSERT INTO migrations VALUES(129,'2019_02_02_112647_create_sm_student_take_online_exams_table',1);
INSERT INTO migrations VALUES(130,'2019_02_02_112719_create_sm_student_take_online_exam_questions_table',1);
INSERT INTO migrations VALUES(131,'2019_02_02_115540_create_sm_student_take_onln_ex_ques_options_table',1);
INSERT INTO migrations VALUES(132,'2019_02_09_050800_create_sm_email_sms_logs_table',1);
INSERT INTO migrations VALUES(133,'2019_02_10_125119_create_sm_general_settings_table',1);
INSERT INTO migrations VALUES(134,'2019_02_11_093834_create_sm_user_logs_table',1);
INSERT INTO migrations VALUES(135,'2019_02_12_064024_create_sm_email_settings_table',1);
INSERT INTO migrations VALUES(136,'2019_02_16_082050_create_sm_student_certificates_table',1);
INSERT INTO migrations VALUES(137,'2019_02_17_124203_create_sm_student_id_cards_table',1);
INSERT INTO migrations VALUES(138,'2019_02_24_124115_create_sm_to_dos_table',1);
INSERT INTO migrations VALUES(139,'2019_03_13_075602_create_sm_admission_queries_table',1);
INSERT INTO migrations VALUES(140,'2019_03_14_075324_create_sm_admission_query_followups_table',1);
INSERT INTO migrations VALUES(141,'2019_04_04_124508_create_sm_backups_table',1);
INSERT INTO migrations VALUES(142,'2019_04_10_054237_create_sm_temporary_meritlists',1);
INSERT INTO migrations VALUES(143,'2019_04_13_062212_create_sm_exam_setups_table',1);
INSERT INTO migrations VALUES(144,'2019_04_15_055616_create_sm_mark_stores_table',1);
INSERT INTO migrations VALUES(145,'2019_04_17_101844_create_sm_result_stores_table',1);
INSERT INTO migrations VALUES(146,'2019_04_21_071626_create_sm_class_routine_updates_table',1);
INSERT INTO migrations VALUES(147,'2019_04_23_051315_create_sm_weekends_table',1);
INSERT INTO migrations VALUES(148,'2019_04_25_164649_create_sm_countries_table',1);
INSERT INTO migrations VALUES(149,'2019_04_27_121353_create_sm_language_phrases_table',1);
INSERT INTO migrations VALUES(150,'2019_04_28_074534_create_sm_notifications_table',1);
INSERT INTO migrations VALUES(151,'2019_04_30_181622_create_continents_table',1);
INSERT INTO migrations VALUES(152,'2019_04_30_181730_create_countries_table',1);
INSERT INTO migrations VALUES(153,'2019_05_07_103627_create_sm_currencies_table',1);
INSERT INTO migrations VALUES(154,'2019_05_26_095459_create_sm_news_table',1);
INSERT INTO migrations VALUES(155,'2019_05_27_103844_create_sm_testimonials_table',1);
INSERT INTO migrations VALUES(156,'2019_06_01_113053_create_sm_contact_pages_table',1);
INSERT INTO migrations VALUES(157,'2019_06_01_165107_create_sm_contact_messages_table',1);
INSERT INTO migrations VALUES(158,'2019_06_10_155041_create_sm_product_purchases_table',1);
INSERT INTO migrations VALUES(159,'2019_06_11_112109_create_sm_about_pages_table',1);
INSERT INTO migrations VALUES(160,'2019_06_12_143430_create_sm_courses_table',1);
INSERT INTO migrations VALUES(161,'2019_07_17_182142_create_sm_dashboard_settings_table',1);
INSERT INTO migrations VALUES(162,'2019_07_18_141858_create_sm_background_settings_table',1);
INSERT INTO migrations VALUES(163,'2019_07_20_151115_create_sm_custom_links_table',1);
INSERT INTO migrations VALUES(164,'2019_07_20_183407_create_sm_frontend_persmissions_table',1);
INSERT INTO migrations VALUES(165,'2019_07_21_110814_create_sm_home_page_settings_table',1);
INSERT INTO migrations VALUES(166,'2019_09_01_171428_create_sm_system_versions_table',1);
INSERT INTO migrations VALUES(167,'2019_09_06_113029_create_continets_table',1);
INSERT INTO migrations VALUES(168,'2019_09_09_142112_create_sm_styles_table',1);
INSERT INTO migrations VALUES(169,'2019_09_25_183656_create_sm_module_permissions_table',1);
INSERT INTO migrations VALUES(170,'2019_09_26_115256_create_sm_module_permission_assigns_table',1);
INSERT INTO migrations VALUES(171,'2019_10_16_160104_create_sm_time_zones_table',1);
INSERT INTO migrations VALUES(172,'2019_11_27_120508_create_sm_student_attendance_imports_table',1);
INSERT INTO migrations VALUES(173,'2019_11_27_181351_create_sm_staff_attendance_imports_table',1);
INSERT INTO migrations VALUES(174,'2020_01_01_100001_create_infix_bio_settings_table',1);
INSERT INTO migrations VALUES(175,'2020_01_11_141636_create_infix_module_infos_table',1);
INSERT INTO migrations VALUES(176,'2020_01_23_125935_create_sm_optional_subject_assigns_table',1);
INSERT INTO migrations VALUES(177,'2020_01_26_112215_create_sm_class_optional_subject',1);
INSERT INTO migrations VALUES(178,'2020_01_28_103859_create_sm_news_pages_table',1);
INSERT INTO migrations VALUES(179,'2020_01_28_121210_create_sm_course_pages_table',1);
INSERT INTO migrations VALUES(180,'2020_01_29_110503_create_sm_subject_attendances_table',1);
INSERT INTO migrations VALUES(181,'2020_02_05_105739_create_custom_result_settings_table',1);
INSERT INTO migrations VALUES(182,'2020_02_05_131307_create_sm_custom_temporary_results_table',1);
INSERT INTO migrations VALUES(183,'2020_02_25_100003_create_device_log_table',1);
INSERT INTO migrations VALUES(184,'2020_03_09_153421_create_sm_add_ons_table',1);
INSERT INTO migrations VALUES(185,'2020_03_14_123955_create_sms_templates_table',1);
INSERT INTO migrations VALUES(186,'2020_03_21_200226_create_sm_social_media_icons_table',1);
INSERT INTO migrations VALUES(187,'2020_03_29_102518_create_sm_upload_homework_contents_table',1);
INSERT INTO migrations VALUES(188,'2020_04_01_060324_create_jobs_table',1);
INSERT INTO migrations VALUES(189,'2020_04_12_125728_create_infix_permission_assigns_table',1);
INSERT INTO migrations VALUES(190,'2020_04_16_064434_create_infix_module_student_parent_infos_table',1);
INSERT INTO migrations VALUES(191,'2020_04_23_094037_create_billing__information_table',1);
INSERT INTO migrations VALUES(192,'2020_04_23_094213_create_additional_services_table',1);
INSERT INTO migrations VALUES(193,'2020_04_23_094317_create_sm_saas_packages_table',1);
INSERT INTO migrations VALUES(194,'2020_04_23_094444_create_subscriptions_table',1);
INSERT INTO migrations VALUES(195,'2020_04_23_094820_create_categories_table',1);
INSERT INTO migrations VALUES(196,'2020_04_23_094821_create_priorities_table',1);
INSERT INTO migrations VALUES(197,'2020_04_23_094822_create_tickets_table',1);
INSERT INTO migrations VALUES(198,'2020_04_23_094823_create_comments_table',1);
INSERT INTO migrations VALUES(199,'2020_04_23_095304_create_infix_invoices_table',1);
INSERT INTO migrations VALUES(200,'2020_04_23_095428_create_infix_invoice_categories_table',1);
INSERT INTO migrations VALUES(201,'2020_04_23_095524_create_infix_invoice_settings_table',1);
INSERT INTO migrations VALUES(202,'2020_04_23_095647_create_infix_invoice_category_links_table',1);
INSERT INTO migrations VALUES(203,'2020_04_23_095752_create_infix_invoice_products_table',1);
INSERT INTO migrations VALUES(204,'2020_04_23_095913_create_verify_users_table',1);
INSERT INTO migrations VALUES(205,'2020_04_23_133604_create_sm_administrator_notices_table',1);
INSERT INTO migrations VALUES(206,'2020_04_25_073212_create_saas_school_module_permission_assigns_table',1);
INSERT INTO migrations VALUES(207,'2020_04_27_061914_create_sm_student_registrations_table',1);
INSERT INTO migrations VALUES(208,'2020_04_27_061915_create_sm_registration_settings_table',1);
INSERT INTO migrations VALUES(209,'2020_06_10_060128_create_zoom_meetings_table',1);
INSERT INTO migrations VALUES(210,'2020_06_10_134834_create_zoom_meeting_users_table',1);
INSERT INTO migrations VALUES(211,'2020_06_10_193309_create_infix_module_managers_table',1);
INSERT INTO migrations VALUES(212,'2020_06_16_051034_create_zoom_settings_table',1);
INSERT INTO migrations VALUES(213,'2020_06_17_091643_create_student_bulk_temporaries_table',1);
INSERT INTO migrations VALUES(214,'2020_06_18_084210_create_zoom_virtual_class_table',1);
INSERT INTO migrations VALUES(215,'2020_06_18_084255_create_zoom_virtual_class_teachers_table',1);
INSERT INTO migrations VALUES(216,'2020_06_22_120034_create_student_attendance_bulks_table',1);
INSERT INTO migrations VALUES(217,'2020_06_23_065442_create_sm_package_plans_table',1);
INSERT INTO migrations VALUES(218,'2020_06_23_110330_create_sm_package_plan_features_table',1);
INSERT INTO migrations VALUES(219,'2020_06_24_101619_create_sm_saas_payment_gateway_settings_table',1);
INSERT INTO migrations VALUES(220,'2020_06_24_101620_create_sm_saas_payment_methods_table',1);
INSERT INTO migrations VALUES(221,'2020_06_24_120736_create_sm_saas_subscription_settings_table',1);
INSERT INTO migrations VALUES(222,'2020_06_26_144311_create_library_subjects_table',1);
INSERT INTO migrations VALUES(223,'2020_06_27_081604_create_sm_subscription_payments_table',1);
INSERT INTO migrations VALUES(224,'2020_07_05_125524_create_razor_pays_table',1);
INSERT INTO migrations VALUES(225,'2020_07_14_052504_create_sm_bank_payment_slips_table',1);
INSERT INTO migrations VALUES(226,'2020_08_21_053415_create_check_classes_table',1);
INSERT INTO migrations VALUES(227,'2020_10_27_071255_create_sm_leave_deduction_infos_table',1);
INSERT INTO migrations VALUES(228,'2020_11_16_065239_create_sm_role_permissions_table',1);
INSERT INTO migrations VALUES(229,'2020_11_18_113808_create_sm_lessons_table',1);
INSERT INTO migrations VALUES(230,'2020_11_18_121616_create_sm_lesson_details_table',1);
INSERT INTO migrations VALUES(231,'2020_11_20_044018_create_sm_lesson_topics_table',1);
INSERT INTO migrations VALUES(232,'2020_11_20_045211_create_sm_lesson_topic_details_table',1);
INSERT INTO migrations VALUES(233,'2020_11_24_123643_create_bbb_settings_table',1);
INSERT INTO migrations VALUES(234,'2020_11_24_123936_create_bbb_meetings_table',1);
INSERT INTO migrations VALUES(235,'2020_11_24_124009_create_bbb_meeting_users_table',1);
INSERT INTO migrations VALUES(236,'2020_12_07_101416_create_online_exam_student_answer_markings_table',1);
INSERT INTO migrations VALUES(237,'2020_12_10_095530_create_lesson_planners_table',1);
INSERT INTO migrations VALUES(238,'2020_12_27_091444_bbb_virtual_class_teachers',1);
INSERT INTO migrations VALUES(239,'2020_12_27_094638_create_bbb_virtual_classes_table',1);
INSERT INTO migrations VALUES(240,'2021_01_18_121007_create_sm_bank_statements_table',1);
INSERT INTO migrations VALUES(241,'2021_01_27_103347_create_sm_exam_settings_table',1);
INSERT INTO migrations VALUES(242,'2021_02_01_042422_create_sm_amount_transfers_table',1);
INSERT INTO migrations VALUES(243,'2021_02_10_110920_create_absent_notification_time_setups_table',1);
INSERT INTO migrations VALUES(244,'2021_02_15_111736_create_invitations_table',1);
INSERT INTO migrations VALUES(245,'2021_02_15_130414_create_conversations_table',1);
INSERT INTO migrations VALUES(246,'2021_02_17_165101_create_block_users_table',1);
INSERT INTO migrations VALUES(247,'2021_03_01_131441_create_notifications_table',1);
INSERT INTO migrations VALUES(248,'2021_03_03_112535_create_groups_table',1);
INSERT INTO migrations VALUES(249,'2021_03_03_112734_create_group_users_table',1);
INSERT INTO migrations VALUES(250,'2021_03_03_112908_create_group_message_recipients_table',1);
INSERT INTO migrations VALUES(251,'2021_03_15_112756_create_sm_pages_table',1);
INSERT INTO migrations VALUES(252,'2021_03_18_071730_create_sm_header_menu_managers_table',1);
INSERT INTO migrations VALUES(253,'2021_03_18_172321_create_group_message_removes_table',1);
INSERT INTO migrations VALUES(254,'2021_03_22_121237_create_sm_course_categories_table',1);
INSERT INTO migrations VALUES(255,'2021_03_29_055746_create_jitsi_virtual_classes_table',1);
INSERT INTO migrations VALUES(256,'2021_03_29_060954_jitsi_virtual_class_teachers',1);
INSERT INTO migrations VALUES(257,'2021_03_29_070403_create_jitsi_settings_table',1);
INSERT INTO migrations VALUES(258,'2021_03_29_124902_create_jitsi_meetings_table',1);
INSERT INTO migrations VALUES(259,'2021_03_31_053231_create_invitation_types_table',1);
INSERT INTO migrations VALUES(260,'2021_03_31_114808_create_jitsi_meeting_users_table',1);
INSERT INTO migrations VALUES(261,'2021_04_15_063841_create_infix_question_groups_table',1);
INSERT INTO migrations VALUES(262,'2021_04_15_063842_create_infix_question_banks_table',1);
INSERT INTO migrations VALUES(263,'2021_04_15_063843_create_infix_online_exams_table',1);
INSERT INTO migrations VALUES(264,'2021_04_15_063844_create_infix_question_bank_mu_options_table',1);
INSERT INTO migrations VALUES(265,'2021_04_15_063845_create_infix_student_take_online_exams_table',1);
INSERT INTO migrations VALUES(266,'2021_04_15_063846_create_infix_student_take_online_exam_questions_table',1);
INSERT INTO migrations VALUES(267,'2021_04_15_063849_create_infix_online_exam_student_answer_markings_table',1);
INSERT INTO migrations VALUES(268,'2021_04_15_064031_create_infix_student_take_onln_ex_ques_options_table',1);
INSERT INTO migrations VALUES(269,'2021_04_15_085410_create_infix_online_exam_question_assigns_table',1);
INSERT INTO migrations VALUES(270,'2021_04_17_032749_create_infix_online_exam_settings_table',1);
INSERT INTO migrations VALUES(271,'2021_04_17_053404_create_infix_online_exam_marks_table',1);
INSERT INTO migrations VALUES(272,'2021_05_02_041410_create_infix_question_bank_bulk_temporaries_table',1);
INSERT INTO migrations VALUES(273,'2021_05_12_092535_create_sm_custom_fields_table',1);
INSERT INTO migrations VALUES(274,'2021_05_18_044006_create_infix_written_exams_table',1);
INSERT INTO migrations VALUES(275,'2021_05_20_110115_create_infix_student_take_written_exams_table',1);
INSERT INTO migrations VALUES(276,'2021_06_06_070142_create_invoice_settings_table',1);
INSERT INTO migrations VALUES(277,'2021_06_26_120238_create_xendit_payment_setting_table',1);
INSERT INTO migrations VALUES(278,'2021_06_30_044055_zoom_update',1);
INSERT INTO migrations VALUES(279,'2021_07_20_110254_create_fm_fees_groups_table',1);
INSERT INTO migrations VALUES(280,'2021_07_20_110718_create_fm_fees_types_table',1);
INSERT INTO migrations VALUES(281,'2021_07_26_045723_create_fm_fees_invoice_settings_table',1);
INSERT INTO migrations VALUES(282,'2021_07_26_070244_create_student_academic_histories_table',1);
INSERT INTO migrations VALUES(283,'2021_07_29_050922_create_failed_jobs_table',1);
INSERT INTO migrations VALUES(284,'2021_07_29_120251_create_fm_fees_invoices_table',1);
INSERT INTO migrations VALUES(285,'2021_07_31_072347_create_fm_fees_invoice_chields_table',1);
INSERT INTO migrations VALUES(286,'2021_08_03_035307_create_fm_fees_weavers_table',1);
INSERT INTO migrations VALUES(287,'2021_08_03_094121_create_fm_fees_transactions_table',1);
INSERT INTO migrations VALUES(288,'2021_08_04_040918_create_wm__wallet_settings_table',1);
INSERT INTO migrations VALUES(289,'2021_08_24_072815_create_courses_table',1);
INSERT INTO migrations VALUES(290,'2021_08_24_072833_create_course_categories_table',1);
INSERT INTO migrations VALUES(291,'2021_08_24_073006_create_course_lessons_table',1);
INSERT INTO migrations VALUES(292,'2021_08_24_073043_create_course_chapters_table',1);
INSERT INTO migrations VALUES(293,'2021_08_24_073210_create_course_quizzes_table',1);
INSERT INTO migrations VALUES(294,'2021_08_25_092306_create_wallet_transactions_table',1);
INSERT INTO migrations VALUES(295,'2021_08_26_094606_create_course_teachers_table',1);
INSERT INTO migrations VALUES(296,'2021_08_31_070309_create_transcations_table',1);
INSERT INTO migrations VALUES(297,'2021_09_01_132144_add_custom_domain_to_school_table',1);
INSERT INTO migrations VALUES(298,'2021_09_02_125424_create_course_reviews_table',1);
INSERT INTO migrations VALUES(299,'2021_09_02_125713_create_course_questions_table',1);
INSERT INTO migrations VALUES(300,'2021_09_06_123838_create_course_settings_table',1);
INSERT INTO migrations VALUES(301,'2021_09_13_113431_create_sm_student_fields_table',1);
INSERT INTO migrations VALUES(302,'2021_09_15_063949_add_online_student_custom_field_to_sm_registration_settings',1);
INSERT INTO migrations VALUES(303,'2021_09_16_091205_create_course_purchase_logs_table',1);
INSERT INTO migrations VALUES(304,'2021_09_23_085729_create_khalti_payments_table',1);
INSERT INTO migrations VALUES(305,'2021_09_27_060646_create_raudhahpays_table',1);
INSERT INTO migrations VALUES(306,'2021_09_27_093014_create_raudhahpay_collections_table',1);
INSERT INTO migrations VALUES(307,'2021_09_27_115658_create_raudhahpay_bills_table',1);
INSERT INTO migrations VALUES(308,'2021_09_29_105752_create_saas_settings_table',1);
INSERT INTO migrations VALUES(309,'2021_10_06_093746_create_course_comments_table',1);
INSERT INTO migrations VALUES(310,'2021_10_18_122358_create_course_comment_replies_table',1);
INSERT INTO migrations VALUES(311,'2021_10_23_123651_create_school_modules_table',1);
INSERT INTO migrations VALUES(312,'2021_11_02_062835_create_fm_fees_transaction_chields_table',1);
INSERT INTO migrations VALUES(313,'2021_11_16_062629_base_setup_update',1);
INSERT INTO migrations VALUES(314,'2021_11_22_113702_create_quiz_question_groups_table',1);
INSERT INTO migrations VALUES(315,'2021_11_22_115515_create_quiz_question_banks_table',1);
INSERT INTO migrations VALUES(316,'2021_11_23_043626_create_quiz_question_bank_mu_options_table',1);
INSERT INTO migrations VALUES(317,'2021_11_23_102833_create_quizzes_table',1);
INSERT INTO migrations VALUES(318,'2021_11_24_065920_create_quiz_exam_question_assigns_table',1);
INSERT INTO migrations VALUES(319,'2021_11_27_060045_create_lms_hosts_table',1);
INSERT INTO migrations VALUES(320,'2021_11_27_064810_create_course_files_table',1);
INSERT INTO migrations VALUES(321,'2021_11_30_124224_create_vimeo_settings_table',1);
INSERT INTO migrations VALUES(322,'2021_12_01_061938_create_vimeo_videos_table',1);
INSERT INTO migrations VALUES(323,'2021_12_06_064456_create_quiz_participents_table',1);
INSERT INTO migrations VALUES(324,'2021_12_11_063853_create_quiz_answers_table',1);
INSERT INTO migrations VALUES(325,'2021_12_11_090545_create_quiz_student_answer_markings_table',1);
INSERT INTO migrations VALUES(326,'2021_12_11_121519_create_student_take_quiz_quesitons_table',1);
INSERT INTO migrations VALUES(327,'2021_12_11_121932_create_student_take_quiz_que_options_table',1);
INSERT INTO migrations VALUES(328,'2021_12_13_044231_create_lesson_completes_table',1);
INSERT INTO migrations VALUES(329,'2021_12_15_134114_create_sm_student_registration_fields_table',1);
INSERT INTO migrations VALUES(330,'2021_12_29_040913_create_lesson_plan_topics_table',1);
INSERT INTO migrations VALUES(331,'2021_12_30_084056_create_lms_certificate_generates_table',1);
INSERT INTO migrations VALUES(332,'2022_01_05_111544_lead_fees_table',1);
INSERT INTO migrations VALUES(333,'2022_01_10_081128_multiple_course_data_migration',1);
INSERT INTO migrations VALUES(334,'2022_01_11_065535_create_admit_card_settings_table',1);
INSERT INTO migrations VALUES(335,'2022_01_11_065552_create_seat_plan_settings_table',1);
INSERT INTO migrations VALUES(336,'2022_01_11_065604_create_admit_cards_table',1);
INSERT INTO migrations VALUES(337,'2022_01_11_065617_create_seat_plans_table',1);
INSERT INTO migrations VALUES(338,'2022_01_12_094218_create_fees_xtra_table',1);
INSERT INTO migrations VALUES(339,'2022_01_26_044752_create_sm_staff_registration_fields_table',1);
INSERT INTO migrations VALUES(340,'2022_01_26_122051_create_mercado_pago_table',1);
INSERT INTO migrations VALUES(341,'2022_01_28_075807_create_fees_invoice_settings_table',1);
INSERT INTO migrations VALUES(342,'2022_02_01_060747_create_onilne_exam_customizes_table',1);
INSERT INTO migrations VALUES(343,'2022_02_03_104230_lmsCustomization',1);
INSERT INTO migrations VALUES(344,'2022_02_04_042713_add_lesson_id_to_infix_online_exams',1);
INSERT INTO migrations VALUES(345,'2022_02_04_064016_add_chapter_id_to_infix_online_exams',1);
INSERT INTO migrations VALUES(346,'2022_02_20_065504_study_material_homework',1);
INSERT INTO migrations VALUES(347,'2022_03_02_070412_add_lesson_plan_subtopic_to_general_settings_table',1);
INSERT INTO migrations VALUES(348,'2022_03_10_141706_multiple_course_migration_fixing_migration',1);
INSERT INTO migrations VALUES(349,'2022_04_06_035808_create_direct_fees_installments_table',1);
INSERT INTO migrations VALUES(350,'2022_04_06_112758_create_direct_fees_installment_assigns_table',1);
INSERT INTO migrations VALUES(351,'2022_04_30_044521_create_dire_fees_installment_child_payments_table',1);
INSERT INTO migrations VALUES(352,'2022_04_30_104150_create_fees_invoices_table',1);
INSERT INTO migrations VALUES(353,'2022_05_15_065010_add_marcado_pago_to_modules_table',1);
INSERT INTO migrations VALUES(354,'2022_05_15_071522_create_direct_fees_settings_table',1);
INSERT INTO migrations VALUES(355,'2022_05_15_072844_create_direct_fees_reminders_table',1);
INSERT INTO migrations VALUES(356,'2022_05_27_101907_add_exit_time_to_attendances_table',1);
INSERT INTO migrations VALUES(357,'2022_06_18_134636_chat_migrations',1);
INSERT INTO migrations VALUES(358,'2022_08_22_095151_create_parent_courses_table',1);
INSERT INTO migrations VALUES(359,'2022_08_22_110225_create_add_parent_course_ids_table',1);
INSERT INTO migrations VALUES(360,'2022_08_30_062414_create_exam_merit_positions_table',1);
INSERT INTO migrations VALUES(361,'2022_10_05_131155_create_gmeet_settings_table',1);
INSERT INTO migrations VALUES(362,'2022_10_06_084040_create_gmeet_virtual_classes_table',1);
INSERT INTO migrations VALUES(363,'2022_10_06_113934_create_gmeet_virtual_class_teachers_table',1);
INSERT INTO migrations VALUES(364,'2022_10_07_121536_create_gmeet_virtual_meetings_table',1);
INSERT INTO migrations VALUES(365,'2022_10_07_121730_create_gmeet_virtual_meeting_users_table',1);
INSERT INTO migrations VALUES(366,'2022_10_12_122432_create_google_accounts_table',1);
INSERT INTO migrations VALUES(367,'2022_10_14_023532_add_gmeet_day_to_sm_weekends_table',1);
INSERT INTO migrations VALUES(368,'2022_10_24_050844_create_optionbuilder_settings_table',1);
INSERT INTO migrations VALUES(369,'2022_10_24_133230_create_custom_sms_settings_table',1);
INSERT INTO migrations VALUES(370,'2022_11_10_092640_add_service_charge_table',1);
INSERT INTO migrations VALUES(371,'2022_11_18_053137_create_colors_table',1);
INSERT INTO migrations VALUES(372,'2022_11_18_095219_create_themes_table',1);
INSERT INTO migrations VALUES(373,'2022_11_18_095321_create_color_theme_table',1);
INSERT INTO migrations VALUES(374,'2022_11_21_060020_create_exam_step_skips_table',1);
INSERT INTO migrations VALUES(375,'2022_11_30_050447_create_fees_installment_credits_table',1);
INSERT INTO migrations VALUES(376,'2022_12_08_142623_create_staff_import_bulk_temporaries_table',1);
INSERT INTO migrations VALUES(377,'2022_12_13_043756_create_graduates_table',1);
INSERT INTO migrations VALUES(378,'2022_12_19_060543_create_student_record_temporaries_table',1);
INSERT INTO migrations VALUES(379,'2022_12_29_072511_create_pages_table',1);
INSERT INTO migrations VALUES(380,'2022_12_30_033620_drop_foreign_column',1);
INSERT INTO migrations VALUES(381,'2023_01_06_053233_create_add_parent_id_for_academics_modules',1);
INSERT INTO migrations VALUES(382,'2023_01_20_052540_add_column_online_exam_route_permission_table',1);
INSERT INTO migrations VALUES(383,'2023_01_23_123534_create_matching_type_question_assigns_table',1);
INSERT INTO migrations VALUES(384,'2023_01_27_111653_add_colum_to_infix_module_student_parent_infos',1);
INSERT INTO migrations VALUES(385,'2023_03_10_123138_create_all_exam_wise_positions_table',1);
INSERT INTO migrations VALUES(386,'2023_03_13_071949_create_add_xtra_column_sm_temporary_meritlists_table',1);
INSERT INTO migrations VALUES(387,'2023_03_20_122806_create_comment_multi_attachments_table',1);
INSERT INTO migrations VALUES(388,'2023_03_20_124918_create_ticket_multi_attachments_table',1);
INSERT INTO migrations VALUES(389,'2023_03_21_103045_create_permission_sections_table',1);
INSERT INTO migrations VALUES(390,'2023_03_22_131748_create_sidebars_table',1);
INSERT INTO migrations VALUES(391,'2023_03_26_035701_create_permissions_table',1);
INSERT INTO migrations VALUES(392,'2023_03_26_035702_add_is_alumni_to_permissions_table',1);
INSERT INTO migrations VALUES(393,'2023_03_26_043548_create_assign_permissions_table',1);
INSERT INTO migrations VALUES(394,'2023_03_29_051529_remove_xendit_payment_from_default_module',1);
INSERT INTO migrations VALUES(395,'2023_04_19_092512_add_column_bbb_route_permission_table',1);
INSERT INTO migrations VALUES(396,'2023_04_19_093912_add_column_zoom_route_permission_table',1);
INSERT INTO migrations VALUES(397,'2023_04_19_094059_add_column_jitsi_route_permission_table',1);
INSERT INTO migrations VALUES(398,'2023_04_19_094451_add_column_gmeet_route_permission_table',1);
INSERT INTO migrations VALUES(399,'2023_04_19_103942_add_column_parent_registration_route_permission_table',1);
INSERT INTO migrations VALUES(400,'2023_04_19_110027_add_column_biometrics_route_permission_table',1);
INSERT INTO migrations VALUES(401,'2023_04_19_141634_add_column_lms_route_permission_table',1);
INSERT INTO migrations VALUES(402,'2023_05_08_142343_add_route_to_sass_settings_table',1);
INSERT INTO migrations VALUES(403,'2023_05_09_053931_add_version_7_0_0_migration',1);
INSERT INTO migrations VALUES(404,'2023_05_10_064204_add_role_id_sidebars_table',1);
INSERT INTO migrations VALUES(405,'2023_05_12_102406_add_account_id_zoom_settings_table',1);
INSERT INTO migrations VALUES(406,'2023_05_19_041042_create_sm_exam_signatures_table',1);
INSERT INTO migrations VALUES(407,'2023_05_19_053931_add_version_7_0_2_migration',1);
INSERT INTO migrations VALUES(408,'2023_05_23_045054_create_user_otp_codes_table',1);
INSERT INTO migrations VALUES(409,'2023_05_23_073801_create_two_factor_settings_table',1);
INSERT INTO migrations VALUES(410,'2023_05_24_081000_create_maintenance_settings_table',1);
INSERT INTO migrations VALUES(411,'2023_06_02_053931_add_version_7_0_3_migration',1);
INSERT INTO migrations VALUES(412,'2023_06_05_061123_create_sm_notification_settings_table',1);
INSERT INTO migrations VALUES(413,'2023_06_07_000001_create_pulse_tables',1);
INSERT INTO migrations VALUES(414,'2023_06_07_113530_create_fees_carry_forward_settings_table',1);
INSERT INTO migrations VALUES(415,'2023_06_08_120933_create_due_fees_login_prevents_table',1);
INSERT INTO migrations VALUES(416,'2023_06_09_110746_add_behaviour_record_sidebarmenu',1);
INSERT INTO migrations VALUES(417,'2023_06_12_085040_create_payroll_payments_table',1);
INSERT INTO migrations VALUES(418,'2023_06_14_034342_create_fees_carry_forward_logs_table',1);
INSERT INTO migrations VALUES(419,'2023_06_15_060654_create_incidents_table',1);
INSERT INTO migrations VALUES(420,'2023_06_16_025623_create_assign_incidents_table',1);
INSERT INTO migrations VALUES(421,'2023_06_27_095706_create_sm_exam_types_extension_table',1);
INSERT INTO migrations VALUES(422,'2023_07_06_090158_create_phone_pay_gateway_setting_table',1);
INSERT INTO migrations VALUES(423,'2023_07_06_115723_create_in_app_live_class_settings_table',1);
INSERT INTO migrations VALUES(424,'2023_07_07_071941_create_in_app_live_classes_table',1);
INSERT INTO migrations VALUES(425,'2023_07_07_121144_create_version_histories_table',1);
INSERT INTO migrations VALUES(426,'2023_07_10_054811_create_behaviour_record_settings_table',1);
INSERT INTO migrations VALUES(427,'2023_07_10_054849_create_assign_incident_comments_table',1);
INSERT INTO migrations VALUES(428,'2023_07_11_094614_create_teacher_evaluations_table',1);
INSERT INTO migrations VALUES(429,'2023_07_11_094734_create_teacher_evaluation_settings_table',1);
INSERT INTO migrations VALUES(430,'2023_07_12_082820_create_in_app_live_class_menus_table',1);
INSERT INTO migrations VALUES(431,'2023_07_14_100422_create_in_app_live_meetings_table',1);
INSERT INTO migrations VALUES(432,'2023_07_19_061034_create_calendar_menus_table',1);
INSERT INTO migrations VALUES(433,'2023_07_21_081453_add_teacher_evaluation_sidebarmenu',1);
INSERT INTO migrations VALUES(434,'2023_07_25_120800_create_frontend_exam_results_table',1);
INSERT INTO migrations VALUES(435,'2023_07_27_102731_create_sm_calendar_settings_table',1);
INSERT INTO migrations VALUES(436,'2023_08_03_031848_create_sm_class_exam_routine_pages_table',1);
INSERT INTO migrations VALUES(437,'2023_08_25_062823_create_update_7.1.1_to_7.2.0_table',1);
INSERT INTO migrations VALUES(438,'2023_08_31_031255_create_content_types_table',1);
INSERT INTO migrations VALUES(439,'2023_08_31_102733_create_contents_table',1);
INSERT INTO migrations VALUES(440,'2023_09_04_065348_create_content_share_lists_table',1);
INSERT INTO migrations VALUES(441,'2023_09_04_085741_create_video_uploads_table',1);
INSERT INTO migrations VALUES(442,'2023_09_11_0628897_create_update_7.2.0_to_7.2.1_table',1);
INSERT INTO migrations VALUES(443,'2023_09_12_080612_create_regions_table',1);
INSERT INTO migrations VALUES(444,'2023_09_12_093653_create_saas_clients_custom_table',1);
INSERT INTO migrations VALUES(445,'2023_09_19_082020_add_download_center_sidebarmenu',1);
INSERT INTO migrations VALUES(446,'2023_09_22_1346345_update_v721_to_v8_migrations',1);
INSERT INTO migrations VALUES(447,'2023_09_26_094106_create_home_sliders_table',1);
INSERT INTO migrations VALUES(448,'2023_09_27_065756_create_sm_expert_teachers_table',1);
INSERT INTO migrations VALUES(449,'2023_09_28_054606_create_sm_photo_galleries_table',1);
INSERT INTO migrations VALUES(450,'2023_09_29_052332_create_sm_video_galleries_table',1);
INSERT INTO migrations VALUES(451,'2023_09_30_040648_create_front_results_table',1);
INSERT INTO migrations VALUES(452,'2023_10_03_054024_create_front_exam_routines_table',1);
INSERT INTO migrations VALUES(453,'2023_10_03_054032_create_front_class_routines_table',1);
INSERT INTO migrations VALUES(454,'2023_10_05_064926_create_certificate_types_table',1);
INSERT INTO migrations VALUES(455,'2023_10_05_071914_create_certificate_templates_table',1);
INSERT INTO migrations VALUES(456,'2023_10_05_071937_create_certificate_settings_table',1);
INSERT INTO migrations VALUES(457,'2023_10_05_073446_AddCertificatePermissions',1);
INSERT INTO migrations VALUES(458,'2023_10_06_024910_create_front_academic_calendars_table',1);
INSERT INTO migrations VALUES(459,'2023_10_06_043223_create_certificate_template_designs_table',1);
INSERT INTO migrations VALUES(460,'2023_10_11_053735_create_sm_news_comments_table',1);
INSERT INTO migrations VALUES(461,'2023_10_15_100024_create_certificate_records_table',1);
INSERT INTO migrations VALUES(462,'2023_10_19_100644_create_ai_templates_table',1);
INSERT INTO migrations VALUES(463,'2023_10_19_100658_create_ai_template_contents_table',1);
INSERT INTO migrations VALUES(464,'2023_10_19_100713_create_ai_generated_contents_table',1);
INSERT INTO migrations VALUES(465,'2023_10_20_030735_create_ai_content_settings_table',1);
INSERT INTO migrations VALUES(466,'2023_10_23_092339_create_agents_table',1);
INSERT INTO migrations VALUES(467,'2023_10_23_092357_create_agent_times_table',1);
INSERT INTO migrations VALUES(468,'2023_10_23_092411_create_messages_table',1);
INSERT INTO migrations VALUES(469,'2023_10_23_092425_create_settings_table',1);
INSERT INTO migrations VALUES(470,'2023_10_26_045838_add_ai_content_sidebarmenu',1);
INSERT INTO migrations VALUES(471,'2023_10_26_055348_add_whatsapp_support_sidebarmenu',1);
INSERT INTO migrations VALUES(472,'2023_11_02_044840_UpdateDatabaseAsInfixLms',1);
INSERT INTO migrations VALUES(473,'2023_11_03_110411_edulia_demo_pages',1);
INSERT INTO migrations VALUES(474,'2023_11_07_103530_create_course_levels_table',1);
INSERT INTO migrations VALUES(475,'2023_11_09_104528_update_course_table_as_infix_lms',1);
INSERT INTO migrations VALUES(476,'2023_11_22_034222_update_sm_student_certificates_table',1);
INSERT INTO migrations VALUES(477,'2023_12_04_083919_create_speech_sliders_table',1);
INSERT INTO migrations VALUES(478,'2023_12_04_122708_update_v8.0.1_to_8.1.0',1);
INSERT INTO migrations VALUES(479,'2023_12_07_121858_create_plugins_table',1);
INSERT INTO migrations VALUES(480,'2023_12_11_063723_create_sm_donors_table',1);
INSERT INTO migrations VALUES(481,'2023_12_13_024824_create_sm_form_downloads_table',1);
INSERT INTO migrations VALUES(482,'2023_12_21_040359_add_is_graduate_to_student_records',1);
INSERT INTO migrations VALUES(483,'2024_01_30_051708_update_v8.1.0_to_8.1.1',1);
INSERT INTO migrations VALUES(484,'2024_03_05_081452_update_v8.1.1_to_8.1.2',1);
INSERT INTO migrations VALUES(485,'2024_03_15_081452_update_v8.1.2_to_8.2.0',1);
INSERT INTO migrations VALUES(486,'2024_03_15_081453_update_v8.1.2_to_8.2.0_stable',1);
INSERT INTO migrations VALUES(487,'2024_03_20_081452_update_v8.2.0_to_8.2.1',1);
INSERT INTO migrations VALUES(488,'2024_04_19_141634_add_column_lms_routes_permissions_table',1);
INSERT INTO migrations VALUES(489,'2024_07_02_081453_update_v8.2.0_to_8.2.0_stable',1);
INSERT INTO migrations VALUES(490,'2024_07_15_081453_update_v8.2.2',1);
INSERT INTO migrations VALUES(491,'2024_07_17_081453_update_for_saas',1);
INSERT INTO migrations VALUES(492,'2024_08_25_104528_update_as_course_table',1);
INSERT INTO migrations VALUES(493,'2024_09_05_184427_add_role_based_sidebar_column_to_general_settings_table',1);
INSERT INTO migrations VALUES(494,'2024_09_05_191641_add_role_id_column_to_permissions_table',1);
INSERT INTO migrations VALUES(495,'2024_09_13_081453_update_v8.2.4',1);
INSERT INTO migrations VALUES(496,'2024_09_13_093012_add_custom_menus_to_permission_table',1);
INSERT INTO migrations VALUES(497,'2024_09_19_081453_update_v8.2.5',1);
INSERT INTO migrations VALUES(498,'2024_10_07_061404_add_qr_code_attendance_sidebar',1);
INSERT INTO migrations VALUES(499,'2024_10_07_094324_add_qr_code_module_on_database',1);
INSERT INTO migrations VALUES(500,'2024_10_07_120635_create_auto_submission_settings_table',1);
INSERT INTO migrations VALUES(501,'2024_10_08_120009_add_qr_code_settings_column_on_general_settings_table',1);
INSERT INTO migrations VALUES(502,'2024_10_17_120149_add_photo_and_signature_column_on_sm_student_id_cards_table',1);
INSERT INTO migrations VALUES(503,'2024_10_22_081453_update_colors_data',1);
INSERT INTO migrations VALUES(504,'2024_10_29_112352_add_time_column_to_student_and_staffs_attendance_table',1);
INSERT INTO migrations VALUES(505,'2024_10_30_081453_update_8.2.6_demo_content',1);
INSERT INTO migrations VALUES(506,'2024_11_02_081221_modify_attendance_table_for_qr_code_attendace',1);
INSERT INTO migrations VALUES(507,'2024_11_06_094451_add_column_gmeet_route_parent_permission_redo_table',1);
INSERT INTO migrations VALUES(508,'2024_11_11_111111_add_lms_permission_table',1);
INSERT INTO migrations VALUES(509,'2024_11_24_094112_add_cbse_exam',1);
INSERT INTO migrations VALUES(510,'2024_11_24_094224_create_terms_table',1);
INSERT INTO migrations VALUES(511,'2024_11_24_112736_create_observation_parameter',1);
INSERT INTO migrations VALUES(512,'2024_11_25_043735_create_observation_table',1);
INSERT INTO migrations VALUES(513,'2024_11_25_044127_create_parameters_of_observation_table',1);
INSERT INTO migrations VALUES(514,'2024_11_27_063632_create_marksheet_templates_table',1);
INSERT INTO migrations VALUES(515,'2024_11_27_064318_create_marksheet_sections_table',1);
INSERT INTO migrations VALUES(516,'2024_12_17_072416_add_staff_department_designation_column_on_sm_student_id_cards_table',1);
INSERT INTO migrations VALUES(517,'2024_12_24_110403_create_cbse_exam_assignments_table',1);
INSERT INTO migrations VALUES(518,'2024_12_24_110650_create_cbse_exam_assignment_attribute',1);
INSERT INTO migrations VALUES(519,'2024_12_27_033444_update_8.2.7',1);
INSERT INTO migrations VALUES(520,'2024_12_27_033445_update_8.2.8',1);
INSERT INTO migrations VALUES(521,'2025_01_23_075305_version_8.2.8_update_migration',1);
INSERT INTO migrations VALUES(522,'2025_02_07_090832_create_assign_observations_table',1);
INSERT INTO migrations VALUES(523,'2025_02_18_033910_optimize_sm_student_id_cards_table',1);
INSERT INTO migrations VALUES(524,'2025_02_18_034756_optimize_sm_sm_student_certificates_table',1);
INSERT INTO migrations VALUES(525,'2025_02_18_093303_optimize_classes_sm_sections_table',1);
INSERT INTO migrations VALUES(526,'2025_02_20_103101_add_config_fields_on_general_settings_table_for_bio_metrics_module',1);
INSERT INTO migrations VALUES(527,'2025_02_21_052430_add_class_id_section__id_on_biomatric_settings_table',1);
INSERT INTO migrations VALUES(528,'2025_02_27_043253_modify_sm_student_categories_table',1);
INSERT INTO migrations VALUES(529,'2025_02_27_043531_modify_sm_students_table',1);
INSERT INTO migrations VALUES(530,'2025_03_04_084527_modify_fm_fees_groups_table',1);
INSERT INTO migrations VALUES(531,'2025_03_04_084919_modify_fm_fees_types_table',1);
INSERT INTO migrations VALUES(532,'2025_03_15_083114_create_cbse_exam_grades_table',1);
INSERT INTO migrations VALUES(533,'2025_03_15_083310_create_cbse_exam_grade_marks_table',1);
INSERT INTO migrations VALUES(534,'2025_03_17_022127_create_cbse_exams_table',1);
INSERT INTO migrations VALUES(535,'2025_03_17_025312_create_cbse_exam_sections_table',1);
INSERT INTO migrations VALUES(536,'2025_03_21_060643_change_custome_marksheet_report_menu',1);
INSERT INTO migrations VALUES(537,'2025_03_23_065153_add_univesiry_columns_on_jitsi_virtual_classes_table',1);
INSERT INTO migrations VALUES(538,'2025_03_24_040512_add_university_columns_on_gmeet_virtual_classes_table',1);
INSERT INTO migrations VALUES(539,'2025_03_25_070707_add_university_module_columns_on_video_uploads_table',1);
INSERT INTO migrations VALUES(540,'2025_03_28_112934_add_ssl_commers_on_modules_table',1);
INSERT INTO migrations VALUES(541,'2025_04_09_075026_set_nullable_course_id_on_lesson_completes_table',1);
INSERT INTO migrations VALUES(542,'2025_04_29_130721_add_default_sm_menus_data',1);
INSERT INTO migrations VALUES(543,'2025_04_29_130722_update_v9.0.0_demo_content',1);
INSERT INTO migrations VALUES(544,'2025_05_15_093132_change_grade_column_type_in_all_exam_wise_positions_table',1);
INSERT INTO migrations VALUES(545,'2025_05_19_065023_create_shifts_table',1);
INSERT INTO migrations VALUES(546,'2025_05_19_065604_database_update_for_shift',1);
INSERT INTO migrations VALUES(547,'2025_05_22_064521_update_general_settings',1);
INSERT INTO migrations VALUES(548,'2025_06_02_060230_add_shift_enable_to_sm_general_settings_table',1);
INSERT INTO migrations VALUES(549,'2025_06_04_130832_make_nullable_payment_note_column_onfm_fees_transactions_table',1);
INSERT INTO migrations VALUES(550,'2025_06_11_145721_add_academic_id_to_shifts_table',1);
INSERT INTO migrations VALUES(551,'2025_06_11_151334_insert_shift_menus_into_sm_menus_table',1);
INSERT INTO migrations VALUES(552,'2025_06_13_043829_shift_add_jitsi_virtual_classes',1);
INSERT INTO migrations VALUES(553,'2025_06_17_105642_add_permission_to_view_student',1);
INSERT INTO migrations VALUES(554,'2025_06_17_142932_add_shift_id_to_courses_table',1);
INSERT INTO migrations VALUES(555,'2025_06_18_024924_add_permission_to_view_content',1);
INSERT INTO migrations VALUES(556,'2025_06_18_080258_remove_sidebar_manager_from_teanchs_and_student',1);
INSERT INTO migrations VALUES(557,'2025_06_18_083317_update_permissions',1);
INSERT INTO migrations VALUES(558,'2025_06_20_114558_add_column_gmeet',1);
INSERT INTO migrations VALUES(559,'2025_06_26_045554_course_id_nullable_on_lession_completes_table',1);
INSERT INTO migrations VALUES(560,'2025_06_26_072156_create_add_shift_id_content_list_table',1);
INSERT INTO migrations VALUES(561,'2025_06_26_110249_create_add_shift_id_graduate_table',1);
INSERT INTO migrations VALUES(562,'2025_06_27_044859_create_add_always_enable_table',1);
INSERT INTO migrations VALUES(563,'2025_06_30_072643_add_menus_migration',1);
INSERT INTO migrations VALUES(564,'2025_07_04_060420_crate_mark_registration_import',1);
INSERT INTO migrations VALUES(565,'2025_07_04_152107_add_shift_id_on_qr_code_attendance_table',1);
INSERT INTO migrations VALUES(566,'2025_07_09_084044_update_school_wise_menus_table',1);
INSERT INTO migrations VALUES(567,'2025_07_11_110838_create_assign_subject_import',1);
INSERT INTO migrations VALUES(568,'2025_07_21_071948_create_zoom_university_column_add',1);
INSERT INTO migrations VALUES(569,'2025_07_21_105841_create_in_app_live_university_column_add',1);
INSERT INTO migrations VALUES(570,'2025_07_22_113511_create_bbb_university_column_add',1);
INSERT INTO migrations VALUES(571,'2025_07_25_110157_create_front_header_menu_add_table',1);
INSERT INTO migrations VALUES(572,'2025_08_05_111936_create_main_id_add_table',1);
INSERT INTO migrations VALUES(573,'2025_08_11_101934_add_carry_forword_due_day_on_general_settings_table',1);
INSERT INTO migrations VALUES(574,'2025_08_12_130311_add_delivery_mode_column_on_course_column',1);
INSERT INTO migrations VALUES(575,'2025_08_15_101657_add_custom_url_field_on_zoom_virtual_classes_table',1);
INSERT INTO migrations VALUES(576,'2025_08_27_140538_add_main_id_column_on_lms_tables',1);
INSERT INTO migrations VALUES(577,'2025_09_08_133621_mark_sheet_report_fix',1);
INSERT INTO migrations VALUES(578,'2025_10_27_204253_create_cbse_exam_students_table',1);
INSERT INTO migrations VALUES(579,'2025_10_28_134311_create_cbse_exam_subjects',1);
INSERT INTO migrations VALUES(580,'2025_10_29_063545_create_cbse_exam_subject_marks_table',1);
INSERT INTO migrations VALUES(581,'2025_10_29_065134_create_cbse_exam_marks_table',1);
INSERT INTO migrations VALUES(582,'2025_10_29_143746_create_tempalte_links_table',1);
INSERT INTO migrations VALUES(583,'2025_10_29_143758_create_tempalte_link_terms_table',1);
INSERT INTO migrations VALUES(584,'2025_10_29_143807_create_tempalte_link_exam_table',1);
INSERT INTO migrations VALUES(585,'2025_10_29_155539_create_permission_on_permissions_table',1);
INSERT INTO migrations VALUES(586,'2025_10_29_155634_create_cbse_exam_student_ranks_table',1);
INSERT INTO migrations VALUES(587,'2025_10_29_165924_add_menus_on_sidebar',1);
INSERT INTO migrations VALUES(588,'2025_11_02_194958_create_cbse_exam_results_table',1);
INSERT INTO migrations VALUES(589,'2025_11_12_135945_add_cbse_exam_module',1);
INSERT INTO migrations VALUES(590,'2025_11_20_111142_add_source_coulumn_on_sm_subject_attendances_table',1);
INSERT INTO migrations VALUES(591,'2025_11_24_051951_add_marksheet_send_email_template',1);
INSERT INTO migrations VALUES(592,'2025_12_02_184017_make_grades_table_remarks_column_nullable',1);
INSERT INTO migrations VALUES(593,'2025_12_03_144600_create_observation_marks_table',1);
INSERT INTO migrations VALUES(594,'2025_12_04_092612_create_template_wise_results_table',1);
INSERT INTO migrations VALUES(595,'2025_12_04_101543_create_cbse_exam_subject_wise_ranks',1);
INSERT INTO migrations VALUES(596,'2025_12_05_092014_add_subject_wise_menu_on_sm_menus_table',1);
INSERT INTO migrations VALUES(597,'2025_12_08_132408_change_amount_column_type',1);
INSERT INTO migrations VALUES(598,'2025_12_10_150845_add_note_column_on_cbse_exam_marks_table',1);
INSERT INTO migrations VALUES(599,'2025_12_10_170540_add_publish_result_column_on_exams_table',1);
INSERT INTO migrations VALUES(600,'2025_12_11_102045_add_menus_for_student_and_parent_panel',1);
INSERT INTO migrations VALUES(601,'2025_12_15_045619_create_add_student_staff_list_view_general_settings_table',1);
INSERT INTO migrations VALUES(602,'2025_12_22_091304_change_amount_column_sm_amount_transfer',1);
INSERT INTO migrations VALUES(603,'2025_12_30_120606_create_branches_table',1);
INSERT INTO migrations VALUES(604,'2025_12_30_143924_add_branch_module',1);
INSERT INTO migrations VALUES(605,'2025_12_31_084719_create_branch_id_add_table',1);
INSERT INTO migrations VALUES(606,'2026_01_16_083505_add_arabic_language_on_sm_languages_table',1);
INSERT INTO migrations VALUES(607,'2026_01_20_113910_shift_menu_fix_on_sm_menus_table',1);
INSERT INTO migrations VALUES(608,'2026_01_27_093947_create_branch_id_add_table',1);
INSERT INTO migrations VALUES(609,'2026_01_27_100254_add_demo_menus_for_online_exam',1);
INSERT INTO migrations VALUES(610,'2026_01_28_051448_create_branch_id_add_table',1);
INSERT INTO migrations VALUES(611,'2026_01_29_124602_create_branch_id_add_table',1);
INSERT INTO migrations VALUES(612,'2026_01_30_085739_create_branch_id_add_table',1);
INSERT INTO migrations VALUES(613,'2026_01_30_091308_create_branch_id_add_table',1);
INSERT INTO migrations VALUES(614,'2026_02_02_045655_create_branch_id_add_table',1);
INSERT INTO migrations VALUES(615,'2026_02_03_065946_create_branch_id_add_table',1);
INSERT INTO migrations VALUES(616,'2026_02_05_074259_create_branch_id_add_table',1);
INSERT INTO migrations VALUES(617,'2026_02_16_161826_create_branch_menus_add_table',1);
INSERT INTO migrations VALUES(618,'2026_02_18_122441_create_branch_id_add_table',1);
INSERT INTO migrations VALUES(619,'2026_02_23_060958_create_branch_id_add_table',1);
INSERT INTO migrations VALUES(620,'2026_02_25_094548_change_institute_region_nullable',1);
INSERT INTO migrations VALUES(621,'2026_02_26_043457_add_manage_subscription_on_saas_settings_table',1);
