ALTER TABLE prefix_User ADD COLUMN u_nosigns TINYINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE prefix_User ADD COLUMN u_prevmsgs TINYINT UNSIGNED NOT NULL DEFAULT 5;

ALTER TABLE prefix_UserLevel ADD COLUMN l_pic VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE prefix_Forum ADD COLUMN f_lpremod SMALLINT NOT NULL DEFAULT 0;

ALTER TABLE prefix_Forum ADD COLUMN f_ltopicpremod SMALLINT NOT NULL DEFAULT 0;

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