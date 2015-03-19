delimiter $$

DROP PROCEDURE IF EXISTS `role_add`$$
CREATE PROCEDURE `role_add`(
        p_role_name varchar (20),
        p_description varchar (100),
        p_detail text,
        p_created_by varchar(20),
        p_created_date datetime
        )
BEGIN

        declare idh int;

        DECLARE comma INT DEFAULT 0;
        DECLARE mylist TEXT;
        DECLARE temp TEXT DEFAULT '';
        DECLARE strlen INT;
        DECLARE splitchar CHAR(1) DEFAULT '#';

        START TRANSACTION;

        insert into adm_role_hdr (
                role_name, description, created_by, created_date
        ) values (
                p_role_name, p_description, p_created_by, p_created_date
        );

        SELECT LAST_INSERT_ID() INTO idh;

        set mylist = p_detail;
        set strlen = LENGTH(p_detail);

        SET comma = LOCATE(splitchar, mylist);

        WHILE strlen > 0 DO
                IF comma = 0 THEN
                        SET temp = TRIM(mylist);
                        SET mylist = '';
                        SET strlen = 0;
                END IF;

                IF comma != 0 THEN
                        SET temp = TRIM(SUBSTRING(mylist, 1, comma-1));
                        SET mylist = TRIM(SUBSTRING(mylist FROM comma+1));
                        SET strlen = LENGTH(mylist);
                END IF;

                IF temp != '' THEN

                       set @det = temp;

                       set @sql = 'insert into adm_role_dtl (
                                         id_hdr, id_menu
                                     ) values (';

                       set @sql = concat(@sql, idh);
                       set @sql = concat(@sql, ', ');
                       set @sql = concat(@sql, @det);
                       set @sql = concat(@sql, ')');

                       prepare stmt from @sql;
                       execute stmt;
                       deallocate prepare stmt;

                END IF;

                SET comma = LOCATE(splitchar, mylist);
        END WHILE;

        COMMIT;

END$$

DROP PROCEDURE IF EXISTS `role_load`$$
CREATE PROCEDURE `role_load`(p_id integer)
BEGIN
        select role_name, description, created_by, created_date, last_user, last_update
        from adm_role_hdr
        where id = p_id;
END$$

DROP PROCEDURE IF EXISTS `role_load_all`$$
CREATE PROCEDURE `role_load_all`()
BEGIN
        select id, role_name, description, created_by, created_date, last_user, last_update
        from adm_role_hdr
        order by role_name;
END$$

DROP PROCEDURE IF EXISTS `role_load_dtl`$$
CREATE PROCEDURE `role_load_dtl`(p_id integer)
BEGIN
        select a.id_menu, b.title
        from adm_role_dtl a
        inner join adm_menu b on a.id_menu = b.id_menu
        where a.id_hdr = p_id
        order by a.id_menu;
END$$

DROP PROCEDURE IF EXISTS `role_load_dtl_by_name`$$
CREATE PROCEDURE `role_load_dtl_by_name`(p_name varchar(20))
BEGIN
        select a.id_menu, b.title
        from adm_role_dtl a
        inner join adm_role_hdr c on a.id_hdr = c.id
        inner join adm_menu b on a.id_menu = b.id_menu
        where c.role_name = p_name
        order by a.id_menu;
END$$

DROP PROCEDURE IF EXISTS `role_remove`$$
CREATE PROCEDURE `role_remove`(p_id integer)
BEGIN
        declare rolename varchar(20);

        select role_name into rolename from adm_role_hdr where id = p_id;

        start transaction;

        delete from adm_role_dtl where id_hdr = p_id;
        delete from adm_role_hdr where id = p_id;

        update adm_user set role_name = 'N/A' where role_name = rolename;

        commit;
END$$

DROP PROCEDURE IF EXISTS `role_update`$$
CREATE PROCEDURE `role_update`(
        p_id integer,
        p_role_name varchar (20),
        p_description varchar (100),
        p_detail text,
        p_last_user varchar(20),
        p_last_update datetime
        )
BEGIN

        DECLARE comma INT DEFAULT 0;
        DECLARE mylist TEXT;
        DECLARE temp TEXT DEFAULT '';
        DECLARE strlen INT;
        DECLARE splitchar CHAR(1) DEFAULT '#';

        START TRANSACTION;

        delete from adm_role_dtl where id_hdr = p_id;

        update adm_role_hdr set
                role_name = p_role_name,
                description = p_description,
                last_user = p_last_user,
                last_update = p_last_update
        where id = p_id;

        set mylist = p_detail;
        set strlen = LENGTH(p_detail);

        SET comma = LOCATE(splitchar, mylist);

        WHILE strlen > 0 DO
                IF comma = 0 THEN
                        SET temp = TRIM(mylist);
                        SET mylist = '';
                        SET strlen = 0;
                END IF;

                IF comma != 0 THEN
                        SET temp = TRIM(SUBSTRING(mylist, 1, comma-1));
                        SET mylist = TRIM(SUBSTRING(mylist FROM comma+1));
                        SET strlen = LENGTH(mylist);
                END IF;

                IF temp != '' THEN

                       set @det = temp;

                       set @sql = 'insert into adm_role_dtl (
                                         id_hdr, id_menu
                                     ) values (';

                       set @sql = concat(@sql, p_id);
                       set @sql = concat(@sql, ', ');
                       set @sql = concat(@sql, @det);
                       set @sql = concat(@sql, ')');

                       prepare stmt from @sql;
                       execute stmt;
                       deallocate prepare stmt;

                END IF;

                SET comma = LOCATE(splitchar, mylist);
        END WHILE;

        COMMIT;

END$$


DROP PROCEDURE IF EXISTS `menu_load_all`$$
CREATE PROCEDURE `menu_load_all`()
BEGIN
        select id, id_menu, title, created_by, created_date, last_user, last_update
        from adm_menu
        order by id_menu;
END$$


DROP PROCEDURE IF EXISTS `user_add`$$
CREATE PROCEDURE `user_add`(
        p_user_id varchar(20),
        p_user_name varchar(40),
        p_passwd varchar(40),
        p_email varchar(100),
        p_branch_code varchar(10),
        p_departement varchar(30),
        p_created_by varchar(20),
        p_created_date datetime
        )
BEGIN
        insert into adm_user (
                user_id, user_name, passwd, email, branch_code, departement, created_by, created_date
        ) values (
                p_user_id, p_user_name, p_passwd, p_email, p_branch_code, p_departement, p_created_by, p_created_date
        );
END$$

DROP PROCEDURE IF EXISTS `user_load`$$
CREATE PROCEDURE `user_load`(p_id integer)
BEGIN
        select user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date, last_user, last_update
        from adm_user
        where id = p_id;
END$$

DROP PROCEDURE IF EXISTS `user_load_all`$$
CREATE PROCEDURE `user_load_all`()
BEGIN
        select id, user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date, last_user, last_update
        from adm_user
        order by user_id;
END$$

DROP PROCEDURE IF EXISTS `user_load_by_active`$$
CREATE PROCEDURE `user_load_by_active`(p_active integer)
BEGIN
        select id, user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date, last_user, last_update
        from adm_user
        where active = p_active
        order by user_id;
END$$

DROP PROCEDURE IF EXISTS `user_load_by_role_name`$$
CREATE PROCEDURE `user_load_by_role_name`(p_role_name varchar(20))
BEGIN
        select id, user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date, last_user, last_update
        from adm_user
        where role_name = p_role_name
        order by user_id;
END$$

DROP PROCEDURE IF EXISTS `user_load_by_user_id`$$
CREATE PROCEDURE `user_load_by_user_id`(p_user_id varchar(20))
BEGIN
        select id, user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date, last_user, last_update
        from adm_user
        where user_id = p_user_id;
END$$

DROP PROCEDURE IF EXISTS `user_login`$$
CREATE PROCEDURE `user_login`(p_user_id varchar(20), p_logtime datetime)
BEGIN
        insert into adm_user_login (
                user_id, logtime
        ) values (
                p_user_id, p_logtime
        );
END$$

DROP PROCEDURE IF EXISTS `user_remove`$$
CREATE PROCEDURE `user_remove`(p_id integer)
BEGIN
        delete from adm_user where id = p_id;
END$$

DROP PROCEDURE IF EXISTS `user_set_active`$$
CREATE PROCEDURE `user_set_active`(p_id integer, p_active integer, p_last_user varchar(20), p_last_update datetime)
BEGIN
        update adm_user set active = p_active, last_user = p_last_user, last_update = p_last_update where id = p_id;
END$$

DROP PROCEDURE IF EXISTS `user_set_passwd`$$
CREATE PROCEDURE `user_set_passwd`(p_id integer, p_passwd varchar(40), p_last_user varchar(20), p_last_update datetime)
BEGIN
        update adm_user set passwd = p_passwd, last_user = p_last_user, last_update = p_last_update where id = p_id;
END$$

DROP PROCEDURE IF EXISTS `user_set_passwd_by_user_id`$$
CREATE PROCEDURE `user_set_passwd_by_user_id`(p_user_id varchar(20), p_passwd varchar(40), p_last_user varchar(20), p_last_update datetime)
BEGIN
        update adm_user set passwd = p_passwd, last_user = p_last_user, last_update = p_last_update where user_id = p_user_id;
END$$

DROP PROCEDURE IF EXISTS `user_set_role`$$
CREATE PROCEDURE `user_set_role`(p_id integer, p_role_name varchar(20), p_last_user varchar(20), p_last_update datetime)
BEGIN
        update adm_user set role_name = p_role_name, active = 1, last_user = p_last_user, last_update = p_last_update where id = p_id;
END$$

DROP PROCEDURE IF EXISTS `user_update_with_modify_passwd`$$
CREATE PROCEDURE `user_update_with_modify_passwd`(
        p_id integer,
        p_user_id varchar(20),
        p_user_name varchar(40),
        p_email varchar(100),
        p_branch_code varchar(10),
        p_departement varchar(30),
        p_role_name varchar(20),
        p_active integer,
        p_passwd varchar(40),
        p_last_user varchar(20),
        p_last_update datetime
        )
BEGIN
        update adm_user set
                user_id = p_user_id,
                user_name = p_user_name,
                email = p_email,
                branch_code = p_branch_code,
                departement = p_departement,
                role_name = p_role_name,
                active = p_active,
                passwd = p_passwd,
                last_user = p_last_user,
                last_update = p_last_update
        where id = p_id;
END$$

DROP PROCEDURE IF EXISTS `user_update`$$
CREATE PROCEDURE `user_update`(
        p_id integer,
        p_user_id varchar(20),
        p_user_name varchar(40),
        p_email varchar(100),
        p_branch_code varchar(10),
        p_departement varchar(30),
        p_role_name varchar(20),
        p_active integer,
        p_last_user varchar(20),
        p_last_update datetime
        )
BEGIN
        update adm_user set
                user_id = p_user_id,
                user_name = p_user_name,
                email = p_email,
                branch_code = p_branch_code,
                departement = p_departement,
                role_name = p_role_name,
                active = p_active,
                last_user = p_last_user,
                last_update = p_last_update
        where id = p_id;
END$$


DROP FUNCTION IF EXISTS `role_count`$$
CREATE FUNCTION `role_count`(p_role_name varchar(20)) RETURNS int(11)
BEGIN
        declare cnt integer default 0;

        if p_role_name = '' then
                select count(id) into cnt from adm_role_hdr;
        else
                select count(id) into cnt from adm_role_hdr where role_name = p_role_name;
        end if;

        return cnt;
END$$

DROP FUNCTION IF EXISTS `user_count`$$
CREATE FUNCTION `user_count`(p_user_id varchar(20)) RETURNS int(11)
BEGIN
        declare cnt integer default 0;

        if p_user_id = '' then
                select count(id) into cnt from adm_user;
        else
                select count(id) into cnt from adm_user where user_id = p_user_id;
        end if;

        return cnt;
END$$

DROP FUNCTION IF EXISTS `user_is_valid`$$
CREATE FUNCTION `user_is_valid`(p_user_id varchar(20), p_passwd varchar(40)) RETURNS int(11)
BEGIN
        declare cnt integer default 0;

        select count(id) into cnt from adm_user where user_id = p_user_id and passwd = p_passwd and active = 1;

        return cnt;
END$$

