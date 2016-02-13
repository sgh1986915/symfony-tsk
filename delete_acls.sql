DELIMITER //
DROP PROCEDURE IF EXISTS delete_acls;
CREATE PROCEDURE delete_acls ()
BEGIN
    DELETE FROM acl_security_identities;
    DELETE FROM acl_object_identities;
    DELETE FROM acl_object_identity_ancestors;
    DELETE FROM acl_classes;
    DELETE FROM acl_entries;
END //
