

-- BATAS --

alter table trn_sales add column division varchar(50);
alter table trn_sales add index (division);

alter table trn_sales_hst add column division varchar(50);
alter table trn_sales_hst add index (division);

delimiter $$

drop  PROCEDURE `storemap_add`$$

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
        p_wide decimal(8, 2),
        p_division varchar(50)
        )
BEGIN
        insert into mst_storemap (
                code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, wide, division
        ) values (
                p_code, p_name, p_description, p_brand_name, p_shape, p_coordinate, p_init_color, p_map_code, p_top_left, p_bottom_right, p_center, p_radius, p_created_by, p_created_date, p_wide, p_division
        );
END$$

delimiter $$

drop PROCEDURE `storemap_update`$$

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
        p_wide decimal(8, 2),
        p_division varchar(50)
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
                wide = p_wide,
                division = p_division
        where id = p_id;
END$$

delimiter $$

drop PROCEDURE `storemap_load` $$

CREATE PROCEDURE `storemap_load`(p_id integer)
begin
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update, wide, division
        from mst_storemap
        where id = p_id;
end$$

delimiter $$

drop PROCEDURE `storemap_load_all`$$

CREATE PROCEDURE `storemap_load_all`()
begin
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update, wide, division
        from mst_storemap
        order by code;
end$$

delimiter $$

drop  PROCEDURE `storemap_load_by_map_code`$$

CREATE PROCEDURE `storemap_load_by_map_code`(p_map_code varchar(12))
begin
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update, wide, division
        from mst_storemap
        where map_code = p_map_code
        order by code;
end$$

delimiter $$

drop  PROCEDURE `storemap_load_by_map_id`$$

CREATE PROCEDURE `storemap_load_by_map_id`(p_map_id integer)
begin
        declare v_map_code varchar(12) default '';
        declare continue handler for not found set v_map_code = '';
        
        select code into v_map_code from mst_map where id = p_map_id;
        
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update, wide, division
        from mst_storemap
        where map_code = v_map_code
        order by code;
end$$

alter table mst_storemap add column division varchar(50);
alter table mst_storemap add index (division);

delimiter $$

drop PROCEDURE `article_add`$$

CREATE PROCEDURE `article_add`(
        p_plu8 varchar(8),
        p_article_type integer,
        p_article_code varchar(13),
        p_description varchar(255),
        p_brand_name varchar(100),
        p_store_init varchar(5),
        p_created_by varchar(20),
        p_created_date datetime,
        p_division varchar(50)
        )
BEGIN
        insert into mst_article (
                plu8, article_type, article_code, description, brand_name, store_init, created_by, created_date, division
        ) values (
                p_plu8, p_article_type, p_article_code, p_description, p_brand_name, p_store_init, p_created_by, p_created_date, p_division
        );
END$$

delimiter $$

drop PROCEDURE `article_update`$$

CREATE PROCEDURE `article_update`(
        p_id integer,
        p_plu8 varchar(8),
        p_article_type integer,
        p_article_code varchar(13),
        p_description varchar(255),
        p_brand_name varchar(100),
        p_store_init varchar(5),
        p_last_user varchar(20),
        p_last_update datetime,
        p_division varchar(50)
        )
BEGIN
        update mst_article set
                plu8 = p_plu8,
                article_type = p_article_type,
                article_code = p_article_code,
                description = p_description,
                brand_name = p_brand_name,
                store_init = p_store_init,
                last_user = p_last_user,
                last_update = p_last_update,
                division = p_division
        where id = p_id;
END$$

drop PROCEDURE `article_load`$$

CREATE PROCEDURE `article_load`(p_id integer)
begin
        select id, plu8, article_type, article_code, description, brand_name, store_init, created_by, created_date, last_user, last_update, division
        from mst_article
        where id = p_id;
end$$

delimiter $$

drop PROCEDURE `article_load_all`$$

CREATE PROCEDURE `article_load_all`()
begin
        select id, plu8, article_type, article_code, description, brand_name, store_init, created_by, created_date, last_user, last_update, division
        from mst_article
        order by plu8;
end$$

delimiter $$

CREATE PROCEDURE `brand_load_distinct_name`()
begin
        select distinct name
        from mst_brand
        order by name;
end$$

delimiter $$

CREATE PROCEDURE `division_load`(p_code varchar(5))
begin
        select code, name
        from mst_division
        where code = p_code;
end$$

delimiter $$

CREATE PROCEDURE `division_load_all`()
begin
        select code, name
        from mst_division
        order by code;
end$$

create table mst_division (
    code varchar(5),
    name varchar(50),
    primary key (code)
);

alter table mst_division add index (name);

insert into mst_division (code, name) values
('A', 'LADIES'), ('B', 'MENS'), ('C', 'BABY AND KIDS'), ('D', 'SHOES AND BAGS'), ('E', 'BEAUTY AND ACCESSORIES');

alter table mst_article add column division varchar(50);
alter table mst_article add index (division);

delimiter $$

drop PROCEDURE `brand_add`$$

CREATE PROCEDURE `brand_add`(
        p_name varchar(100),
        p_description varchar(255),
        p_division varchar(50),
        p_store_init varchar(5),
        p_created_by varchar(20),
        p_created_date datetime,
        p_code varchar(10)
        )
BEGIN
        insert into mst_brand (
                name, description, division, store_init, created_by, created_date, code 
        ) values (
                p_name, p_description, p_division, p_store_init, p_created_by, p_created_date, p_code
        );
END$$

drop PROCEDURE `brand_update`$$

CREATE PROCEDURE `brand_update`(
        p_id integer,
        p_name varchar(100),
        p_description varchar(255),
        p_division varchar(50),
        p_store_init varchar(5),
        p_last_user varchar(20),
        p_last_update datetime,
        p_code varchar(10)
        )
BEGIN
        update mst_brand set
                name = p_name,
                description = p_description,
                division = p_division,
                store_init = p_store_init,
                last_user = p_last_user,
                last_update = p_last_update,
                code = p_code
        where id = p_id;
END$$

drop PROCEDURE `brand_load`$$

CREATE PROCEDURE `brand_load`(p_id integer)
begin
        select id, name, description, division, store_init, created_by, created_date, last_user, last_update, code 
        from mst_brand
        where id = p_id;
end$$

delimiter $$

drop PROCEDURE `brand_load_all` $$

CREATE PROCEDURE `brand_load_all`()
begin
        select id, name, description, division, store_init, created_by, created_date, last_user, last_update, code
        from mst_brand
        order by name;
end$$

alter table mst_brand add column code varchar(10);
alter table mst_brand add index (code);

delimiter $$

CREATE FUNCTION `brand_division_count`(p_name varchar(100), p_division varchar(50)) RETURNS int(11)
BEGIN
        declare cnt integer default 0;
        
        select count(id) into cnt from mst_brand where concat(name, division) = concat(p_name, p_division);
        
        return cnt;
END $$

