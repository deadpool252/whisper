create table user(
	userId varchar(30),
	userName varchar(20) not null,
	password varchar(64) not null,
	profile varchar(200) default "",
	iconPath varchar(100),
	primary key(userId)
);

create table follow(
	userId varchar(30),
	followUserId varchar(30),
	primary key(userId,followUserId)
);

create table whisper(
	whisperNo bigint auto_increment,
	userId varchar(30) not null,
	postDate date not null default (now()),
	content varchar(256) not null,
	imagePath varchar(100) ,
	primary key(whisperNo)
);

create table goodInfo(
	userId varchar(30),
	whisperNo bigint,
	primary key(userId,whisperNo)
);


