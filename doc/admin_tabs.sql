drop table if exists adm_user_login;
drop table if exists adm_menu;
drop table if exists adm_role_hdr;
drop table if exists adm_role_dtl;
drop table if exists adm_user;

create table adm_user_login (
  id integer not null auto_increment,
  user_id varchar(20),
  logtime datetime,
  primary key (id)
) ENGINE = InnoDB;

create table adm_menu (
  id integer not null auto_increment,
  id_menu integer,
  title varchar(100),
  created_by varchar(20),
  created_date datetime,
  last_user varchar(20),
  last_update datetime,
  primary key (id)
) ENGINE = InnoDB;

alter table adm_menu add index (id_menu);

create table adm_role_hdr (
  id integer not null auto_increment,
  role_name varchar(20),
  description varchar(100),
  created_by varchar(20),
  created_date datetime,
  last_user varchar(20),
  last_update datetime,
  primary key (id)
) ENGINE = InnoDB;

alter table adm_role_hdr add index (role_name);

create table adm_role_dtl (
  id integer not null auto_increment,
  id_hdr integer not null,
  id_menu integer not null,
  primary key (id)
) ENGINE = InnoDB;

alter table adm_role_dtl add index (id_hdr);
alter table adm_role_dtl add index (id_menu);

create table adm_user (
  id integer not null auto_increment,
  user_id varchar(20),
  user_name varchar(40),
  passwd varchar(40),
  email varchar(100),
  branch_code varchar(10),
  departement varchar(30),
  role_name varchar(20) default 'N/A',
  active integer default 0, -- 0: inactive, otherwise active
  created_by varchar(20),
  created_date datetime,
  last_user varchar(20),
  last_update datetime,
  primary key (id)
) ENGINE = InnoDB;

alter table adm_user add index (user_id);
alter table adm_user add index (branch_code);
alter table adm_user add index (role_name);

-- master, start with 1 --
insert into adm_menu (id_menu, title, created_by, created_date, last_user, last_update) values
    (111, 'Map - List', 'system', sysdate(), 'system', sysdate()),
    (112, 'Map - Create', 'system', sysdate(), 'system', sysdate()),
    (113, 'Map - Edit', 'system', sysdate(), 'system', sysdate()),
    (114, 'Map - Delete', 'system', sysdate(), 'system', sysdate()),
	(115, 'Map - View', 'system', sysdate(), 'system', sysdate()),
    (121, 'Section - List', 'system', sysdate(), 'system', sysdate()),
    (122, 'Section - Create', 'system', sysdate(), 'system', sysdate()),
    (123, 'Section - Edit', 'system', sysdate(), 'system', sysdate()),
    (124, 'Section - Delete', 'system', sysdate(), 'system', sysdate()),
	(125, 'Section - Info', 'system', sysdate(), 'system', sysdate()),
    (131, 'Status - List', 'system', sysdate(), 'system', sysdate()),
    (132, 'Status - Create', 'system', sysdate(), 'system', sysdate()),
    (133, 'Status - Edit', 'system', sysdate(), 'system', sysdate()),
    (134, 'Status - Delete', 'system', sysdate(), 'system', sysdate()),
    (141, 'Sales - List', 'system', sysdate(), 'system', sysdate()),
    (142, 'Sales - Create', 'system', sysdate(), 'system', sysdate()),
    (143, 'Sales - Edit', 'system', sysdate(), 'system', sysdate()),
    (144, 'Sales - Delete', 'system', sysdate(), 'system', sysdate()),
    (145, 'Sales - Upload', 'system', sysdate(), 'system', sysdate()),
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
    
-- transaction, start with 3 --
    
-- report, start with 7 --
insert into adm_menu (id_menu, title, created_by, created_date, last_user, last_update) values
    (711, 'Report', 'system', sysdate(), 'system', sysdate());
    
-- admin, start with 9 --
insert into adm_menu (id_menu, title, created_by, created_date, last_user, last_update) values
    (999, 'System Administration', 'system', sysdate(), 'system', sysdate());

insert into adm_role_hdr (role_name, description, created_by, created_date, last_user, last_update) values
    ('Administrator', 'Super user role', 'system', sysdate(), 'system', sysdate());

insert into adm_role_dtl (id_hdr, id_menu) values
    (1, 111), (1, 112), (1, 113), (1, 114), (1, 115), (1, 121), (1, 122), (1, 123), (1, 124), (1, 125),
    (1, 131), (1, 132), (1, 133), (1, 134), (1, 141), (1, 142), (1, 143), (1, 144), (1, 145),
    (1, 151), (1, 152), (1, 153), (1, 154), (1, 161), (1, 162), (1, 163), (1, 164), (1, 165), 
    (1, 711), (1, 999);

insert into adm_user (user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date, last_user, last_update) values
    ('admin', 'admin', 'a77856fced892a098d007d7d81180cf36b6e835a', '', '', '', 'Administrator', 1, 'system', sysdate(), 'system', sysdate());

