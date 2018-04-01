CREATE DATABASE demo2;
use demo2;
CREATE TABLE users(
	id int not null auto_increment,
	username varchar(255) not null,
	password varchar(255) not null,
	nickname varchar(255),
	age varchar(255),
	description varchar(255),
	primary key(id)
);

insert into users(username,password) values('adasdasdas','asdasdsadsa');
insert into users(username,password) values('asdfsadfads','asdfsdfdsafsad');
insert into users(username,password) values('4410','627');
insert into users(username,password) values('5146','265');
insert into users(username,password) values('6720','7700');
insert into users(username,password) values('4634','1706');
insert into users(username,password) values('3380','7897');
insert into users(username,password) values('4306','6010');
insert into users(username,password) values('156','542');
insert into users(username,password) values('7675','1730');
insert into users(username,password) values('1518','9274');
insert into users(username,password) values('7182','6217');
insert into users(username,password) values('4028','9331');
insert into users(username,password) values('3064','2599');
insert into users(username,password) values('6501','264');
insert into users(username,password) values('4924','9540');
insert into users(username,password) values('8922','2124');
insert into users(username,password,description) values('admin','blaiubdalifubdfdaf','Susctf{sqli_injection_in_update}');

CREATE USER 'web'@'localhost' IDENTIFIED BY 'web'; 
GRANT ALL ON demo2.* TO 'web'@'localhost'; 
