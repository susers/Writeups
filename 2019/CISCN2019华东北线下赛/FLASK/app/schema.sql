drop table if exists users;
create table users (
  id integer primary key autoincrement,
  username string not null,
  passwd string not null,
  mail string 
);
insert into users values(1,'admin','YouCantLoginWithThis','ThisIsFakeMail');