<?php

namespace Quark\Sql;

enum Dictionary: string
{
    case SELECT = 'SELECT';
    case INSERT_INTO = 'INSERT INTO';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
    case TRUNCATE = 'TRUNCATE';
    case EVERYTHING = '*';

    case FROM = 'FROM';
    case WHERE = 'WHERE';
    case SET = 'SET';
    case VALUES = 'VALUES';
    case ORDER_BY = 'ORDER BY';
    case GROUP_BY = 'GROUP BY';
    case HAVING = 'HAVING';
    case LIMIT = 'LIMIT';
    case OFFSET = 'OFFSET';
    case DISTINCT = 'DISTINCT';
    case JOIN = 'JOIN';
    case INNER_JOIN = 'INNER JOIN';
    case LEFT_JOIN = 'LEFT JOIN';
    case RIGHT_JOIN = 'RIGHT JOIN';
    case FULL_JOIN = 'FULL JOIN';
    case ON = 'ON';
    case AS = 'AS';
    case IN = 'IN';
    case BETWEEN = 'BETWEEN';
    case LIKE = 'LIKE';
    case NOT_LIKE = 'NOT LIKE';
    case IS_NULL = 'IS NULL';
    case IS_NOT_NULL = 'IS NOT NULL';
    case UNION = 'UNION';
    case UNION_ALL = 'UNION ALL';
    case CASE = 'CASE';
    case WHEN = 'WHEN';
    case THEN = 'THEN';
    case ELSE = 'ELSE';
    case END = 'END';

    case COUNT = 'COUNT';
    case SUM = 'SUM';
    case AVG = 'AVG';
    case MAX = 'MAX';
    case MIN = 'MIN';

    case AND = 'AND';
    case OR = 'OR';
    case NOT = 'NOT';
    case XOR = 'XOR';

    case ASC = 'ASC';
    case DESC = 'DESC';

    case EXISTS = 'EXISTS';
    case NOT_EXISTS = 'NOT EXISTS';
    case ANY = 'ANY';
    case ALL = 'ALL';




    case TABLE = 'TABLE';
    case CREATE_TABLE = 'CREATE TABLE';
    case CREATE_INDEX = 'CREATE INDEX';
    case CREATE_VIEW = 'CREATE VIEW';
    case CREATE_TRIGGER = 'CREATE TRIGGER';
    case CREATE_PROCEDURE = 'CREATE PROCEDURE';
    case CREATE_FUNCTION = 'CREATE FUNCTION';
    case CREATE_SCHEMA = 'CREATE SCHEMA';
    case CREATE_DATABASE = 'CREATE DATABASE';
    case CREATE_SEQUENCE = 'CREATE SEQUENCE';

    case ALTER_TABLE = 'ALTER TABLE';
    case ALTER_COLUMN = 'ALTER COLUMN';
    case ALTER_INDEX = 'ALTER INDEX';
    case ALTER_VIEW = 'ALTER VIEW';
    case ALTER_TRIGGER = 'ALTER TRIGGER';
    case ALTER_PROCEDURE = 'ALTER PROCEDURE';
    case ALTER_FUNCTION = 'ALTER FUNCTION';
    case ALTER_DATABASE = 'ALTER DATABASE';
    case ALTER_SCHEMA = 'ALTER SCHEMA';
    case ALTER_SEQUENCE = 'ALTER SEQUENCE';

    case DROP_TABLE = 'DROP TABLE';
    case DROP_INDEX = 'DROP INDEX';
    case DROP_VIEW = 'DROP VIEW';
    case DROP_TRIGGER = 'DROP TRIGGER';
    case DROP_PROCEDURE = 'DROP PROCEDURE';
    case DROP_FUNCTION = 'DROP FUNCTION';
    case DROP_DATABASE = 'DROP DATABASE';
    case DROP_SCHEMA = 'DROP SCHEMA';
    case DROP_SEQUENCE = 'DROP SEQUENCE';

    case RENAME_TABLE = 'RENAME TABLE';
    case RENAME_COLUMN = 'RENAME COLUMN';

    case ADD_COLUMN = 'ADD COLUMN';
    case DROP_COLUMN = 'DROP COLUMN';
    case MODIFY_COLUMN = 'MODIFY COLUMN';
    case CHANGE_COLUMN = 'CHANGE COLUMN';

    case ADD_CONSTRAINT = 'ADD CONSTRAINT';
    case DROP_CONSTRAINT = 'DROP CONSTRAINT';
    case PRIMARY_KEY = 'PRIMARY KEY';
    case FOREIGN_KEY = 'FOREIGN KEY';
    case UNIQUE = 'UNIQUE';
    case CHECK = 'CHECK';
    case DEFAULT = 'DEFAULT';
    case INDEX = 'INDEX';
    case FULLTEXT_INDEX = 'FULLTEXT INDEX';
    case SPATIAL_INDEX = 'SPATIAL INDEX';
    case AUTO_INCREMENT = 'AUTO_INCREMENT';
    case GENERATED_ALWAYS = 'GENERATED ALWAYS';

    case COMMENT = 'COMMENT';
    case ENGINE = 'ENGINE';
    case COLLATE = 'COLLATE';
    case CHARSET = 'CHARSET';
    case TABLESPACE = 'TABLESPACE';

    case REPLACE = 'REPLACE';
    case MERGE = 'MERGE';
    case CALL = 'CALL';
    case LOCK_TABLES = 'LOCK TABLES';
    case UNLOCK_TABLES = 'UNLOCK TABLES';
    case ANALYZE_TABLE = 'ANALYZE TABLE';
    case CHECK_TABLE = 'CHECK TABLE';
    case OPTIMIZE_TABLE = 'OPTIMIZE TABLE';
    case REPAIR_TABLE = 'REPAIR TABLE';

    case SHOW_TABLES = 'SHOW TABLES';
    case SHOW_COLUMNS = 'SHOW COLUMNS';
    case SHOW_INDEXES = 'SHOW INDEXES';
    case SHOW_DATABASES = 'SHOW DATABASES';
    case SHOW_STATUS = 'SHOW STATUS';
    case SHOW_VARIABLES = 'SHOW VARIABLES';
    case SHOW_WARNINGS = 'SHOW WARNINGS';
    case SHOW_ERRORS = 'SHOW ERRORS';
    case SHOW_CREATE_TABLE = 'SHOW CREATE TABLE';
    case SHOW_CREATE_DATABASE = 'SHOW CREATE DATABASE';
    case SHOW_TRIGGERS = 'SHOW TRIGGERS';
    case SHOW_EVENTS = 'SHOW EVENTS';
    case SHOW_PROCESSLIST = 'SHOW PROCESSLIST';
    case FLUSH = 'FLUSH';
    case RESET = 'RESET';

    case CACHE_INDEX = 'CACHE INDEX';
    case LOAD_INDEX = 'LOAD INDEX';
    case RESET_QUERY_CACHE = 'RESET QUERY CACHE';
    case KILL = 'KILL';

    case CREATE_USER = 'CREATE USER';
    case DROP_USER = 'DROP USER';
    case ALTER_USER = 'ALTER USER';
    case RENAME_USER = 'RENAME USER';
    case GRANT = 'GRANT';
    case REVOKE = 'REVOKE';
    case SHOW_GRANTS = 'SHOW GRANTS';

    case START_TRANSACTION = 'START TRANSACTION';
    case COMMIT = 'COMMIT';
    case ROLLBACK = 'ROLLBACK';
    case SAVEPOINT = 'SAVEPOINT';
    case RELEASE_SAVEPOINT = 'RELEASE SAVEPOINT';
    case SET_TRANSACTION = 'SET TRANSACTION';
}
