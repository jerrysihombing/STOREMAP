delimiter $$

drop  FUNCTION `status_find_color`$$

CREATE FUNCTION `status_find_color`(p_value decimal(18, 2), p_wide integer) RETURNS varchar(7)
BEGIN
        declare v_data varchar(7) default '';
        declare continue handler for not found set v_data = 'CCCCCC';
        
        if p_wide = 1 then
            select color into v_data from mst_status where p_value between min_value_wide and max_value_wide;
        else
            select color into v_data from mst_status where p_value between min_value and max_value;
        end if;
        
        return v_data;
END $$

delimiter $$

drop  PROCEDURE `status_add` $$

CREATE PROCEDURE `status_add`(
        p_code varchar(12),
        p_name varchar(100),
        p_description varchar(255),
        p_color varchar(7),
        p_min_value decimal(18, 2),
        p_max_value decimal(18, 2),
        p_created_by varchar(20),
        p_created_date datetime,
        p_min_value_wide decimal(18, 2),
        p_max_value_wide decimal(18, 2)
        )
BEGIN
        insert into mst_status (
                code, name, description, color, min_value, max_value, created_by, created_date, min_value_wide, max_value_wide
        ) values (
                p_code, p_name, p_description, p_color, p_min_value, p_max_value, p_created_by, p_created_date, p_min_value_wide, p_max_value_wide
        );
END$$

drop PROCEDURE `status_update`$$

CREATE PROCEDURE `status_update`(
        p_id integer,
        p_code varchar(12),
        p_name varchar(100),
        p_description varchar(255),
        p_color varchar(7),
        p_min_value decimal(18, 2),
        p_max_value decimal(18, 2),
        p_last_user varchar(20),
        p_last_update datetime,
        p_min_value_wide decimal(18, 2),
        p_max_value_wide decimal(18, 2)
        )
BEGIN
        update mst_status set
                code = p_code,
                name = p_name,
                description = p_description,
                color = p_color,
                min_value = p_min_value,
                max_value = p_max_value,
                last_user = p_last_user,
                last_update = p_last_update,
                min_value_wide = p_min_value_wide,
                max_value_wide = p_max_value_wide
        where id = p_id;
END$$

delimiter $$

drop PROCEDURE `status_load`$$
 
CREATE PROCEDURE `status_load`(p_id integer)
begin
        select id, code, name, description, color, min_value, max_value, created_by, created_date, last_user, last_update, min_value_wide, max_value_wide
        from mst_status
        where id = p_id;
end$$

delimiter $$

drop PROCEDURE `status_load_all`$$

CREATE PROCEDURE `status_load_all`()
begin
        select id, code, name, description, color, min_value, max_value, created_by, created_date, last_user, last_update, min_value_wide, max_value_wide
        from mst_status
        order by code;
end$$

----- -----

alter table mst_status add column min_value_wide decimal(18, 2);
alter table mst_status add column max_value_wide decimal(18, 2);

alter table mst_status add index (min_value_wide);
alter table mst_status add index (max_value_wide);
