<?php
require_once ("database.php");
require_once ("../classes/connectdb.php");

$handler = connectDB::getHandler($DB_DSN,$DB_NAME, $DB_USER, $DB_PASSWORD);

$handler->query("
DROP DATABASE IF EXISTS $DB_NAME;

CREATE DATABASE $DB_NAME;
USE $DB_NAME;

CREATE TABLE IF NOT EXISTS Users(
    UserID INT AUTO_INCREMENT NOT NULL,
    UserLastName VARCHAR(30) NOT NULL,
    UserFirstName VARCHAR(30) NOT NULL,
    UserEmail VARCHAR(50) NOT NULL,
    Username VARCHAR (30) NOT NULL,
    UserPassword LONGTEXT NOT NULL,
    code text NOT NULL,
    Verified BOOLEAN NOT NULL,
    Notification BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (UserID)
);


CREATE TABLE IF NOT EXISTS UserProfile(
    id INT AUTO_INCREMENT NOT NULL,
    UserID INT NOT NULL,
    Gender VARCHAR(10) NOT NULL,
    Age INT NOT NULL DEFAULT 0,
    `Area` VARCHAR(120),
    Bio TEXT NOT NULL,
    Preference VARCHAR(10) NOT NULL,
    Fame INT NOT NULL DEFAULT 0,
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    PRIMARY KEY (id)
);


CREATE TABLE IF NOT EXISTS UserImages(
    id INT AUTO_INCREMENT NOT NULL,
    UserID INT NOT NULL,
    ImageName VARCHAR(100) NOT NULL,
    ProfileImage BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    PRIMARY KEY (id)
    
);

CREATE TABLE IF NOT EXISTS Tags(
    TagID INT AUTO_INCREMENT NOT NULL,
    TagName VARCHAR(50) NOT NULL,
    PRIMARY KEY (TagID)
);

CREATE TABLE IF NOT EXISTS TagLink(
    id INT AUTO_INCREMENT NOT NULL,
    UserID INT NOT NULL,
    TagID INT NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    FOREIGN KEY (TagID) REFERENCES Tags(TagID),
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS Likes(
    id INT AUTO_INCREMENT NOT NULL,
    Liker INT NOT NULL,
    Liked INT NOT NULL,
    Stat BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (Liker) REFERENCES Users(UserID),
    FOREIGN KEY (Liked) REFERENCES Users(UserID),
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS Blocked(
    id INT AUTO_INCREMENT NOT NULL,
    Blocker INT NOT NULL,
    Blocked INT NOT NULL,
    FOREIGN KEY (Blocker) REFERENCES Users(UserID),
    FOREIGN KEY (Blocked) REFERENCES Users(UserID),
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS Fakes(
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    UserID INT NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

CREATE TABLE IF NOT EXISTS Location(
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    UserID INT NOT NULL,
    Lati VARCHAR(15),
    Logi VARCHAR(15),
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

CREATE TABLE IF NOT EXISTS Chats(
    `id`            INT AUTO_INCREMENT PRIMARY KEY,
    `user_id_from`  INT NOT NULL REFERENCES users(UserID),
    `user_id_to`    INT NOT NULL REFERENCES users(UserID),
    `message`       TEXT NOT NULL,
    `date_updated`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Notification(
    id INT AUTO_INCREMENT PRIMARY KEY,
    FromUser INT NOT NULL REFERENCES users(UserID),
    ToUser INT NOT NULL REFERENCES users(UserID),
    Mssg TEXT NOT NULL,
    Notif_status BOOLEAN NOT NULL DEFAULT FALSE, 
    date_updated  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

    INSERT INTO Tags(TagName)
        VALUES ('Environment');
    
    INSERT INTO Tags(TagName)
        VALUES ('Vegan');
    
    INSERT INTO Tags(TagName)
        VALUES ('Vampires');
    
    INSERT INTO Tags(TagName)
        VALUES ('Sports');
    
    INSERT INTO Tags(TagName)
        VALUES ('Geek');
    
    INSERT INTO Tags(TagName)
        VALUES ('Piercing');
    
    INSERT INTO Tags(TagName)
        VALUES ('Animals');
    
    INSERT INTO Tags(TagName)
         VALUES ('Cartoons');
    
    INSERT INTO Tags(TagName)
        VALUES ('Video Games');
    
    INSERT INTO Tags(TagName)
        VALUES ('Movies');

       
");
header ("Location: ../index.php");

/* INSERT INTO `users` (`UserID`, `UserLastName`, `UserFirstName`, `UserEmail`, `Username`, `UserPassword`, `code`, `Verified`, `Notification`) VALUES (NULL, 'Mthom', 'Mellisa', 'mellisa@gm', 'mellie', 'iodhfisohf', 'fihihhuhuigy', '1', '1'), (NULL, 'Mthom', 'Lebo', 'lebo@hgio', 'crazyshawty', 'udhsfiosgio', 'ishfiahfaih', '1', '1'), (NULL, 'Kim', 'Love', 'lovemore', 'lovemore', 'udhsfiosgio', 'ishfiahfaih', '1', '1'), (NULL, 'Tom', 'Tommy', 'tommy@ghihi', 'tommy101', 'udhsfiosgio', 'ishfiahfaih', '1', '1'), (NULL, 'Nelson', 'Nellz', 'nel@something', 'nellyBabe', 'udhsfiosgio', 'ishfiahfaih', '1', '1'), (NULL, 'Thoms', 'Mario', 'mario@gmail', 'mario', 'udhsfiosgio', 'ishfiahfaih', '1', '1'), (NULL, 'Pants', 'Sponge', 'spongeBob', 'spongeBob', 'udhsfiosgio', 'ishfiahfaih', '1', '1'), (NULL, 'Tatum', 'Tum', 'tum@gmail.com', 'tumtum', 'udhsfiosgio', 'ishfiahfaih', '1', '1'), (NULL, 'Pac', 'Pacy', 'pacy.@gm', 'pacman', 'udhsfiosgio', 'ishfiahfaih', '1', '1'), (NULL, 'Brian', 'Brain', 'bfgir@hfiosip', 'brezzy', 'udhsfiosgio', 'ishfiahfaih', '1', '1');

INSERT INTO `userprofile` (`id`, `UserID`, `Gender`, `Age`, `Area`, `Bio`, `Preference`, `Fame`) VALUES (NULL, '11', 'female', '32', 'Johannesburg', 'sahufgesiufbsjk fisuifbsiufgsc bcsuobc', 'bi', '0'), (NULL, '12', 'female', '20', 'Johannesburg', 'hdsfgsoegsiuf sbfsbf', 'bi', '0'), (NULL, '3', 'male', '19', 'Pretoria', 'hffoisfjo jjfihi', 'famale', '0'), (NULL, '4', 'female', '25', 'Johannesburg', 'uoihdiosv uidshvoishvozihcio i', 'female', '15'), (NULL, '5', 'male', '42', 'Sandton', 'husagfuagfaouiohdio', 'male', '10'), (NULL, '6', 'female', '24', 'Midrand', 'huodsfoishfduhv vusbviosdbvios', 'male', '8'), (NULL, '7', 'male', '36', 'Sandton', 'sihusfgsouefh vgdsucosdih uisdghoudsgh', 'male', '0'), (NULL, '8', 'female', '45', 'Johannesburg', 'suisgfuis iusegfuigdsvc cuigdsugsou ugcugsduosgus', 'bi', '5'), (NULL, '9', 'female', '29', 'Sandton', 'udgvosdhvdsi uivgdsuohc sdiuohvciosd', 'bi', '20'), (NULL, '10', 'male', '29', 'Johannesburg', 'buosbfgouds siugfisug siu gvsdiugu', 'bi', '5');
INSERT INTO `userimages` (`id`, `UserID`, `ImageName`, `ProfileImage`) VALUES (NULL, '3', 'uploads/tin.jpeg', '1'), (NULL, '4', 'uploads/tin.jpeg', '1'), (NULL, '5', 'uploads/tin.jpeg', '1'), (NULL, '6', 'uploads/tin.jpeg', '1'), (NULL, '7', 'uploads/tin.jpeg', '1'), (NULL, '8', 'uploads/tin.jpeg', '1'), (NULL, '9', 'uploads/tin.jpeg', '1'), (NULL, '10', 'uploads/tin.jpeg', '1'), (NULL, '11', 'uploads/tin.jpeg', '1'), (NULL, '12', 'uploads/tin.jpeg', '1');
INSERT INTO `taglink` (`id`, `UserID`, `TagID`) VALUES (NULL, '3', '1'), (NULL, '4', '2'), (NULL, '5', '3'), (NULL, '6', '4'), (NULL, '7', '5'), (NULL, '8', '6'), (NULL, '9', '7'), (NULL, '10', '8'), (NULL, '11', '9'), (NULL, '12', '10');
        INSERT INTO `users` (`UserID`, `UserLastName`, `UserFirstName`, `UserEmail`, `Username`, `UserPassword`, `code`, `Verified`, `Notification`) 
        VALUES (NULL, 'Lang', 'Lolo', 'lolo@hot.com', 'lolo', 'ubuewbfwfjuqrf', 'iuhgege478hue8hfhw8', '1', '1'),
         (NULL, 'Run', 'Yonce', 'yonce@hottie.com', 'hottieYonce', 'fihfwhunfiwu3hufbuwundhu', 'iwhnfe9r9hunbe8rhu', '1', '1');



        INSERT INTO `UsersProfile` (`UserID`, `Gender`, `Age`, `Bio`, `Preference`) 
        VALUES ('1', 'female', '19', 'hidyuhd jdhuhn', `bi`), 
        ('2', 'male', '25', 'hsheifho iesdfjpiel isjja', `bi`),
        ('3', 'female', '26', 'hisyd eyhiwi hjdei', `male`),
        ('4', 'female', '32', 'dyffoi fhrihew jded', `female`),
        ('5', 'male', '23', 'jhidhyc yegf sjihd', `female`),
        ('6', 'male', '75', 'sihdiojojk', `famale`),
        ('7', 'female', '66', 'jsiojdo idjmcpskoiuh', `male`),
        ('8', 'female', '44', 'ojksdpojpo hdiowjchi', `male`),
        ('9', 'male', '21', 'sjiodjmcpj cjijhw', `female`),
        ('10', 'male', '19', 'sjiodjojcm iwjjcojmp', `male`),
        ('11', 'female', '29', 'siojc duwhnc outgoing', `female`),
        ('12', 'female', '33', 'sjhuhx udnwkam', `bi`),
        ('13', 'male', '28', 'ihdhcu wgygiwe', `female`),
        ('14', 'male', '27', 'hduishc 8duhwh weijic', `male`),
        ('15', 'male', '64', 'hihx cjiejiw hwehiio', `bi`),
        ('16', 'female', '52', `sjdh dijdh  cuin`, `bi`);

        INSERT INTO `UsersImages` (`UserID`, `ImageName`, `ProfileImage`) 
        VALUES ('1', `uploads/cartoon.jpeg`, `true`), 
        ('2', `uploads/cartoon.jpeg`, `true`),
        ('3', `uploads/cartoon.jpeg`, `true`),
        ('4', `uploads/cartoon.jpeg`, `true`),
        ('5', `uploads/cartoon.jpeg`, `true`),
        ('6', `uploads/cartoon.jpeg`, `true`),
        ('7', `uploads/cartoon.jpeg`, `true`),
        ('8', `uploads/cartoon.jpeg`, `true`),
        ('9', `uploads/cartoon.jpeg`, `true`),
        ('10', `uploads/cartoon.jpeg`, `true`),
        ('11', `uploads/cartoon.jpeg`, `true`),
        ('12', `uploads/cartoon.jpeg`, `true`),
        ('13', `uploads/cartoon.jpeg`, `true`),
        ('14', `uploads/cartoon.jpeg`, `true`),
        ('15', `uploads/cartoon.jpeg`, `true`),
        ('16', `uploads/cartoon.jpeg`, `true`);

        INSERT INTO `TagLink` (`UserID`, `TagID`) 
        VALUES ('1', `10`), 
        ('2', `10`),
        ('3', `9`),
        ('4', `9`),
        ('5', `8`),
        ('6', `8`),
        ('7', `7`),
        ('8', `6`),
        ('9', `5`),
        ('10', `4`),
        ('11', `3`),
        ('12', `2`),
        ('13', `1`),
        ('14', `4`),
        ('15', `2`),
        ('16', `1`);*/