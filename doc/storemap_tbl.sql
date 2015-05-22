-- added 29-Apr-15 --

create table trn_sales_mkg_by_brand (
    id integer not null auto_increment,
    store_init varchar(5),
    trans_date date,
    pos_no integer,
    brand_name varchar(30),
    division varchar(50),
    qty decimal(8, 2),
    amount decimal(18, 2),
    last_update datetime,
    primary key (id)
);

alter table trn_sales_mkg_by_brand add index (store_init);
alter table trn_sales_mkg_by_brand add index (trans_date);
alter table trn_sales_mkg_by_brand add index (pos_no);
alter table trn_sales_mkg_by_brand add index (brand_name);
alter table trn_sales_mkg_by_brand add index (division);

create table trn_sales_mkg_import (
    id integer not null auto_increment,
    filename varchar(50),
    created_by varchar(20),
    created_date datetime,
    primary key (id)
);

create table trn_sales_mkg (
    id integer not null auto_increment,
    id_import integer not null,
    store_init varchar(5),
    trans_date date,
    trans_no integer,
    pos_no integer,
    tpl_plu varchar(14),
    sku varchar(14),
    gold_plu varchar(14),
    category varchar(6),
    qty decimal(8, 2),
    gross_sale decimal(18, 2),
    disc decimal(18, 2),
    primary key (id)
);

alter table trn_sales_mkg add index (store_init);
alter table trn_sales_mkg add index (trans_date);
alter table trn_sales_mkg add index (pos_no);
alter table trn_sales_mkg add index (gold_plu);
alter table trn_sales_mkg add index (category);
alter table trn_sales_mkg add index (id_import);

-- alter table trn_sales_mkg add column is_obral integer default 0;
-- alter table trn_sales_mkg add index (is_obral);

-- added 10-Apr-15 --

create table trn_sales_by_brand (
    id integer not null auto_increment,
    store_init varchar(5),
    trans_date date,
    brand_name varchar(30),
    division varchar(50),
    qty decimal(8, 2),
    amount decimal(18, 2),
    last_update datetime,
    primary key (id)
);

alter table trn_sales_by_brand add index (store_init);
alter table trn_sales_by_brand add index (trans_date);
alter table trn_sales_by_brand add index (brand_name);
alter table trn_sales_by_brand add index (division);

create table mst_article_gold (
    id integer not null auto_increment,
    article_code varchar(13),
    description varchar(200),
    tipo integer,
    uom varchar(20), 
    brand_code varchar(4),  
    brand_name varchar(30),
    division varchar(5),
    article_type integer default 0, -- 0: normal, 1: obral
    start_date date,
    end_date date,
    created_by varchar(20),
    created_date datetime,
    last_update datetime,
    primary key (id)
);

alter table mst_article_gold add index (article_code);
alter table mst_article_gold add index (tipo);
alter table mst_article_gold add index (brand_code);
alter table mst_article_gold add index (brand_name);
alter table mst_article_gold add index (division);
alter table mst_article_gold add index (article_type);
alter table mst_article_gold add index (start_date);
alter table mst_article_gold add index (end_date);

create table trn_sales_import (
    id integer not null auto_increment,
    filename varchar(20),
    created_by varchar(20),
    created_date datetime,
    primary key (id)
);

create table trn_sales_tpl (
    id integer not null auto_increment,
    id_import integer not null,
    store_init varchar(5),
    trans_date date,
    tpl_plu varchar(14),
    sku varchar(14),
    gold_plu varchar(14),
    dept varchar(6),
    unit_price decimal(18, 2),
    qty decimal(8, 2),
    gross_sale decimal(18, 2),
    disc decimal(18, 2),
    primary key (id)
);

alter table trn_sales_tpl add index (store_init);
alter table trn_sales_tpl add index (trans_date);
alter table trn_sales_tpl add index (gold_plu);
alter table trn_sales_tpl add index (dept);
alter table trn_sales_tpl add index (id_import);

-- added 26-Feb-15 --

create table mst_division (
    code varchar(5),
    name varchar(50),
    primary key (code)
);

alter table mst_division add index (name);

insert into mst_division (code, name) values
('A', 'LADIES'), ('B', 'MENS'), ('C', 'BABY AND KIDS'), ('D', 'SHOES AND BAGS'), ('E', 'BEAUTY AND ACCESSORIES');

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

alter table trn_sales_hst add column division varchar(50);
alter table trn_sales_hst add index (division);

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

alter table trn_sales add column division varchar(50);
alter table trn_sales add index (division);

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

alter table mst_brand add column code varchar(10);
alter table mst_brand add index (code);

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

alter table mst_article add column division varchar(50);
alter table mst_article add index (division);

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

alter table mst_storemap add column wide decimal(8, 2);

alter table mst_storemap add column division varchar(50);
alter table mst_storemap add index (division);

alter table mst_storemap add column terminal_no integer default 0;
alter table mst_storemap add index (terminal_no);

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

alter table mst_status add column min_value_wide decimal(18, 2);
alter table mst_status add column max_value_wide decimal(18, 2);

alter table mst_status add index (code);
alter table mst_status add index (color);
alter table mst_status add index (min_value);
alter table mst_status add index (max_value);
alter table mst_status add index (min_value_wide);
alter table mst_status add index (max_value_wide);

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
