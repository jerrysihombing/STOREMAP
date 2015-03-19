update adm_menu set title = 'Sales - List' where id_menu = 141;
update adm_menu set title = 'Sales - Create' where id_menu = 142;
update adm_menu set title = 'Sales - Edit' where id_menu = 143;
update adm_menu set title = 'Sales - Delete' where id_menu = 144;
    
insert into adm_menu (id_menu, title, created_by, created_date, last_user, last_update) values
    (151, 'Brand - List', 'system', sysdate(), 'system', sysdate()),
    (152, 'Brand - Create', 'system', sysdate(), 'system', sysdate()),
    (153, 'Brand - Edit', 'system', sysdate(), 'system', sysdate()),
    (154, 'Brand - Delete', 'system', sysdate(), 'system', sysdate()),
    (161, 'Article - List', 'system', sysdate(), 'system', sysdate()),
    (162, 'Article - Create', 'system', sysdate(), 'system', sysdate()),
    (163, 'Article - Edit', 'system', sysdate(), 'system', sysdate()),
    (164, 'Article - Delete', 'system', sysdate(), 'system', sysdate()),
    (165, 'Article - Upload', 'system', sysdate(), 'system', sysdate())
    ;

insert into adm_menu (id_menu, title, created_by, created_date, last_user, last_update) values
(145, 'Sales - Upload', 'system', sysdate(), 'system', sysdate());

insert into adm_menu (id_menu, title, created_by, created_date, last_user, last_update) values
(711, 'Report', 'system', sysdate(), 'system', sysdate());
    
insert into adm_role_dtl (id_hdr, id_menu) values
(1, 151), (1, 152), (1, 153), (1, 154), (1, 161), (1, 162), (1, 163), (1, 164), (1, 165)
;

insert into adm_role_dtl (id_hdr, id_menu) values
(1, 145);

insert into adm_role_dtl (id_hdr, id_menu) values
(1, 711);

drop table if exists trn_sales_hst;
create table trn_sales_hst (
    id integer not null auto_increment,
    id_ori integer not null, 
    trans_date datetime,
    brand_name varchar(100),
    article_type integer default 0, -- 0: normal, 1: obral
    quantity decimal(18, 2),
    amount decimal(18, 2),
    store_init varchar(5),
    created_by varchar(20),
    created_date datetime,
    last_user varchar(20),
    last_update datetime,
    primary key (id)
);

alter table trn_sales_hst add index (trans_date);
alter table trn_sales_hst add index (brand_name);
alter table trn_sales_hst add index (article_type);
alter table trn_sales_hst add index (store_init);

drop table if exists trn_sales;
create table trn_sales (
    id integer not null auto_increment,
    trans_date datetime,
    brand_name varchar(100),
    article_type integer default 0, -- 0: normal, 1: obral
    quantity decimal(18, 2),
    amount decimal(18, 2),
    store_init varchar(5),
    created_by varchar(20),
    created_date datetime,
    last_user varchar(20),
    last_update datetime,
    primary key (id)
);

alter table trn_sales add index (trans_date);
alter table trn_sales add index (brand_name);
alter table trn_sales add index (article_type);
alter table trn_sales add index (store_init);

drop table if exists mst_storemap;
create table mst_storemap (
    id integer not null auto_increment,
    code varchar(12),
    name varchar(100),
    description varchar(255),
    brand_name varchar(100),
    shape varchar(10),
    coordinate varchar(255),
    init_color varchar(7),
    map_code varchar(12),
    top_left varchar(37),
    bottom_right varchar(37),
    center varchar(37),
    radius varchar(18),
    created_by varchar(20),
    created_date datetime,
    last_user varchar(20),
    last_update datetime,
    primary key (id)
);

alter table mst_storemap add index (code);
alter table mst_storemap add index (map_code);
alter table mst_storemap add index (brand_name);

create table mst_brand (
    id integer not null auto_increment,
    name varchar(100),
    division varchar(50),
    description varchar(255),
    store_init varchar(5),
    created_by varchar(20),
    created_date datetime,
    last_user varchar(20),
    last_update datetime,
    primary key (id)
);

alter table mst_brand add index (name);
alter table mst_brand add index (division);
alter table mst_brand add index (store_init);

create table mst_article (
    id integer not null auto_increment,
    plu8 varchar(8),
    article_type integer default 0, -- 0: normal, 1: obral
    article_code varchar(13),
    description varchar(255),
    brand_name varchar(100),
    store_init varchar(5),
    created_by varchar(20),
    created_date datetime,
    last_user varchar(20),
    last_update datetime,
    primary key (id)
);

alter table mst_article add index (plu8);
alter table mst_article add index (article_type);
alter table mst_article add index (brand_name);
alter table mst_article add index (store_init);

-- ### ---

delimiter $$

CREATE PROCEDURE `sales_add`(
        p_trans_date datetime,
        p_brand_name varchar(100),
        p_article_type integer,
        p_quantity decimal(18, 2),
        p_amount decimal(18, 2),
        p_store_init varchar(5),
        p_created_by varchar(20),
        p_created_date datetime
        )
BEGIN
        insert into trn_sales (
                trans_date, brand_name, article_type, quantity, amount, store_init, created_by, created_date
        ) values (
                p_trans_date, p_brand_name, p_article_type, p_quantity, p_amount, p_store_init, p_created_by, p_created_date
        );
END$$

delimiter $$

CREATE PROCEDURE `sales_update`(
        p_id integer,
        p_trans_date datetime,
        p_brand_name varchar(100),
        p_article_type integer,
        p_quantity decimal(18, 2),
        p_amount decimal(18, 2),
        p_store_init varchar(5),
        p_last_user varchar(20),
        p_last_update datetime
        )
BEGIN
        update trn_sales set
                trans_date = p_trans_date,
                brand_name = p_brand_name,
                article_type = p_article_type,
                quantity = p_quantity,
                amount = p_amount,
                store_init = p_store_init,
                last_user = p_last_user,
                last_update = p_last_update
        where id = p_id;
END$$

delimiter $$

CREATE PROCEDURE `sales_load`(p_id integer)
begin
        select id, trans_date, date_format(trans_date, '%d-%m-%Y') trans_date_f, brand_name, article_type, quantity, amount, store_init, created_by, created_date, last_user, last_update
        from trn_sales
        where id = p_id;
end$$

delimiter $$

CREATE PROCEDURE `sales_load_all`()
begin
        select id, trans_date, date_format(trans_date, '%d-%m-%Y') trans_date_f, brand_name, article_type, quantity, amount, store_init, created_by, created_date, last_user, last_update
        from trn_sales
        order by id;
end$$

delimiter $$

CREATE PROCEDURE `sales_remove`(p_id integer)
begin
        delete from trn_sales where id = p_id;
end$$

delimiter $$

drop function sales_count $$
CREATE FUNCTION `sales_count`(p_trans_date varchar(10), p_brand_name varchar(100), p_article_type integer, p_store_init varchar(5)) RETURNS int(11)
BEGIN
        declare cnt integer default 0;
        
        select count(id) into cnt from trn_sales where date_format(trans_date, '%d-%m-%Y') = p_trans_date and brand_name = p_brand_name and article_type = p_article_type and store_init = p_store_init;
        
        return cnt;
END $$

delimiter $$

CREATE FUNCTION `sales_find_amount_by_brand_map`(p_brand_name varchar(100), p_map_id integer) RETURNS decimal(18, 2)
BEGIN
        declare v_date_f varchar(10);
        declare v_data decimal(18, 2) default 0;
        
        begin
            declare continue handler for not found set v_date_f = '2001-01-01';
            
            select date_format(x.trans_date, '%Y-%m-%d') into v_date_f from trn_sales x
            inner join mst_storemap y on y.brand_name = x.brand_name
            inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
            where x.brand_name = p_brand_name and z.id = p_map_id
            order by x.trans_date desc limit 1;
        end;
        
        begin
            declare continue handler for not found set v_data = 0;
            
            select sum(x.amount) into v_data from trn_sales x
            inner join mst_storemap y on y.brand_name = x.brand_name
            inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
            where x.brand_name = p_brand_name and z.id = p_map_id
            and date_format(x.trans_date, '%Y-%m-%d') = v_date_f;
        end;
        
        return v_data;
END $$

delimiter $$

CREATE FUNCTION `sales_find_quantity_by_brand_map`(p_brand_name varchar(100), p_map_id integer) RETURNS decimal(18, 2)
BEGIN
        declare v_date_f varchar(10);
        declare v_data decimal(18, 2) default 0;
        
        begin
            declare continue handler for not found set v_date_f = '2001-01-01';
            
            select date_format(x.trans_date, '%Y-%m-%d') into v_date_f from trn_sales x
            inner join mst_storemap y on y.brand_name = x.brand_name
            inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
            where x.brand_name = p_brand_name and z.id = p_map_id
            order by x.trans_date desc limit 1;
        end;
        
        begin
            declare continue handler for not found set v_data = 0;
            
            select sum(x.quantity) into v_data from trn_sales x
            inner join mst_storemap y on y.brand_name = x.brand_name
            inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
            where x.brand_name = p_brand_name and z.id = p_map_id
            and date_format(x.trans_date, '%Y-%m-%d') = v_date_f;
        end;
        
        return v_data;
END $$

delimiter $$

CREATE FUNCTION `sales_find_amount_by_brand_type_map`(p_brand_name varchar(100), p_article_type integer, p_map_id integer) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;
        
        select x.amount into v_data from trn_sales x
        inner join mst_storemap y on y.brand_name = x.brand_name
        inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
        where x.brand_name = p_brand_name and x.article_type = p_article_type and z.id = p_map_id
        order by x.trans_date desc limit 1;
        
        return v_data;
END $$

delimiter $$

CREATE FUNCTION `sales_find_quantity_by_brand_type_map`(p_brand_name varchar(100), p_article_type integer, p_map_id integer) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;
        
        select x.quantity into v_data from trn_sales x
        inner join mst_storemap y on y.brand_name = x.brand_name
        inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
        where x.brand_name = p_brand_name and x.article_type = p_article_type and z.id = p_map_id
        order by x.trans_date desc limit 1;
        
        return v_data;
END $$

-- ### ---

delimiter $$

drop  PROCEDURE `storemap_add` $$
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
        p_created_date datetime
        )
BEGIN
        insert into mst_storemap (
                code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date
        ) values (
                p_code, p_name, p_description, p_brand_name, p_shape, p_coordinate, p_init_color, p_map_code, p_top_left, p_bottom_right, p_center, p_radius, p_created_by, p_created_date
        );
END$$

delimiter $$

drop  PROCEDURE `storemap_update` $$
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
        p_last_update datetime
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
                last_update = p_last_update
        where id = p_id;
END$$

delimiter $$

drop PROCEDURE `storemap_load` $$
CREATE PROCEDURE `storemap_load`(p_id integer)
begin
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update
        from mst_storemap
        where id = p_id;
end$$

delimiter $$

drop PROCEDURE `storemap_load_all` $$
CREATE PROCEDURE `storemap_load_all`()
begin
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update
        from mst_storemap
        order by code;
end$$

delimiter $$

drop PROCEDURE `storemap_load_by_map_code` $$
CREATE PROCEDURE `storemap_load_by_map_code`(p_map_code varchar(12))
begin
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update
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
        
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update
        from mst_storemap
        where map_code = v_map_code
        order by code;
end$$

delimiter $$

CREATE PROCEDURE `map_load_by_store`(p_store_init varchar(5))
begin
        select id, code, name, description, store_init, map_file, created_by, created_date, last_user, last_update
        from mst_map where store_init = p_store_init 
        order by code;
end$$

delimiter $$

CREATE PROCEDURE `article_add`(
        p_plu8 varchar(8),
        p_article_type integer,
        p_article_code varchar(13),
        p_description varchar(255),
        p_brand_name varchar(100),
        p_store_init varchar(5),
        p_created_by varchar(20),
        p_created_date datetime
        )
BEGIN
        insert into mst_article (
                plu8, article_type, article_code, description, brand_name, store_init, created_by, created_date
        ) values (
                p_plu8, p_article_type, p_article_code, p_description, p_brand_name, p_store_init, p_created_by, p_created_date
        );
END$$

delimiter $$

CREATE PROCEDURE `article_update`(
        p_id integer,
        p_plu8 varchar(8),
        p_article_type integer,
        p_article_code varchar(13),
        p_description varchar(255),
        p_brand_name varchar(100),
        p_store_init varchar(5),
        p_last_user varchar(20),
        p_last_update datetime
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
                last_update = p_last_update
        where id = p_id;
END$$

delimiter $$

CREATE PROCEDURE `article_load`(p_id integer)
begin
        select id, plu8, article_type, article_code, description, brand_name, store_init, created_by, created_date, last_user, last_update
        from mst_article
        where id = p_id;
end$$

delimiter $$

CREATE PROCEDURE `article_load_all`()
begin
        select id, plu8, article_type, article_code, description, brand_name, store_init, created_by, created_date, last_user, last_update
        from mst_article
        order by plu8;
end$$

delimiter $$

CREATE PROCEDURE `article_remove`(p_id integer)
begin
        delete from mst_article where id = p_id;
end$$

CREATE FUNCTION `article_count`(p_plu8 varchar(8)) RETURNS int(11)
BEGIN
        declare cnt integer default 0;
        
        if (p_plu8 = "") then
            select count(id) into cnt from mst_article;
        else
            select count(id) into cnt from mst_article where plu8 = p_plu8;
        end if;
        
        return cnt;
END $$

-- ### --

delimiter $$

CREATE PROCEDURE `brand_add`(
        p_name varchar(100),
        p_description varchar(255),
        p_division varchar(50),
        p_store_init varchar(5),
        p_created_by varchar(20),
        p_created_date datetime
        )
BEGIN
        insert into mst_brand (
                name, description, division, store_init, created_by, created_date
        ) values (
                p_name, p_description, p_division, p_store_init, p_created_by, p_created_date
        );
END$$

delimiter $$

CREATE PROCEDURE `brand_update`(
        p_id integer,
        p_name varchar(100),
        p_description varchar(255),
        p_division varchar(50),
        p_store_init varchar(5),
        p_last_user varchar(20),
        p_last_update datetime
        )
BEGIN
        update mst_brand set
                name = p_name,
                description = p_description,
                division = p_division,
                store_init = p_store_init,
                last_user = p_last_user,
                last_update = p_last_update
        where id = p_id;
END$$

delimiter $$

CREATE PROCEDURE `brand_load`(p_id integer)
begin
        select id, name, description, division, store_init, created_by, created_date, last_user, last_update
        from mst_brand
        where id = p_id;
end$$

delimiter $$

CREATE PROCEDURE `brand_load_all`()
begin
        select id, name, description, division, store_init, created_by, created_date, last_user, last_update
        from mst_brand
        order by name;
end$$

delimiter $$

CREATE PROCEDURE `brand_remove`(p_id integer)
begin
        delete from mst_brand where id = p_id;
end$$

CREATE FUNCTION `brand_count`(p_name varchar(100)) RETURNS int(11)
BEGIN
        declare cnt integer default 0;
        
        if (p_name = "") then
            select count(id) into cnt from mst_brand;
        else
            select count(id) into cnt from mst_brand where name = p_name;
        end if;
        
        return cnt;
END $$

delimiter ;
