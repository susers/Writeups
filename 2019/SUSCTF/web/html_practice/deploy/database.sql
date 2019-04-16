CREATE DATABASE ctf;
use ctf;
CREATE TABLE users(
	id int not null auto_increment,
	username varchar(1000) not null,
	password varchar(1000) not null,
	student_number varchar(1000),
	html varchar(1000),
	age varchar(1000),
	school varchar(1000),
	primary key(id)
);
CREATE TABLE log(
	id int,
	student_number varchar(1000),
	ip varchar(1000),
	time varchar(1000),
	identity varchar(1000)	
);
