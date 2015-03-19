-- added 26-Feb-15 --

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

-- end added --

-- added 18-Feb-15 --

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

-- end added --

create table mst_map (
    id integer not null auto_increment,
    code varchar(12),
    name varchar(100),
    description varchar(255),
    store_init varchar(5),
    map_file varchar(50),
    created_by varchar(20),
    created_date datetime,
    last_user varchar(20),
    last_update datetime,
    primary key (id)
);

alter table mst_map add index (code);
alter table mst_map add index (store_init);

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

create table mst_status (
    id integer not null auto_increment,
    code varchar(12),
    name varchar(100),
    description varchar(255),
    color varchar(7),
    min_value decimal(18, 2),
    max_value decimal(18, 2),
    created_by varchar(20),
    created_date datetime,
    last_user varchar(20),
    last_update datetime,
    primary key (id)
);

alter table mst_status add index (code);
alter table mst_status add index (color);
alter table mst_status add index (min_value);
alter table mst_status add index (max_value);

create table trn_data (
    id integer not null auto_increment,
    map_code varchar(12),
    storemap_code varchar(12),
    data_category varchar(20),
    data_value decimal(18, 2),
    data_month integer,
    data_year integer,
    description varchar(255),
    created_by varchar(20),
    created_date datetime,
    last_user varchar(20),
    last_update datetime,
    primary key (id)
);

alter table trn_data add index (map_code);
alter table trn_data add index (storemap_code);
alter table trn_data add index (data_category);
alter table trn_data add index (data_month);
alter table trn_data add index (data_year);
