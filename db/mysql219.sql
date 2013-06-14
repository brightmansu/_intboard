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
