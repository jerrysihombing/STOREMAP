create table mst_site (
    site varchar(5) not null,
    store_code varchar(3),
    store_init varchar(5),
    store_name varchar(60),
    regional_code varchar(3),
    regional_init varchar(5),
    regional_name varchar(60),
    primary key (site)
);

alter table mst_site add index (store_init);
alter table mst_site add index (regional_init);

delimiter $$

CREATE PROCEDURE `site_load_all`()
BEGIN
        select site, store_code, store_init, store_name, regional_code, regional_init, regional_name
        from mst_site
        order by site;
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

delimiter ;
