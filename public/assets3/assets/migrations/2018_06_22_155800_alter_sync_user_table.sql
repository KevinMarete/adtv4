TRUNCATE sync_user//
ALTER TABLE sync_user DROP id//
ALTER TABLE sync_user ADD user_id int(11) NOT NULL AFTER status, CHANGE profile_id profile_id varchar(50) NOT NULL AFTER user_id//
ALTER TABLE sync_user ADD organization_id varchar(50) COLLATE utf8_general_ci NOT NULL//
ALTER TABLE sync_user ADD UNIQUE user_id_profile_id (user_id, profile_id), DROP INDEX username//
ALTER TABLE sync_user CHANGE organization_id organization_id text COLLATE 'utf8_general_ci' NOT NULL AFTER profile_id//