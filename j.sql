drop DATABASE IF  EXISTS chatapp;
create database chatapp;
use chatapp;
CREATE TABLE user (
#thống tin người dùng
    userid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`email` nvarchar(50),#mail
		`pass` nvarchar(100),
		`name` nvarchar(20),
		`avata` nvarchar(100),
		`mota` nvarchar(300),
		`phone` nvarchar(20),
		`diachi` nvarchar(100),
		`token` nvarchar(500),
        `joindate` char(10)
		
);

create table boxchat( 
	#đoạn chat với bạn bè
	chanel char(10) NOT NULL  PRIMARY KEY,#id boxchat (tự tăng)
    user1 int, FOREIGN KEY (user1) REFERENCES user(userid),#id người gửi
    user2 int, FOREIGN KEY (user2) REFERENCES user(userid)#id người nhận

);

create table groupChat(
	#thông tin group chat
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,#id  (tự tăng)
    tittle nvarchar(30),
    creator int ,FOREIGN KEY (creator) REFERENCES user(userid),
    chanel nvarchar(10)
);
create table gruopChatMember(
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,#id  (tự tăng)
    groupchatid int,FOREIGN KEY (groupchatid) REFERENCES groupChat(id),
    memberid int,
    FOREIGN KEY (memberid) REFERENCES user(userid)
);
create table groupChatMessage(
	#chi tiết đoạn chat
	messageid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,#id tin nhắn (tự tăng)
    senderid int,FOREIGN KEY (senderid) REFERENCES user(userid),#người nhận
    content nvarchar(500) ,#nội dung text
    type nvarchar(10) not null,#kiểu tin nhắn , file,text...
    timeForMediaCall nvarchar(100),#trạng thái (gọi nhỡ...) ,thời gian gọi(nếu nghe máy ) cho cuộc gọi video,voice call
    groupchatid int,FOREIGN KEY (groupchatid) REFERENCES groupChat(id),#nếu nhắn vào nhóm thì lưu vào đây
    time datetime#ngày giừo gửi
);

create table message(
	#chi tiết đoạn chat
	messageid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,#id tin nhắn (tự tăng)
	sender int, #FOREIGN KEY (sender) REFERENCES user(userid),#id người nhận
    content nvarchar(500) ,#nội dung text
    type nvarchar(10) not null,#kiểu tin nhắn , file,text...
    timeForMediaCall nvarchar(100),#trạng thái (gọi nhỡ...) ,thời gian gọi(nếu nghe máy ) cho cuộc gọi video,voice call
    chanel char(10),FOREIGN KEY (chanel) REFERENCES boxchat(chanel),#nêu là nhắn cho 1 người 
    time char(10)#ngày giừo gửi
);

create table boxChatMessageStatus
(	
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,#id  (tự tăng)
	withUserid int,FOREIGN KEY (withUserid) REFERENCES user(userid),#id với user id
	status char(10),#trạng thái tin nhắn(đã xem , đã xoá,dã thu hồi ...)
    chanel char(10),FOREIGN KEY (chanel) REFERENCES boxchat(chanel)
);
create table messageStatus(
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,#id  (tự tăng)
	withUserid int,#FOREIGN KEY (withUserid) REFERENCES user(userid),#id với user id
	status char(10),#trạng thái tin nhắn(đã xem , đã xoá,dã thu hồi ...)
    messegerid int,FOREIGN KEY (messegerid) REFERENCES message(messageid)
);
create table groupmessageStatus(
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,#id  (tự tăng)
	withUserid int,FOREIGN KEY (withUserid) REFERENCES user(userid),#id với user id
	status char(10),#trạng thái tin nhắn(đã xem , đã xoá,dã thu hồi ...)
    messegerid int,FOREIGN KEY (messegerid) REFERENCES groupChatMessage(messageid)
);
create table Friend(
#lưu trữ thông tin lời mời kết bạn gửi cho nhau
	Friendid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,#id (tự tăng)
    fromid int, FOREIGN KEY (fromid) REFERENCES user(userid),#người gửi
    receiverid int, FOREIGN KEY (receiverid) REFERENCES user(userid),#người nhận
    status char(10),#trạng thái (đồng ý,đang là lời mời)
    time char(10)#thời gian gửi
   
);

DELIMITER $$
 
CREATE  TRIGGER after_members_insert
AFTER UPDATE
ON friend FOR EACH ROW
BEGIN
    if NEW.status = '1' then
        insert into `boxchat`(`user1`,`user2`,`chanel`)values (NEW.fromid,NEW.receiverid, CONCAT (NEW.fromid,'_', NEW.receiverid)) ;
    END IF;
    
END$$



#dưới là code nháp







 #  SELECT * FROM user
	#	ORDER BY RAND()
	#	LIMIT ;


update `friend` set status ='1' where Friendid='6';
update user set diachi='This person has not set a adress';	
INSERT INTO `friend`(`Friendid`, `fromid`, `receiverid`, `status`, `time`) VALUES (6,2,4,0,NOW());
use chatapp;
select * from friend;

CREATE TRIGGER Insert_Messenger
BEFORE INSERT
   ON message FOR EACH ROW

update friend set status ='1';
use chatapp;
select * from user;
select * from friend;
select * from boxchat;
select * from message;
select * from messageStatus;
insert into boxchat values('2','2','2');
insert into message values('1','vip pro','text','',1,NOW());

select * from message inner join boxchat
	On	boxchat.boxchatid = message.messageid;
		select * from message where messageid in(
        select boxchatid from boxchat where
			(user1 =message.sender or user2 =message.sender) and (user1 ='2' or user2 ='2')
        );
		select * from message;
         
        select f.*,us.* from friend f,user us where (receiverid ='102' or fromid='102') and status='0' group by úu.userid 
		
        select * from boxchat where user1 ='1' or user2 ='1';
        delete from boxchat
		select * from user where user id = '' 