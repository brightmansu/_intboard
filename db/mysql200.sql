ALTER TABLE prefix_User CHANGE COLUMN u_level u__level smallint NOT NULL

;

ALTER TABLE prefix_User CHANGE COLUMN u_name u__name char(32) NOT NULL

;

ALTER TABLE prefix_User CHANGE COLUMN u_active u__active tinyint NOT NULL

;

ALTER TABLE prefix_User CHANGE COLUMN u_password u__password char(32) NOT NULL

;

ALTER TABLE prefix_User CHANGE COLUMN u_email u__email varchar(48) NOT NULL

;

ALTER TABLE prefix_User CHANGE COLUMN u_pavatar_id u__pavatar_id int unsigned NOT NULL

;

ALTER TABLE prefix_User CHANGE COLUMN u_avatar u__avatar varchar(128) NOT NULL

;

ALTER TABLE prefix_User CHANGE COLUMN u_photo_id u__photo_id int unsigned NOT NULL

;

ALTER TABLE prefix_User CHANGE COLUMN u_regdate u__regdate int unsigned NOT NULL

;

ALTER TABLE prefix_User CHANGE COLUMN u_profileupdate u__profileupdate int unsigned NOT NULL

;

ALTER TABLE prefix_User CHANGE COLUMN u_newpassword u__newpassword char(32) NOT NULL

;

ALTER TABLE prefix_User CHANGE COLUMN u_key u__key char(12) NOT NULL

;

ALTER TABLE prefix_User CHANGE COLUMN u_title u__title varchar(48) NOT NULL

;

ALTER TABLE prefix_Post CHANGE COLUMN p_modcomment p__modcomment text

;

ALTER TABLE prefix_Post CHANGE COLUMN p_time p__time int unsigned NOT NULL

;

ALTER TABLE prefix_Post CHANGE COLUMN p_edittime p__edittime int unsigned NOT NULL

;

ALTER TABLE prefix_Post CHANGE COLUMN p_smiles p__smiles tinyint NOT NULL

;

ALTER TABLE prefix_Post CHANGE COLUMN p_bcode p__bcode tinyint NOT NULL

;

ALTER TABLE prefix_Post CHANGE COLUMN p_html p__html tinyint NOT NULL

;

ALTER TABLE prefix_Post CHANGE COLUMN p_ip p__ip int unsigned NOT NULL

;

ALTER TABLE prefix_Topic CHANGE COLUMN t_views t__views int NOT NULL

;

ALTER TABLE prefix_Topic CHANGE COLUMN t_sticky t__sticky tinyint NOT NULL

;

ALTER TABLE prefix_Topic CHANGE COLUMN t_stickypost t__stickypost tinyint NOT NULL

;

ALTER TABLE prefix_Topic CHANGE COLUMN t_status t__status tinyint NOT NULL

;

ALTER TABLE prefix_Topic CHANGE COLUMN t_rate t__rate tinyint NOT NULL

;

ALTER TABLE prefix_PersonalMessage CHANGE COLUMN pm_box pm__box tinyint NOT NULL

;

ALTER TABLE prefix_PersonalMessage CHANGE COLUMN pm_owner pm__owner int unsigned NOT NULL

;

ALTER TABLE prefix_PersonalMessage CHANGE COLUMN pm_correspondent pm__correspondent int unsigned NOT NULL

;

ALTER TABLE prefix_PersonalMessage CHANGE COLUMN pm_senddate pm__senddate int unsigned NOT NULL

;

ALTER TABLE prefix_PersonalMessage CHANGE COLUMN pm_readdate pm__readdate int unsigned NOT NULL

;

ALTER TABLE prefix_PersonalMessage CHANGE COLUMN pm_html pm__html tinyint NOT NULL

;

ALTER TABLE prefix_Post ADD COLUMN p__premoderate tinyint NOT NULL

;

ALTER TABLE prefix_Forum ADD COLUMN f_premoderate tinyint NOT NULL

;

ALTER TABLE prefix_Forum ADD COLUMN f_lnid int unsigned NOT NULL

;

ALTER TABLE prefix_Forum ADD COLUMN f_downloads int NOT NULL

;

ALTER TABLE prefix_Forum ADD COLUMN f_update int unsigned NOT NULL

;

ALTER TABLE prefix_User ADD COLUMN u_multilang tinyint NOT NULL

;

ALTER TABLE prefix_Language ADD COLUMN ln_charset VARCHAR(20) NOT NULL

;

UPDATE prefix_Language SET ln_charset="windows-1251" WHERE ln_file="ru"

;

ALTER TABLE prefix_ForumType ADD COLUMN tp_searchable tinyint NOT NULL

;

ALTER TABLE prefix_ForumType ADD COLUMN tp_container tinyint NOT NULL

;

ALTER TABLE prefix_ForumType ADD COLUMN tp_menu tinyint NOT NULL

;

UPDATE prefix_ForumType SET tp_searchable=1, tp_menu=1, tp_container=0 WHERE tp_id=1

;

UPDATE prefix_ForumType SET tp_searchable=0, tp_menu=0, tp_container=1, tp_template="contnr" WHERE tp_id=2

;

INSERT INTO prefix_ForumType SET tp_id=3, tp_title="MSG_tp_article", tp_library="article", tp_template="article", tp_modlib="moderate", tp_searchable=1, tp_container=0, tp_menu=1

;

INSERT INTO prefix_ForumType SET tp_id=4, tp_title="MSG_tp_download", tp_library="download", tp_template="download", tp_modlib="moderate", tp_searchable=1, tp_container=0, tp_menu=1

;

INSERT INTO prefix_ForumType SET tp_id=5, tp_title="MSG_tp_irc", tp_library="irc", tp_template="irc", tp_modlib="irc", tp_searchable=0, tp_container=0, tp_menu=1

;

INSERT INTO prefix_ForumType SET tp_id=6, tp_title="MSG_tp_extlink", tp_library="main", tp_template="main", tp_modlib="moderate", tp_searchable=0, tp_container=0, tp_menu=1

;

INSERT INTO prefix_ForumType SET tp_id=7, tp_title="MSG_tp_news", tp_library="news", tp_template="news", tp_modlib="moderate", tp_searchable=1, tp_container=0, tp_menu=1

;

INSERT INTO prefix_ForumType SET tp_id=8, tp_title="MSG_tp_presentation", tp_library="present", tp_template="present", tp_modlib="present", tp_searchable=0, tp_container=1, tp_menu=1

;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_syspass", ad_category="MSG_cat_actions", ad_url="index.php?m=basic&a=sys_pass_change", ad_sortfield=440

;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_site", ad_category="MSG_cat_support", ad_url="http://iboard.xxxxpro.ru", ad_sortfield=11000

;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_suppforum", ad_category="MSG_cat_support", ad_url="http://xxxxpro.ru/forum/index.php?f=14", ad_sortfield=11010

;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_clearusers", ad_category="MSG_cat_user", ad_url="index.php?m=user&a=u_clear", ad_sortfield=240

;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_bannedusers", ad_category="MSG_cat_user", ad_url="index.php?m=user&a=banned_list", ad_sortfield=260

;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_inactiveusers", ad_category="MSG_cat_user", ad_url="index.php?m=user&a=inactive_list", ad_sortfield=250

;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_warnings", ad_category="MSG_cat_user", ad_url="index.php?m=user&a=uw_form", ad_sortfield=260

;

ALTER TABLE prefix_UserRating DROP PRIMARY KEY

;

ALTER TABLE prefix_UserRating ADD PRIMARY KEY (uid, ur_rated, ur_time)

;

ALTER TABLE prefix_User ADD COLUMN u_timelimit smallint NOT NULL

;

CREATE TABLE prefix_Code (
       sid                  char(32) NOT NULL,
       code                 char(8) NOT NULL,
       time                 int unsigned NOT NULL
)

;

CREATE UNIQUE  INDEX XPKprefix_Code ON prefix_Code
(
       sid,code
)
;

CREATE TABLE prefix_Download (
       dl_tid               int unsigned NOT NULL,
       dl_url               varchar(128) NOT NULL,
       dl_homepage          varchar(128) NOT NULL,
       dl__downloads        int NOT NULL,
       dl_disc_tid          int unsigned NOT NULL
)
;

CREATE UNIQUE INDEX XPKprefix_Download ON prefix_Download
(
       dl_tid
)
;

CREATE TABLE prefix_Article (
       a_tid                int unsigned NOT NULL,
       a_author             varchar(64) NOT NULL,
       a_authormail         varchar(48) NOT NULL,
       a_origin             varchar(128) NOT NULL,
       a_originurl          varchar(128) NOT NULL,
       a_disc_tid           int unsigned NULL
)
;

CREATE UNIQUE INDEX XPKprefix_Article ON prefix_Article
(
       a_tid
)
;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_fgroup", ad_category="MSG_cat_forum", ad_url="index.php?m=forum&a=f_group", ad_sortfield=110

;

ALTER TABLE prefix_User ADD u_bday TINYINT UNSIGNED NOT NULL

;

ALTER TABLE prefix_User ADD u_bmonth TINYINT UNSIGNED NOT NULL

;

ALTER TABLE prefix_User ADD u_byear SMALLINT UNSIGNED NOT NULL

;

ALTER TABLE prefix_User ADD u_bmode TINYINT UNSIGNED NOT NULL

;

ALTER TABLE prefix_User ADD u_aol VARCHAR(32)  NOT NULL

;

ALTER TABLE prefix_User ADD u_yahoo VARCHAR(32)  NOT NULL

;

ALTER TABLE prefix_User ADD u_msn VARCHAR(64)  NOT NULL

;

ALTER TABLE prefix_User ADD u_jabber VARCHAR(64)  NOT NULL

;

ALTER TABLE prefix_User ADD u_diary VARCHAR(128)  NOT NULL

;

ALTER TABLE prefix_User ADD u_extform TINYINT NOT NULL DEFAULT 1

;

ALTER TABLE prefix_Post ADD INDEX premod (p__premoderate)

;

ALTER TABLE prefix_Download ADD COLUMN dl_size VARCHAR(10) NOT NULL

;

ALTER TABLE prefix_User ADD u_aperpage SMALLINT UNSIGNED NOT NULL

;

UPDATE prefix_User SET u_aperpage=2*u_tperpage

;

UPDATE prefix_AdminEntry SET ad_url="http://intboard.ru" WHERE ad_name="MSG_ad_site"

;

UPDATE prefix_AdminEntry SET ad_url="http://intboard.ru/index.php?f=14" WHERE ad_name="MSG_ad_suppforum"

;

ALTER TABLE prefix_Log DROP INDEX XPKprefix_Log

;

ALTER TABLE prefix_Log ADD INDEX XPKLog (sid,uo_time,uid)

;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_deluser", ad_category="MSG_cat_user", ad_url="index.php\?m=user&a=u_del_byname", ad_sortfield=215

;

INSERT INTO prefix_ForumType SET tp_id=9, tp_title="MSG_tp_encyclopedia", tp_library="epedia", tp_template="epedia", tp_modlib="epedia", tp_searchable=1, tp_container=0, tp_menu=1

;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_createuser", ad_category="MSG_cat_user", ad_url="index.php\?m=user&a=u_create", ad_sortfield=211

;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_optimize", ad_category="MSG_cat_actions", ad_url="index.php\?m=basic&a=do_optimize", ad_sortfield=435

;

CREATE TABLE prefix_Photo (
  ph_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  ph_tid INTEGER UNSIGNED NULL,
  ph_key CHAR(8) NULL,
  ph_thumb MEDIUMBLOB NULL,
  ph_image LONGBLOB NULL,
  PRIMARY KEY(ph_id),
  UNIQUE INDEX Photo_TID(ph_tid)
)
;

INSERT INTO prefix_ForumType SET tp_id=10, tp_title="MSG_tp_photos", tp_library="photos", tp_template="photos", tp_modlib="photos", tp_searchable=1, tp_container=0, tp_menu=1

;

ALTER TABLE prefix_PersonalMessage DROP INDEX PM_ownerid, ADD INDEX PM_ownerid(pm__owner, pm__box);

ALTER TABLE prefix_Post ADD PRIMARY KEY(p_id), DROP INDEX XPKprefix_Post, DROP INDEX P_tidkey,
ADD INDEX P_tidkey(p_tid, p__premoderate), DROP INDEX posts;

ALTER TABLE prefix_Topic DROP INDEX T_stickykey;

ALTER TABLE prefix_StyleSet ADD COLUMN st_show TINYINT(4) NOT NULL DEFAULT 1;

ALTER TABLE prefix_StyleSet ADD COLUMN st_parent VARCHAR(20) NOT NULL;

UPDATE prefix_StyleSet SET st_parent="abstract" WHERE st_file<>"abstract";

ALTER TABLE prefix_File CHANGE COLUMN file_data file_data LONGBLOB NOT NULL;

ALTER TABLE prefix_Forum ADD COLUMN f__tcount INT UNSIGNED NOT NULL;

ALTER TABLE prefix_Forum ADD COLUMN f__pcount INT UNSIGNED NOT NULL;

ALTER TABLE prefix_Forum ADD COLUMN f__lastpostid INT UNSIGNED NOT NULL;

ALTER TABLE prefix_Forum ADD COLUMN f__startpostid INT UNSIGNED NOT NULL;

ALTER TABLE prefix_Forum ADD COLUMN f__views INT UNSIGNED NOT NULL;

ALTER TABLE prefix_Forum ADD COLUMN f__premodcount INT UNSIGNED NOT NULL;

ALTER TABLE prefix_Topic ADD COLUMN t__pcount INT UNSIGNED NOT NULL;

ALTER TABLE prefix_Topic ADD COLUMN t__startpostid INT UNSIGNED NOT NULL;

ALTER TABLE prefix_Topic ADD COLUMN t__lastpostid INT UNSIGNED NOT NULL;

ALTER TABLE prefix_Topic ADD COLUMN t__ratingsum INT UNSIGNED NOT NULL;

ALTER TABLE prefix_Topic ADD COLUMN t__ratingcount INT UNSIGNED NOT NULL;

ALTER TABLE prefix_User ADD COLUMN u__rating INT NOT NULL;

ALTER TABLE prefix_User ADD COLUMN u__warnings INT NOT NULL;

ALTER TABLE prefix_User ADD COLUMN u__pmcount INT UNSIGNED NOT NULL;

ALTER TABLE prefix_User ADD COLUMN u__warntime INT UNSIGNED NOT NULL;

RENAME TABLE prefix_Vote TO prefix_OldVote;

CREATE TABLE prefix_Vote (
  pvid INTEGER(10) UNSIGNED NOT NULL,
  uid INTEGER(10) UNSIGNED NOT NULL,
  tid INTEGER(10) UNSIGNED NOT NULL,
  PRIMARY KEY(pvid, uid),
  INDEX Topics(tid)
) TYPE=MyISAM;

INSERT INTO prefix_Vote SELECT pvid,uid,pl_tid FROM prefix_OldVote, PollVariant, Poll WHERE pvid=pv_id AND pv_plid=pl_id;

DROP TABLE prefix_OldVote;

ALTER TABLE prefix_Vote ADD COLUMN tid INT UNSIGNED NOT NULL;

ALTER TABLE prefix_PollVariant ADD COLUMN pv_count INT UNSIGNED NOT NULL;

ALTER TABLE prefix_Topic TYPE=MyISAM;

ALTER TABLE prefix_Post TYPE=MyISAM;

CREATE TABLE prefix_UserStat (
  uid INT UNSIGNED NOT NULL,
  fid INT UNSIGNED NOT NULL,
  us_count INT UNSIGNED NOT NULL,
  PRIMARY KEY (fid,uid),
  INDEX uids(uid)
);

CREATE TABLE prefix_ForumView (
  uid INT UNSIGNED NOT NULL,
  fid INT UNSIGNED NOT NULL,
  fv_count INT UNSIGNED NOT NULL,
  PRIMARY KEY (fid,uid),
  INDEX uids(uid)
);

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_backup", ad_category="MSG_cat_actions", ad_url="index.php\?m=basic&a=backup", ad_sortfield=440;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_resync", ad_category="MSG_cat_actions", ad_url="index.php\?m=stats&a=stat_resync", ad_sortfield=450;

ALTER TABLE prefix_Post ADD FULLTEXT INDEX posts(p_text,p_title);

DROP TABLE prefix_Log;

CREATE TABLE prefix_LogSession (
  sid_id INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  sid CHAR(32),
  uo_ip INTEGER UNSIGNED NULL,
  uo_maxuid INTEGER UNSIGNED NULL,
  uo_curid INTEGER UNSIGNED NULL,
  uo_lasttime INTEGER UNSIGNED NULL,
  PRIMARY KEY (sid_id),
  INDEX timekey(uo_lasttime)
);

CREATE TABLE prefix_LogEntry (
  uo_id INTEGER(10) UNSIGNED NOT NULL,
  uo_tid INTEGER(10) UNSIGNED NOT NULL,
  uo_fid INTEGER(10) UNSIGNED NOT NULL,
  uo_action VARCHAR(20) NOT NULL,
  uo_module VARCHAR(20) NOT NULL,
  uo_mode TINYINT UNSIGNED NOT NULL,
  uo_time INTEGER(10) UNSIGNED NOT NULL,
  INDEX sid(uo_id),
  INDEX timekey(uo_time)
);

DELETE FROM prefix_AdminEntry WHERE ad_name="MSG_ad_statbrowser";

UPDATE prefix_Language SET ln_locale="ru_RU.cp1251", ln_charset="windows-1251" WHERE ln_file="ru";

ALTER TABLE prefix_Topic ADD INDEX last(t__lastpostid);

CREATE TABLE prefix_Search (
  sr_id INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  sr_text VARCHAR(255) NOT NULL,
  sr_mode TINYINT UNSIGNED NOT NULL,
  sr_type TINYINT UNSIGNED NOT NULL,
  sr_starttime INT UNSIGNED NOT NULL,
  sr_endtime INT UNSIGNED NOT NULL,
  PRIMARY KEY(sr_id)
) TYPE=MyISAM;

CREATE TABLE prefix_SearchResult (
  srid INTEGER(10) UNSIGNED NOT NULL,
  srpid INTEGER(10) UNSIGNED NOT NULL,
  relevancy FLOAT NOT NULL,
  PRIMARY KEY (srid,srpid) 
) TYPE=MyISAM;

ALTER TABLE prefix_User ADD COLUMN u_pmnotify TINYINT UNSIGNED NOT NULL DEFAULT 1;

ALTER TABLE prefix_LastVisit ADD COLUMN lv_markall INTEGER(10) UNSIGNED NOT NULL, ADD COLUMN lv_markcount INTEGER(10) UNSIGNED NOT NULL;

UPDATE prefix_LastVisit SET lv_markall=lv_time2;

DELETE FROM prefix_TopicView;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_losttopic", ad_category="MSG_cat_actions", ad_url="index.php\?m=forum&a=f_losttopic", ad_sortfield=460;

ALTER TABLE prefix_File ADD COLUMN file_size INTEGER UNSIGNED NOT NULL;
ALTER TABLE prefix_User ADD COLUMN u__pmtime INT UNSIGNED NOT NULL;
ALTER TABLE prefix_User ADD COLUMN u_realname VARCHAR(60) NOT NULL;
ALTER TABLE prefix_Forum ADD COLUMN f_hidden TINYINT UNSIGNED NOT NULL;
ALTER TABLE prefix_Forum ADD COLUMN f_nosubs TINYINT UNSIGNED NOT NULL;
ALTER TABLE prefix_ForumType ADD COLUMN tp_external TINYINT UNSIGNED NOT NULL;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_fileconvert", ad_category="MSG_cat_actions", ad_url="index.php\?m=basic&a=convert_files", ad_sortfield=398;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_photoconvert", ad_category="MSG_cat_actions", ad_url="index.php\?m=basic&a=convert_photos", ad_sortfield=399;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_modes", ad_category="MSG_cat_settings", ad_url="index.php\?m=basic&a=opt_edit2", ad_sortfield=301;
INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_graphics", ad_category="MSG_cat_settings", ad_url="index.php\?m=basic&a=opt_edit3", ad_sortfield=302;
ALTER TABLE prefix_User ADD COLUMN u_nosigns TINYINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE prefix_User ADD COLUMN u_prevmsgs TINYINT UNSIGNED NOT NULL DEFAULT 5;

ALTER TABLE prefix_UserLevel ADD COLUMN l_pic VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE prefix_Forum ADD COLUMN f_lpremod SMALLINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE prefix_Forum ADD COLUMN f_ltopicpremod SMALLINT UNSIGNED NOT NULL DEFAULT 0;

UPDATE prefix_Forum SET f_lpremod=f_lmoderate*f_premoderate;

ALTER TABLE prefix_Forum DROP COLUMN f_premoderate;

ALTER TABLE prefix_Forum ADD COLUMN f_link VARCHAR(32) NOT NULL DEFAULT '';

ALTER TABLE prefix_Topic ADD COLUMN t_link VARCHAR(32) NOT NULL DEFAULT '';

CREATE TABLE prefix_Online (
  o_uid INTEGER UNSIGNED NOT NULL DEFAULT 1,
  o_key CHAR(32) NOT NULL DEFAULT '',
  o_udata TEXT,
  PRIMARY KEY (o_uid,o_key)
) TYPE=MyISAM;

CREATE TABLE prefix_Present (
  pu_uid INTEGER UNSIGNED NOT NULL DEFAULT 1,
  pu_ip INTEGER UNSIGNED NOT NULL DEFAULT 0,
  pu_uname VARCHAR(32) NOT NULL DEFAULT '',
  pu_lasttime INTEGER UNSIGNED NOT NULL DEFAULT 0,
  pu_action VARCHAR(20) NOT NULL DEFAULT '',
  pu_module VARCHAR(20) NOT NULL DEFAULT '',
  pu_tid INTEGER UNSIGNED NOT NULL DEFAULT 0,
  pu_fid INTEGER UNSIGNED NOT NULL DEFAULT 0,
  pu_hits INTEGER UNSIGNED NOT NULL DEFAULT 0,
  pu_hidden TINYINT UNSIGNED NOT NULL DEFAULT 0,  
  INDEX uid(pu_uid,pu_ip),
  INDEX lasttime(pu_lasttime)
) TYPE=MyISAM;

ALTER TABLE prefix_Topic CHANGE COLUMN t_title t_title VARCHAR(80) NOT NULL;

ALTER TABLE prefix_Forum CHANGE COLUMN f_title f_title VARCHAR(80) NOT NULL;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_boardcodes", ad_category="MSG_cat_actions", ad_url="index.php?m=basic&a=edit_bcode", ad_sortfield=450;

CREATE TABLE prefix_Draft (
  dr_uid INTEGER UNSIGNED NOT NULL DEFAULT 0,
  dr_fid INTEGER UNSIGNED NOT NULL DEFAULT 0,
  dr_tid INTEGER UNSIGNED NOT NULL DEFAULT 0,
  dr_text LONGTEXT,
  PRIMARY KEY (dr_uid,dr_fid,dr_tid)
) TYPE=MyISAM;

ALTER TABLE prefix_Topic DROP INDEX T_stickykey, ADD INDEX PCount (t__pcount);

ALTER TABLE prefix_Search ADD COLUMN sr_uname VARCHAR(32) NOT NULL DEFAULT '';

ALTER TABLE prefix_StyleSet ADD COLUMN st_integrated TINYINT NOT NULL DEFAULT 0;

UPDATE prefix_StyleSet SET st_integrated=1 WHERE st_id<=3;

ALTER TABLE prefix_Post ADD INDEX attach (p_attach);

UPDATE prefix_ForumType SET tp_library="link" WHERE tp_id=6;

CREATE TABLE prefix_TopicVC (
  tid INTEGER UNSIGNED NOT NULL,
  t__views MEDIUMINT UNSIGNED NOT NULL,
  PRIMARY KEY(tid)
) Type=MyISAM;

INSERT INTO prefix_TopicVC (tid,t__views) SELECT t_id, t__views FROM prefix_Topic;

ALTER TABLE prefix_Topic DROP COLUMN t__views;

ALTER TABLE prefix_Topic ADD COLUMN t__lasttime INTEGER UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE prefix_Topic DROP KEY XIF32Topic, ADD KEY XIF32Topic (t_fid,t__pcount,t__lastpostid);


ALTER TABLE prefix_Topic ADD KEY lastpost (t__lastpostid,t_fid,t__pcount);


CREATE TABLE prefix_ForumVC (
  fid INTEGER UNSIGNED NOT NULL,
  f__views MEDIUMINT UNSIGNED NOT NULL,
  PRIMARY KEY(fid)
) Type=MyISAM;


INSERT INTO prefix_ForumVC (fid,f__views) SELECT f_id, f__views FROM prefix_Forum;

ALTER TABLE prefix_Forum DROP COLUMN f__views;

ALTER TABLE prefix_User ADD COLUMN u__blog_fid INTEGER UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE prefix_User ADD INDEX blog_fid (u__blog_fid);

ALTER TABLE prefix_User ADD COLUMN u__gallery_fid INTEGER UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE prefix_User ADD COLUMN u_goto TINYINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE prefix_User ADD COLUMN u_firstpost TINYINT UNSIGNED NOT NULL DEFAULT 1;

ALTER TABLE prefix_User ADD COLUMN u__canonical VARCHAR(32) NOT NULL DEFAULT '';

ALTER TABLE prefix_User ADD COLUMN u_showenemies TINYINT UNSIGNED NOT NULL DEFAULT 1;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_canonize", ad_category="MSG_cat_user", ad_url="index.php\?m=user&a=canonize", ad_sortfield=270;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_banip", ad_category="MSG_cat_settings", ad_url="index.php\?m=basic&a=edit_ip", ad_sortfield=330;

INSERT INTO prefix_AdminEntry SET ad_name="MSG_ad_blog_gal_settings", ad_category="MSG_cat_forum", ad_url="index.php\?m=basic&a=opt_edit4", ad_sortfield=130;

CREATE TABLE prefix_AddrBook (
u_owner INTEGER UNSIGNED NOT NULL,
u_partner INTEGER UNSIGNED NOT NULL,
u_status TINYINT NOT NULL,
PRIMARY KEY(u_owner,u_partner)
) Type=MyISAM;

INSERT INTO prefix_ForumType SET tp_id=11, tp_title="MSG_tp_blog", tp_library="blog", tp_template="blog", tp_modlib="blog", tp_searchable=1, tp_container=0, tp_menu=0;

INSERT INTO prefix_ForumType SET tp_id=12, tp_title="MSG_tp_gallery", tp_library="gallery", tp_template="gallery", tp_modlib="gallery", tp_searchable=1, tp_container=0, tp_menu=0;

INSERT INTO prefix_UserLevel (l_level,l_title,l_minpost,l_custom) VALUES (999,"Владелец форума/галереи",0,1);

ALTER TABLE prefix_Post ADD INDEX tidkey (p_tid,p__premoderate,p__time);

ALTER TABLE prefix_User ADD INDEX regdate (u__regdate);

ALTER TABLE prefix_User ADD INDEX gallery_fid (u__gallery_fid);

CREATE TABLE prefix_ForumIgnore(
  uid INTEGER NOT NULL,
  fid INTEGER NOT NULL,
  PRIMARY KEY (uid,fid)
) Type=MyISAM;

INSERT INTO prefix_ForumType SET tp_id=13, tp_title="MSG_tp_dynpage", tp_library="dynpage", tp_template="dynpage", tp_modlib="dynpage", tp_searchable=0, tp_container=1, tp_menu=1;

ALTER TABLE prefix_File ADD COLUMN file_downloads INTEGER UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE prefix_File ADD COLUMN file_key INTEGER UNSIGNED NOT NULL DEFAULT 0;

CREATE TABLE prefix_RSSImports(
  rss_id INTEGER NOT NULL AUTO_INCREMENT,
  rss_url VARCHAR(255) NOT NULL DEFAULT '',
  rss_lastget INTEGER NOT NULL DEFAULT 0,
  rss_lastentry INTEGER NOT NULL DEFAULT 0,
  rss_fid INTEGER NOT NULL DEFAULT 0,
  rss_premoderated TINYINT NOT NULL DEFAULT 1,
  rss_name VARCHAR(255) NOT NULL DEFAULT '',
  rss_link VARCHAR(255) NOT NULL DEFAULT '',
  rss_user VARCHAR(255) NOT NULL DEFAULT '',
  rss_source VARCHAR(255) NOT NULL  DEFAULT '',
  PRIMARY KEY(rss_id)
) Type=MyISAM;

ALTER TABLE prefix_BadWord ADD COLUMN w_onlyname TINYINT NOT NULL DEFAULT 0;
