delimiter $$

drop  FUNCTION `sales_find_amount_by_type` $$

CREATE FUNCTION `sales_find_amount_by_type`(p_brand_name varchar(100), p_division varchar(50), p_article_type integer, p_map_id integer) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;
        
        select x.amount into v_data from trn_sales x
        inner join mst_storemap y on y.brand_name = x.brand_name and y.division = x.division 
        inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
        where x.brand_name = p_brand_name and x.division = p_division and x.article_type = p_article_type and z.id = p_map_id
        order by x.trans_date desc limit 1;
        
        return v_data;
END $$

drop  FUNCTION `sales_find_amount_by_brand_type_map` $$

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

drop  FUNCTION `sales_find_quantity_by_type`$$

CREATE FUNCTION `sales_find_quantity_by_type`(p_brand_name varchar(100), p_division varchar(50), p_article_type integer, p_map_id integer) RETURNS decimal(18, 2)
BEGIN
        declare v_data decimal(18, 2) default 0;
        declare continue handler for not found set v_data = 0;
        
        select x.quantity into v_data from trn_sales x
        inner join mst_storemap y on y.brand_name = x.brand_name and y.division = x.division 
        inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
        where x.brand_name = p_brand_name and x.division = p_division and x.article_type = p_article_type and z.id = p_map_id
        order by x.trans_date desc limit 1;
        
        return v_data;
END $$

drop  FUNCTION `sales_find_quantity_by_brand_type_map`$$

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

delimiter $$

drop  FUNCTION `sales_find_amount` $$

CREATE FUNCTION `sales_find_amount`(p_brand_name varchar(100), p_division varchar(50), p_map_id integer) RETURNS decimal(18, 2)
BEGIN
        declare v_date_f varchar(10);
        declare v_data decimal(18, 2) default 0;
        
        begin
            declare continue handler for not found set v_date_f = '2001-01-01';
            
            select date_format(x.trans_date, '%Y-%m-%d') into v_date_f from trn_sales x
            inner join mst_storemap y on y.brand_name = x.brand_name and y.division = x.division 
            inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
            where x.brand_name = p_brand_name and x.division = p_division and z.id = p_map_id
            order by x.trans_date desc limit 1;
        end;
        
        begin
            declare continue handler for not found set v_data = 0;
            
            select ifnull(sum(x.amount), 0) into v_data from trn_sales x
            inner join mst_storemap y on y.brand_name = x.brand_name and y.division = x.division 
            inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
            where x.brand_name = p_brand_name and x.division = p_division and z.id = p_map_id
            and date_format(x.trans_date, '%Y-%m-%d') = v_date_f;
        end;
        
        return v_data;
END $$

delimiter $$

drop  FUNCTION `sales_find_amount_by_brand_map`$$

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

drop  FUNCTION `sales_find_quantity`$$

CREATE FUNCTION `sales_find_quantity`(p_brand_name varchar(100), p_division varchar(50), p_map_id integer) RETURNS decimal(18, 2)
BEGIN
        declare v_date_f varchar(10);
        declare v_data decimal(18, 2) default 0;
        
        begin
            declare continue handler for not found set v_date_f = '2001-01-01';
            
            select date_format(x.trans_date, '%Y-%m-%d') into v_date_f from trn_sales x
            inner join mst_storemap y on y.brand_name = x.brand_name and y.division = x.division 
            inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
            where x.brand_name = p_brand_name and x.division = p_division and z.id = p_map_id
            order by x.trans_date desc limit 1;
        end;
        
        begin
            declare continue handler for not found set v_data = 0;
            
            select ifnull(sum(x.quantity), 0) into v_data from trn_sales x
            inner join mst_storemap y on y.brand_name = x.brand_name and y.division = x.division 
            inner join mst_map z on z.code = y.map_code and z.store_init = x.store_init
            where x.brand_name = p_brand_name and x.division = p_division and z.id = p_map_id
            and date_format(x.trans_date, '%Y-%m-%d') = v_date_f;
        end;
        
        return v_data;
END $$

delimiter $$


drop FUNCTION `sales_find_quantity_by_brand_map` $$

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