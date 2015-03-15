/*
* procedure_exemple.
*
* @param {text} p_date (ex : 2013-08-01)
* @return {int} : Si pas d'erreur sortie par defaut (ex : 1)
* @example : select procedure_exemple('');
*/
DROP FUNCTION IF EXISTS `procedure_exemple`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `procedure_exemple`( p_date TEXT ) RETURNS int(11)
    DETERMINISTIC
BEGIN
	DECLARE v_hello VARCHAR(100);

	RETURN 1; 
END$$