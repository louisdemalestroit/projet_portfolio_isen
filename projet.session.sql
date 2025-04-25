CREATE TRIGGER after_user_insert
AFTER INSERT ON utilisateurs
FOR EACH ROW
EXECUTE FUNCTION after_user_insert();