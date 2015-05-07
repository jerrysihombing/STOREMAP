-- added @05May-15 --

delimiter $$
-- belum diedit, heuu
CREATE FUNCTION `is_article_obral`(p_brand_name varchar(100), p_division varchar(50), p_start_date date, p_end_date date, p_store_code varchar(3), p_article_type integer) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;

        -- p_article_type still useless
        
        if p_end_date = "0000-00-00" then
            select ifnull(sum(amount), 0) into v_data
            from trn_sales_by_brand
            where trans_date = p_start_date and store_init = p_store_code and brand_name = p_brand_name and division = p_division;
        else
            select ifnull(sum(amount), 0) into v_data
            from trn_sales_by_brand
            where trans_date between p_start_date and p_end_date
            and store_init = p_store_code and brand_name = p_brand_name and division = p_division;
        end if;
        
        return v_data;
END $$

-- added @20Apr-15 --

delimiter $$

CREATE FUNCTION `sales_find_amount_per_brand`(p_brand_name varchar(100), p_division varchar(50), p_start_date date, p_end_date date, p_store_code varchar(3), p_article_type integer) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;

        -- p_article_type still useless
        
        if p_end_date = "0000-00-00" then
            select ifnull(sum(amount), 0) into v_data
            from trn_sales_by_brand
            where trans_date = p_start_date and store_init = p_store_code and brand_name = p_brand_name and division = p_division;
        else
            select ifnull(sum(amount), 0) into v_data
            from trn_sales_by_brand
            where trans_date between p_start_date and p_end_date
            and store_init = p_store_code and brand_name = p_brand_name and division = p_division;
        end if;
        
        return v_data;
END $$

delimiter $$

CREATE FUNCTION `sales_find_quantity_per_brand`(p_brand_name varchar(100), p_division varchar(50), p_start_date date, p_end_date date, p_store_code varchar(3), p_article_type integer) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;
        
        -- p_article_type still useless
        
        if p_end_date = "0000-00-00" then
            select ifnull(sum(qty), 0) into v_data
            from trn_sales_by_brand
            where trans_date = p_start_date and store_init = p_store_code and brand_name = p_brand_name and division = p_division;
        else
            select ifnull(sum(qty), 0) into v_data
            from trn_sales_by_brand
            where trans_date between p_start_date and p_end_date
            and store_init = p_store_code and brand_name = p_brand_name and division = p_division;
        end if;
        
        return v_data;
END $$

-- end added --

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

delimiter $$

CREATE FUNCTION `store_get_code`(p_init varchar(5)) RETURNS varchar(3)
BEGIN
        declare code varchar(3);
        declare continue handler for not found set code = '';
        
        select store_code into code
        from mst_site
        where store_init = p_init and store_init <> '' limit 1;
        
        return code;
END $$

delimiter $$

CREATE FUNCTION `store_get_init`(p_code varchar(3)) RETURNS varchar(5)
BEGIN
        declare init varchar(5);
        declare continue handler for not found set init = '';
        
        select store_init into init
        from mst_site
        where store_code = p_code limit 1;
        
        return init;
END $$

delimiter $$

CREATE PROCEDURE `site_load_all`()
BEGIN
        select site, store_code, store_init, store_name, regional_code, regional_init, regional_name
        from mst_site
        order by site;
END$$

delimiter $$

CREATE PROCEDURE `store_load`(p_init varchar(10))
BEGIN
        select distinct store_code, store_init, store_name
        from mst_site
        where store_init = p_init and store_init <> '';
END$$

delimiter $$

CREATE PROCEDURE `store_load_all`()
BEGIN
        select distinct store_code, store_init, store_name
        from mst_site where store_init <> '' 
        order by store_init;
END$$


delimiter $$

CREATE PROCEDURE `regional_load_all`()
BEGIN
        select distinct regional_code, regional_init, regional_name
        from mst_site where regional_init <> '' 
        order by regional_init;
END$$


-- added 18-Feb-15 --

delimiter $$

CREATE PROCEDURE `sales_add`(
        p_trans_date datetime,
        p_brand_name varchar(100),
        p_article_type integer,
        p_quantity decimal(18, 2),
        p_amount decimal(18, 2),
        p_store_init varchar(5),
        p_created_by varchar(20),
        p_created_date datetime,
        p_division varchar(50)
        )
BEGIN
        insert into trn_sales (
                trans_date, brand_name, article_type, quantity, amount, store_init, created_by, created_date, division
        ) values (
                p_trans_date, p_brand_name, p_article_type, p_quantity, p_amount, p_store_init, p_created_by, p_created_date, p_division
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
        p_last_update datetime,
        p_division varchar(50)
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
                last_update = p_last_update,
                division = p_division
        where id = p_id;
END$$

CREATE FUNCTION `sales_find_amount_by_brand_type_map`(p_brand_name varchar(100), p_article_type integer, p_map_id integer) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;
        
        select x.amount into v_data from trn_sales x
        inner join mst_storemap y on y.brand_name = x.brand_name and y.division = x.division 
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
        inner join mst_storemap y on y.brand_name = x.brand_name and y.division = x.division 
        inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
        where x.brand_name = p_brand_name and x.article_type = p_article_type and z.id = p_map_id
        order by x.trans_date desc limit 1;
        
        return v_data;
END $$

-- # --

delimiter $$

CREATE FUNCTION `sales_find_amount_by_type`(p_brand_name varchar(100), p_division varchar(50), p_start_date date, p_end_date date, p_store_code varchar(3), p_article_type integer) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;
        
        -- not finished yet
        if p_end_date = "0000-00-00" then
            select ifnull(sum(x.gross_sale-x.disc), 0) into v_data
            from trn_sales_tpl x
            inner join mst_article_gold y on x.gold_plu = y.article_code and (current_date between y.start_date and y.end_date)
            inner join mst_division w on y.division = w.code
            where y.last_update = (select max(v.last_update) from mst_article_gold v where v.article_code = y.article_code)
            and x.trans_date = p_start_date
            and x.store_init = p_store_code and y.brand_name = p_brand_name and w.name = p_division;
        else
            select ifnull(sum(x.gross_sale-x.disc), 0) into v_data
            from trn_sales_tpl x
            inner join mst_article_gold y on x.gold_plu = y.article_code and (current_date between y.start_date and y.end_date)
            inner join mst_division w on y.division = w.code
            where y.last_update = (select max(v.last_update) from mst_article_gold v where v.article_code = y.article_code)
            and x.trans_date between p_start_date and p_end_date
            and x.store_init = p_store_code and y.brand_name = p_brand_name and w.name = p_division;
        end if;
        
        return v_data;
END $$

delimiter $$

CREATE FUNCTION `sales_find_amount`(p_brand_name varchar(100), p_division varchar(50), p_start_date date, p_end_date date, p_store_code varchar(3)) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;
        
        if p_end_date = "0000-00-00" then
            select ifnull(sum(x.gross_sale-x.disc), 0) into v_data
            from trn_sales_tpl x
            inner join mst_article_gold y on x.gold_plu = y.article_code and (current_date between y.start_date and y.end_date)
            inner join mst_division w on y.division = w.code
            where y.last_update = (select max(v.last_update) from mst_article_gold v where v.article_code = y.article_code)
            and x.trans_date = p_start_date
            and x.store_init = p_store_code and y.brand_name = p_brand_name and w.name = p_division;
        else
            select ifnull(sum(x.gross_sale-x.disc), 0) into v_data
            from trn_sales_tpl x
            inner join mst_article_gold y on x.gold_plu = y.article_code and (current_date between y.start_date and y.end_date)
            inner join mst_division w on y.division = w.code
            where y.last_update = (select max(v.last_update) from mst_article_gold v where v.article_code = y.article_code)
            and x.trans_date between p_start_date and p_end_date
            and x.store_init = p_store_code and y.brand_name = p_brand_name and w.name = p_division;
        end if;
        
        return v_data;
END $$


delimiter $$

CREATE FUNCTION `sales_find_quantity_by_type`(p_brand_name varchar(100), p_division varchar(50), p_start_date date, p_end_date date, p_store_code varchar(3), p_article_type integer) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;
        
        -- not finished yet
        if p_end_date = "0000-00-00" then
            select ifnull(sum(x.qty), 0) into v_data
            from trn_sales_tpl x
            inner join mst_article_gold y on x.gold_plu = y.article_code and (current_date between y.start_date and y.end_date)
            inner join mst_division w on y.division = w.code
            where y.last_update = (select max(v.last_update) from mst_article_gold v where v.article_code = y.article_code)
            and x.trans_date = p_start_date
            and x.store_init = p_store_code and y.brand_name = p_brand_name and w.name = p_division;
        else
            select ifnull(sum(x.qty), 0) into v_data
            from trn_sales_tpl x
            inner join mst_article_gold y on x.gold_plu = y.article_code and (current_date between y.start_date and y.end_date)
            inner join mst_division w on y.division = w.code
            where y.last_update = (select max(v.last_update) from mst_article_gold v where v.article_code = y.article_code)
            and x.trans_date between p_start_date and p_end_date
            and x.store_init = p_store_code and y.brand_name = p_brand_name and w.name = p_division;
        end if;
        
        return v_data;
END $$

delimiter $$

CREATE FUNCTION `sales_find_quantity`(p_brand_name varchar(100), p_division varchar(50), p_start_date date, p_end_date date, p_store_code varchar(3)) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;
        
        if p_end_date = "0000-00-00" then
            select ifnull(sum(x.qty), 0) into v_data
            from trn_sales_tpl x
            inner join mst_article_gold y on x.gold_plu = y.article_code and (current_date between y.start_date and y.end_date)
            inner join mst_division w on y.division = w.code
            where y.last_update = (select max(v.last_update) from mst_article_gold v where v.article_code = y.article_code)
            and x.trans_date = p_start_date
            and x.store_init = p_store_code and y.brand_name = p_brand_name and w.name = p_division;
        else
            select ifnull(sum(x.qty), 0) into v_data
            from trn_sales_tpl x
            inner join mst_article_gold y on x.gold_plu = y.article_code and (current_date between y.start_date and y.end_date)
            inner join mst_division w on y.division = w.code
            where y.last_update = (select max(v.last_update) from mst_article_gold v where v.article_code = y.article_code)
            and x.trans_date between p_start_date and p_end_date
            and x.store_init = p_store_code and y.brand_name = p_brand_name and w.name = p_division;
        end if;
        
        return v_data;
END $$

-- # --

delimiter $$

CREATE FUNCTION `sales_find_amount_by_brand_map`(p_brand_name varchar(100), p_map_id integer) RETURNS decimal(18, 2)
BEGIN
        declare v_date_f varchar(10);
        declare v_data decimal(18, 2) default 0;
        
        begin
            declare continue handler for not found set v_date_f = '2001-01-01';
            
            select date_format(x.trans_date, '%Y-%m-%d') into v_date_f from trn_sales x
            inner join mst_storemap y on y.brand_name = x.brand_name and y.division = x.division 
            inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
            where x.brand_name = p_brand_name and z.id = p_map_id
            order by x.trans_date desc limit 1;
        end;
        
        begin
            declare continue handler for not found set v_data = 0;
            
            select ifnull(sum(x.amount), 0) into v_data from trn_sales x
            inner join mst_storemap y on y.brand_name = x.brand_name and y.division = x.division 
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
            inner join mst_storemap y on y.brand_name = x.brand_name and y.division = x.division 
            inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
            where x.brand_name = p_brand_name and z.id = p_map_id
            order by x.trans_date desc limit 1;
        end;
        
        begin
            declare continue handler for not found set v_data = 0;
            
            select ifnull(sum(x.quantity), 0) into v_data from trn_sales x
            inner join mst_storemap y on y.brand_name = x.brand_name and y.division = x.division 
            inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
            where x.brand_name = p_brand_name and z.id = p_map_id
            and date_format(x.trans_date, '%Y-%m-%d') = v_date_f;
        end;
        
        return v_data;
END $$

delimiter $$

CREATE PROCEDURE `sales_load`(p_id integer)
begin
        select id, trans_date, date_format(trans_date, '%d-%m-%Y') trans_date_f, brand_name, article_type, quantity, amount, store_init, created_by, created_date, last_user, last_update, division
        from trn_sales
        where id = p_id;
end$$

delimiter $$

CREATE PROCEDURE `sales_load_all`()
begin
        select id, trans_date, date_format(trans_date, '%d-%m-%Y') trans_date_f, brand_name, article_type, quantity, amount, store_init, created_by, created_date, last_user, last_update, division
        from trn_sales
        order by id;
end$$

delimiter $$

CREATE PROCEDURE `sales_remove`(p_id integer)
begin
        delete from trn_sales where id = p_id;
end$$

delimiter $$

CREATE FUNCTION `sales_count`(p_trans_date varchar(10), p_brand_name varchar(100), p_division varchar(50), p_article_type integer, p_store_init varchar(5)) RETURNS int(11)
BEGIN
        declare cnt integer default 0;
        
        select count(id) into cnt from trn_sales where date_format(trans_date, '%d-%m-%Y') = p_trans_date and brand_name = p_brand_name and division = p_division and article_type = p_article_type and store_init = p_store_init;
        
        return cnt;
END $$

-- ### --

delimiter $$

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

delimiter $$

CREATE PROCEDURE `article_load`(p_id integer)
begin
        select id, plu8, article_type, article_code, description, brand_name, store_init, created_by, created_date, last_user, last_update, division
        from mst_article
        where id = p_id;
end$$

delimiter $$

CREATE PROCEDURE `article_load_all`()
begin
        select id, plu8, article_type, article_code, description, brand_name, store_init, created_by, created_date, last_user, last_update, division
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

delimiter $$

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

delimiter $$

CREATE PROCEDURE `brand_load`(p_id integer)
begin
        select id, name, description, division, store_init, created_by, created_date, last_user, last_update, code 
        from mst_brand
        where id = p_id;
end$$

delimiter $$

CREATE PROCEDURE `brand_load_distinct_name`()
begin
        select distinct name
        from mst_brand
        order by name;
end$$

delimiter $$

CREATE PROCEDURE `brand_load_all`()
begin
        select id, name, description, division, store_init, created_by, created_date, last_user, last_update, code
        from mst_brand
        order by name;
end$$

delimiter $$

CREATE PROCEDURE `brand_remove`(p_id integer)
begin
        delete from mst_brand where id = p_id;
end$$

CREATE FUNCTION `brand_division_count`(p_name varchar(100), p_division varchar(50)) RETURNS int(11)
BEGIN
        declare cnt integer default 0;
        
        select count(id) into cnt from mst_brand where concat(name, division) = concat(p_name, p_division);
        
        return cnt;
END $$

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

-- end added --


delimiter $$

CREATE FUNCTION `data_find_last_by_storemap_code`(p_storemap_code varchar(12), p_data_category varchar(20)) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;
        
        select data_value into v_data from trn_data
        where storemap_code = p_storemap_code and data_category = p_data_category
        order by id desc limit 1;
        
        return v_data;
END $$

delimiter $$

CREATE FUNCTION `data_find_by_storemap_code_and_period`(p_storemap_code varchar(12), p_data_category varchar(20), p_data_month integer, p_data_year integer) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;
        
        select data_value into v_data from trn_data
        where storemap_code = p_storemap_code and data_category = p_data_category and data_month = p_data_month and data_year = p_data_year;
        
        return v_data;
END $$

delimiter $$

CREATE PROCEDURE `data_add`(
        p_map_code varchar(12),
        p_storemap_code varchar(12),
        p_data_category varchar(20),
        p_data_value decimal(18, 2),
        p_data_month integer,
        p_data_year integer,
        p_description varchar(255),
        p_created_by varchar(20),
        p_created_date datetime
        )
BEGIN
        insert into trn_data (
                map_code, storemap_code, data_category, data_value, data_month, data_year, description, created_by, created_date
        ) values (
                p_map_code, p_storemap_code, p_data_category, p_data_value, p_data_month, p_data_year, p_description, p_created_by, p_created_date
        );
END$$

delimiter $$

CREATE PROCEDURE `data_update`(
        p_id integer,
        p_map_code varchar(12),
        p_storemap_code varchar(12),
        p_data_category varchar(20),
        p_data_value decimal(18, 2),
        p_data_month integer,
        p_data_year integer,
        p_description varchar(255),
        p_last_user varchar(20),
        p_last_update datetime
        )
BEGIN
        update trn_data set
                map_code = p_map_code,
                storemap_code = p_storemap_code,
                data_category = p_data_category,
                data_value = p_data_value,
                data_month = p_data_month,
                data_year = p_data_year,
                description = p_description,
                last_user = p_last_user,
                last_update = p_last_update
        where id = p_id;
END$$

delimiter $$

CREATE PROCEDURE `data_load`(p_id integer)
begin
        select id, map_code, storemap_code, data_category, data_value, data_month, data_year, description, created_by, created_date, last_user, last_update
        from trn_data
        where id = p_id;
end$$

delimiter $$

CREATE PROCEDURE `data_load_all`()
begin
        select id, map_code, storemap_code, data_category, data_value, data_month, data_year, description, created_by, created_date, last_user, last_update
        from trn_data
        order by storemap_code;
end$$

delimiter $$

CREATE PROCEDURE `data_remove`(p_id integer)
begin
        delete from trn_data where id = p_id;
end$$

CREATE FUNCTION `data_count`(p_map_code varchar(12), p_storemap_code varchar(12), p_data_category varchar(20), p_data_month integer, p_data_year integer) RETURNS int(11)
BEGIN
        declare cnt integer default 0;
        
        select count(id) into cnt from trn_data
        where map_code = p_map_code and storemap_code = p_storemap_code and data_category = p_data_category and data_month = p_data_month and data_year = p_data_year;
        
        return cnt;
END $$

-- #### --

delimiter $$

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

delimiter $$

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

CREATE PROCEDURE `status_load`(p_id integer)
begin
        select id, code, name, description, color, min_value, max_value, created_by, created_date, last_user, last_update, min_value_wide, max_value_wide
        from mst_status
        where id = p_id;
end$$

delimiter $$

CREATE PROCEDURE `status_load_all`()
begin
        select id, code, name, description, color, min_value, max_value, created_by, created_date, last_user, last_update, min_value_wide, max_value_wide
        from mst_status
        order by code;
end$$

delimiter $$

CREATE PROCEDURE `status_remove`(p_id integer)
begin
        delete from mst_status where id = p_id;
end$$

CREATE FUNCTION `status_count`(p_code varchar(12)) RETURNS int(11)
BEGIN
        declare cnt integer default 0;
        
        if (p_code = "") then
            select count(id) into cnt from mst_status;
        else
            select count(id) into cnt from mst_status where code = p_code;
        end if;
        
        return cnt;
END $$

-- #### --

delimiter $$

CREATE PROCEDURE `map_add`(
        p_code varchar(12),
        p_name varchar(100),
        p_description varchar(255),
        p_store_init varchar(5),
        p_map_file varchar(50),
        p_created_by varchar(20),
        p_created_date datetime
        )
BEGIN
        insert into mst_map (
                code, name, description, store_init, map_file, created_by, created_date
        ) values (
                p_code, p_name, p_description, p_store_init, p_map_file, p_created_by, p_created_date
        );
END$$

delimiter $$

CREATE PROCEDURE `map_update`(
        p_id integer,
        p_code varchar(12),
        p_name varchar(100),
        p_description varchar(255),
        p_store_init varchar(5),
        p_map_file varchar(50),
        p_last_user varchar(20),
        p_last_update datetime
        )
BEGIN
        update mst_map set
                code = p_code,
                name = p_name,
                description = p_description,
                store_init = p_store_init,
                map_file = p_map_file,
                last_user = p_last_user,
                last_update = p_last_update
        where id = p_id;
END$$

delimiter $$

CREATE PROCEDURE `map_load`(p_id integer)
begin
        select id, code, name, description, store_init, map_file, created_by, created_date, last_user, last_update
        from mst_map
        where id = p_id;
end$$

delimiter $$

CREATE PROCEDURE `map_load_all`()
begin
        select id, code, name, description, store_init, map_file, created_by, created_date, last_user, last_update
        from mst_map
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

CREATE PROCEDURE `map_remove`(p_id integer)
begin
        delete from mst_map where id = p_id;
end$$

CREATE FUNCTION `map_count`(p_code varchar(12)) RETURNS int(11)
BEGIN
        declare cnt integer default 0;
        
        if (p_code = "") then
            select count(id) into cnt from mst_map;
        else
            select count(id) into cnt from mst_map where code = p_code;
        end if;
        
        return cnt;
END $$

-- #### --

delimiter $$

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

CREATE PROCEDURE `storemap_load`(p_id integer)
begin
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update, wide, division
        from mst_storemap
        where id = p_id;
end$$

delimiter $$

CREATE PROCEDURE `storemap_load_all`()
begin
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update, wide, division
        from mst_storemap
        order by code;
end$$

delimiter $$

CREATE PROCEDURE `storemap_load_by_map_code`(p_map_code varchar(12))
begin
        select id, code, name, description, brand_name, shape, coordinate, init_color, map_code, top_left, bottom_right, center, radius, created_by, created_date, last_user, last_update, wide, division
        from mst_storemap
        where map_code = p_map_code
        order by code;
end$$

delimiter $$

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

delimiter $$

CREATE PROCEDURE `storemap_remove`(p_id integer)
begin
        delete from mst_storemap where id = p_id;
end$$

CREATE FUNCTION `storemap_count`(p_code varchar(12)) RETURNS int(11)
BEGIN
        declare cnt integer default 0;
        
        if (p_code = "") then
            select count(id) into cnt from mst_storemap;
        else
            select count(id) into cnt from mst_storemap where code = p_code;
        end if;
        
        return cnt;
END $$

