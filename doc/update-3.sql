insert into adm_menu (id_menu, title, created_by, created_date, last_user, last_update) values 
(155, 'Brand - Upload', 'system', sysdate(), 'system', sysdate());

insert into adm_role_dtl (id_hdr, id_menu) values (1, 155);

-- BORDER --

alter table mst_storemap add column wide decimal(8, 2);

delimiter $$

drop PROCEDURE `storemap_add` $$

CREATE PROCEDURE `storemap_add`(
        p_code varchar(12),
        p_name varchar(100),
        p_description varchar(255),
        p_brand_name varchar(100),
        p_shape varchar(10),
        p_coordinate varchar(255),
        p_init_color varchar(7),
        p_map_code varchar(12),
        p_top_left varchar(37),
        p_bottom_right varchar(37),
        p_center varchar(37),
        p_radius varchar(18),
        p_created_by varchar(20),
        p_created_date datetime,
        p_wide decimal(8, 2)
        )
BEGIN
        insert into mst_storemap (
                code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, wide
        ) values (
                p_code, p_name, p_description, p_brand_name, p_shape, p_coordinate, p_init_color, p_map_code, p_top_left, p_bottom_right, p_center, p_radius, p_created_by, p_created_date, p_wide
        );
END$$

delimiter $$

drop PROCEDURE `storemap_update` $$

CREATE PROCEDURE `storemap_update`(
        p_id integer,
        p_code varchar(12),
        p_name varchar(100),
        p_description varchar(255),
        p_brand_name varchar(100),
        p_shape varchar(10),
        p_coordinate varchar(255),
        p_init_color varchar(7),
        p_map_code varchar(12),
        p_top_left varchar(37),
        p_bottom_right varchar(37),
        p_center varchar(37),
        p_radius varchar(18),
        p_last_user varchar(20),
        p_last_update datetime,
        p_wide decimal(8, 2)
        )
BEGIN
        update mst_storemap set
                code = p_code,
                name = p_name,
                description = p_description,
                brand_name = p_brand_name,
                shape = p_shape,
                coordinate = p_coordinate,
                init_color = p_init_color,
                map_code = p_map_code,
                top_left = p_top_left,
                bottom_right = p_bottom_right,
                center = p_center,
                radius = p_radius,
                last_user = p_last_user,
                last_update = p_last_update,
                wide = p_wide
        where id = p_id;
END$$

delimiter $$

drop PROCEDURE `storemap_load`$$

CREATE PROCEDURE `storemap_load`(p_id integer)
begin
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update, wide
        from mst_storemap
        where id = p_id;
end$$

delimiter $$

drop PROCEDURE `storemap_load_all`$$

CREATE PROCEDURE `storemap_load_all`()
begin
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update, wide
        from mst_storemap
        order by code;
end$$

delimiter $$

CREATE PROCEDURE `storemap_load_by_map_code`(p_map_code varchar(12))
begin
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update, wide
        from mst_storemap
        where map_code = p_map_code
        order by code;
end$$

delimiter $$

drop PROCEDURE `storemap_load_by_map_id` $$

CREATE PROCEDURE `storemap_load_by_map_id`(p_map_id integer)
begin
        declare v_map_code varchar(12) default '';
        declare continue handler for not found set v_map_code = '';
        
        select code into v_map_code from mst_map where id = p_map_id;
        
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update, wide
        from mst_storemap
        where map_code = v_map_code
        order by code;
end$$