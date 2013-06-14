ALTER TABLE prefix_user ADD COLUMN u__noedit smallint NOT NULL DEFAULT 0;
ALTER TABLE prefix_user ADD COLUMN u_nosigns smallint NOT NULL DEFAULT 0;
ALTER TABLE prefix_user ADD COLUMN u_prevmsgs smallint NOT NULL DEFAULT 5;

ALTER TABLE prefix_userLevel ADD COLUMN l_pic character varying(255) NOT NULL  DEFAULT ''::character varying;

ALTER TABLE prefix_forum ADD COLUMN f_lpremod smallint NOT NULL DEFAULT 0;
ALTER TABLE prefix_forum ADD COLUMN f_ltopicpremod smallint NOT NULL DEFAULT 0;
UPDATE prefix_forum SET f_lpremod=f_lmoderate*f_premoderate;

ALTER TABLE prefix_forum ADD COLUMN f_link character varying(32) NOT NULL  DEFAULT ''::character varying;

ALTER TABLE prefix_topic ADD COLUMN t_link character varying(32) NOT NULL  DEFAULT ''::character varying;

CREATE TABLE prefix_online (
  o_uid integer NOT NULL DEFAULT 1,
  o_key character varying(32)  NOT NULL  DEFAULT ''::character varying,
  o_udata text,
CONSTRAINT prefix_online_pkey PRIMARY KEY (o_uid,o_key)
) WITHOUT OIDS;

CREATE TABLE prefix_present (
  pu_uid integer NOT NULL DEFAULT 1,
  pu_ip bigint NOT NULL DEFAULT 0,
  pu_uname character varying(32)  NOT NULL  DEFAULT ''::character varying,
  pu_lasttime bigint NOT NULL DEFAULT 0,
  pu_action character varying(20)  NOT NULL  DEFAULT ''::character varying,
  pu_module character varying(20)  NOT NULL  DEFAULT ''::character varying,
  pu_tid integer NOT NULL DEFAULT 0,
  pu_fid integer NOT NULL DEFAULT 0,
  pu_hits integer NOT NULL DEFAULT 0,
  pu_hidden smallint NOT NULL DEFAULT 0
) WITHOUT OIDS;
CREATE INDEX prefix_uid ON prefix_present USING btree (pu_uid);
CREATE INDEX prefix_lasttime ON prefix_present USING btree (pu_lasttime);
                                                    
ALTER TABLE prefix_topic ALTER COLUMN t_title TYPE VARCHAR(80);

ALTER TABLE prefix_forum ALTER COLUMN f_title TYPE VARCHAR(80);

INSERT INTO prefix_adminentry SET ad_name="MSG_ad_boardcodes", ad_category="MSG_cat_actions", ad_url="index.php?m=basic&a=edit_bcode", ad_sortfield=450;

CREATE TABLE prefix_draft (
  dr_uid integer NOT NULL DEFAULT 0,
  dr_fid integer NOT NULL DEFAULT 0,
  dr_tid integer NOT NULL DEFAULT 0,
  dr_text text,
CONSTRAINT prefix_draft_pkey PRIMARY KEY (dr_uid,dr_fid,dr_tid)
) WITHOUT OIDS;

DROP INDEX prefix_t_stickykey;
CREATE INDEX prefix_t_pcount ON prefix_topic USING btree (t__pcount);

ALTER TABLE prefix_search ADD COLUMN st_uname character varying(32) NOT NULL  DEFAULT ''::character varying;

ALTER TABLE prefix_StyleSet ADD COLUMN st_integrated smallint NOT NULL DEFAULT 0;
UPDATE prefix_StyleSet SET st_integrated=1 WHERE st_id<=3;

ALTER TABLE prefix_Post ADD INDEX attach (p_attach);

UPDATE prefix_ForumType SET tp_library="link" WHERE tp_id=6;