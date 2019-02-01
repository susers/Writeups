CREATE DATABASE demo2;
use demo2;
CREATE TABLE ip_records(
	id int not null auto_increment,
	ip varchar(255) not null,
	time varchar(255) not null,
	primary key(id)
);



CREATE TABLE flaaag(
	id int not null auto_increment,
	fl4g varchar(255),
	primary key(id)
);

insert into flaaag(fl4g) values('SUCTF{f**k1n9_T3rr1bl3_5ql1_7r1ck5}');
CREATE USER 'web'@'localhost' IDENTIFIED BY 'web'; 
GRANT ALL ON demo2.* TO 'web'@'localhost'; 

