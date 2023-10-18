DROP USER IF EXISTS 'bruelmvc'@'%';

CREATE USER IF NOT EXISTS 'bruelmvc'@'%'
IDENTIFIED BY 'okami';

GRANT SELECT, INSERT, UPDATE, DELETE ON clicommvc.* TO 'bruelmvc'@'%';