CREATE TABLE prefix_ForumIgnore(
  uid INTEGER NOT NULL,
  fid INTEGER NOT NULL,
  CONSTRAINT prefix_ForumIgnore PRIMARY KEY (uid,fid)
) WITHOUT OIDS;

INSERT INTO prefix_ForumType SET tp_id=13, tp_title="MSG_tp_dynpage", tp_library="dynpage", tp_template="dynpage", tp_modlib="dynpage", tp_searchable=0, tp_container=1, tp_menu=1;

ALTER TABLE prefix_File ADD COLUMN file_downloads INTEGER NOT NULL DEFAULT 0;

ALTER TABLE prefix_File ADD COLUMN file_key INTEGER NOT NULL DEFAULT 0;

CREATE TABLE prefix_RSSImports(
  rss_id INTEGER NOT NULL AUTO_INCREMENT,
  rss_url CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  rss_lastget INTEGER NOT NULL DEFAULT 0,
  rss_lastentry INTEGER NOT NULL DEFAULT 0,
  rss_fid INTEGER NOT NULL DEFAULT 0,
  rss_premoderated TINYINT NOT NULL DEFAULT 1,
  rss_name CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  rss_link CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  rss_user CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  rss_source CHARACTER VARYING(255) NOT NULL  DEFAULT ''::character varying,
  CONSTRAINT prefix_RSSImports PRIMARY KEY(rss_id)
) WITHOUT OIDS;

ALTER TABLE prefix_BadWord ADD COLUMN w_onlyname SMALLINT NOT NULL DEFAULT 0;
