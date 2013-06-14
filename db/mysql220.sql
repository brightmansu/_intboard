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
