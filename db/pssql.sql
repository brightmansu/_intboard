CREATE TABLE prefix_Addon (
  a_name CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  a_ver SMALLINT NOT NULL DEFAULT 0,
  a_fullname CHARACTER VARYING(80) NOT NULL DEFAULT ''::character varying,
  a_descr CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  CONSTRAINT prefix_Addon PRIMARY KEY(a_name, a_ver)
)  WITHOUT OIDS;

CREATE TABLE prefix_AdminEntry (
  ad_name CHARACTER VARYING(40) NOT NULL DEFAULT ''::character varying,
  ad_category CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  ad_url CHARACTER VARYING(128) NOT NULL DEFAULT ''::character varying,
  ad_sortfield SMALLINT NOT NULL DEFAULT 0,
  CONSTRAINT prefix_AdminEntry PRIMARY KEY(ad_name),
)  WITHOUT OIDS;

CREATE INDEX SORT ON prefix_AdminEntry USING BTREE (ad_sortfield);

CREATE TABLE prefix_Article (
  a_tid BIGINT  NOT NULL DEFAULT 0,
  a_author CHARACTER VARYING(64) NOT NULL DEFAULT ''::character varying,
  a_authormail CHARACTER VARYING(48) NOT NULL DEFAULT ''::character varying,
  a_origin CHARACTER VARYING(128) NOT NULL DEFAULT ''::character varying,
  a_originurl CHARACTER VARYING(128) NOT NULL DEFAULT ''::character varying,
  a_disc_tid BIGINT  NULL DEFAULT 0,
  CONSTRAINT prefix_Article PRIMARY KEY(a_tid)
)  WITHOUT OIDS;

CREATE TABLE prefix_BadWord (
  w_id SERIAL,
  w_bad CHARACTER(32) NOT NULL DEFAULT ''::character varying,
  w_good CHARACTER(32) NOT NULL DEFAULT ''::character varying,
  w_onlyname SMALLINT NOT NULL DEFAULT 0,
  CONSTRAINT prefix_BadWord PRIMARY KEY(w_id)
)  WITHOUT OIDS;

CREATE TABLE prefix_Bookmark (
  uid BIGINT  NOT NULL DEFAULT 0,
  tid BIGINT  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_Bookmark PRIMARY KEY(uid, tid),
)  WITHOUT OIDS;

CREATE INDEX XIF40Bookmark ON prefix_Bookmark USING BTREE (tid);

CREATE TABLE prefix_Category (
  ct_id SERIAL,
  ct_name CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,
  ct_sortfield BIGINT NOT NULL DEFAULT 0,
  CONSTRAINT prefix_Category PRIMARY KEY(ct_id)
)  WITHOUT OIDS;

CREATE INDEX XPKSort ON prefix_Category USING BTREE(ct_sortfield);

CREATE TABLE prefix_Code (
  sid CHARACTER(32) NOT NULL DEFAULT ''::character varying,
  code CHARACTER(8) NOT NULL DEFAULT ''::character varying,
  time BIGINT  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_Code PRIMARY KEY(sid)
)  WITHOUT OIDS;

CREATE TABLE prefix_Download (
  dl_tid BIGINT  NOT NULL DEFAULT 0,
  dl_url CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  dl_homepage CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  dl__downloads BIGINT NOT NULL DEFAULT 0,
  dl_disc_tid BIGINT  NOT NULL DEFAULT 0,
  dl_size CHARACTER VARYING(10) NOT NULL DEFAULT ''::character varying,
  CONSTRAINT prefix_Download PRIMARY KEY(dl_tid)
)  WITHOUT OIDS;

CREATE INDEX XPKDownload ON prefix_Download USING BTREE (dl_tid);

CREATE TABLE prefix_File (
  file_id SERIAL,
  file_size INTEGER  NOT NULL DEFAULT 0,
  file_type CHARACTER VARYING(64) NOT NULL DEFAULT ''::character varying,
  file_name CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,
  file_downloads INTEGER NOT NULL DEFAULT 0,
  file_key INTEGER NOT NULL DEFAULT 0,
  CONSTRAINT prefix_File PRIMARY KEY(file_id)
)  WITHOUT OIDS;

CREATE TABLE prefix_Forum (
  f_id SERIAL,
  f_tpid SMALLINT NOT NULL DEFAULT 0,
  f_ctid BIGINT  NOT NULL DEFAULT 0,
  f_title CHARACTER VARYING(80) NOT NULL DEFAULT ''::character varying,
  f_descr CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  f_status  NOT NULL DEFAULT 0,
  f_lview SMALLINT NOT NULL DEFAULT 0,
  f_lread SMALLINT NOT NULL DEFAULT 0,
  f_sortfield BIGINT NOT NULL DEFAULT 0,
  f_lpost SMALLINT NOT NULL DEFAULT 0,
  f_ltopic SMALLINT NOT NULL DEFAULT 0,
  f_ledit SMALLINT NOT NULL DEFAULT 0,
  f_lhtml SMALLINT NOT NULL DEFAULT 0,
  f_lattach SMALLINT NOT NULL DEFAULT 0,
  f_lvote SMALLINT NOT NULL DEFAULT 0,
  f_lmoderate SMALLINT NOT NULL DEFAULT 0,
  f_lip SMALLINT NOT NULL DEFAULT 0,
  f_lpoll SMALLINT NOT NULL DEFAULT 0,
  f_lsticky SMALLINT NOT NULL DEFAULT 0,
  f_rate  NOT NULL DEFAULT 0,
  f_bcode  NOT NULL DEFAULT 0,
  f_nostats  NOT NULL DEFAULT 0,
  f_rules TEXT,
  f_smiles  NOT NULL DEFAULT 0,
  f_attachpics  NOT NULL DEFAULT 0,
  f_selfmod  NOT NULL DEFAULT 0,
  f_parent BIGINT NOT NULL DEFAULT 0,
  f_text MEDIUMTEXT,
  f_url CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  f_nonewpic CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  f_newpic CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  f_premoderate  NOT NULL DEFAULT 0,
  f_lnid BIGINT  NOT NULL DEFAULT 0,
  f_downloads BIGINT NOT NULL DEFAULT 0,
  f_update BIGINT  NOT NULL DEFAULT 0,
  f__tcount INT  NOT NULL DEFAULT 0,
  f__pcount INT  NOT NULL DEFAULT 0,
  f__lastpostid INT  NOT NULL DEFAULT 0,
  f__startpostid INT  NOT NULL DEFAULT 0,
  f__premodcount INT  NOT NULL DEFAULT 0,
  f_hidden TINYINT  NOT NULL DEFAULT 0,
  f_nosubs TINYINT  NOT NULL DEFAULT 0,
  f_lpremod SMALLINT NOT NULL DEFAULT 0,
  f_ltopicpremod SMALLINT NOT NULL DEFAULT 0,
  f_link CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,

  CONSTRAINT prefix_Froum PRIMARY KEY(f_id,f_lview,f_premoderate)
)  WITHOUT OIDS;

CREATE INDEX ctid ON prefix_Forum USING BTREE (f_ctid);

CREATE INDEX sortfield ON prefix_Forum USING BTREE (f_sortfield);

CREATE INDEX parent ON prefix_Forum USING BTREE (f_parent);

CREATE INDEX tpid ON prefix_Forum USING BTREE (f_tpid);

CREATE TABLE prefix_ForumType (
  tp_id SMALLINT NOT NULL DEFAULT 0,
  tp_title CHARACTER VARYING(40) NOT NULL DEFAULT ''::character varying,
  tp_library CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  tp_template CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  tp_modlib CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  tp_searchable  NOT NULL DEFAULT 0,
  tp_container  NOT NULL DEFAULT 0,
  tp_menu  NOT NULL DEFAULT 0,
  tp_external TINYINT  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_ForumType PRIMARY KEY(tp_id)
)  WITHOUT OIDS;

CREATE TABLE prefix_ForumView (
  uid INT  NOT NULL DEFAULT 0,
  fid INT  NOT NULL DEFAULT 0,
  fv_count INT  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_ForumView PRIMARY KEY (fid,uid)
)  WITHOUT OIDS;

CREATE INDEX uids ON prefix_ForumView USING BTREE (uid);

CREATE TABLE prefix_ForumVC (
  fid INTEGER  NOT NULL,
  f__views MEDIUMINT  NOT NULL,
  CONSTRAINT prefix_ForumVC PRIMARY KEY(fid)
)  WITHOUT OIDS;

CREATE TABLE prefix_Language (
  ln_id SERIAL,
  ln_name CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  ln_file CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  ln_locale CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  ln_charset CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  CONSTRAINT prefix_Language PRIMARY KEY(ln_id)
)  WITHOUT OIDS;

CREATE TABLE prefix_LastVisit (
  uid BIGINT  NOT NULL DEFAULT 0,
  fid BIGINT  NOT NULL DEFAULT 0,
  lv_time1 BIGINT  NOT NULL DEFAULT 0,
  lv_time2 BIGINT  NOT NULL DEFAULT 0,
  lv_markall BIGINT  NOT NULL DEFAULT 0,
  lv_markcount BIGINT  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_LastVisit PRIMARY KEY(uid, fid)
)  WITHOUT OIDS;

CREATE INDEX XIF62LastVisit ON prefix_LastVisit USING BTREE (fid);

CREATE TABLE prefix_PersonalMessage (
  pm_id SERIAL,
  pm__box  NOT NULL DEFAULT 0,
  pm__owner INTEGER  NULL DEFAULT 0,
  pm__correspondent BIGINT  NOT NULL DEFAULT 0,
  pm__senddate BIGINT  NOT NULL DEFAULT 0,
  pm__readdate BIGINT  NOT NULL DEFAULT 0,
  pm_text MEDIUMTEXT NOT NULL,
  pm_signature  NOT NULL DEFAULT 0,
  pm_smiles  NOT NULL DEFAULT 0,
  pm__html  NOT NULL DEFAULT 0,
  pm_bcode  NOT NULL DEFAULT 0,
  pm_pair BIGINT NULL DEFAULT 0,
  pm_subj CHARACTER VARYING(80) NOT NULL DEFAULT ''::character varying,
  CONSTRAINT prefix_PersonalMessage PRIMARY KEY(pm_id)
)  WITHOUT OIDS;

CREATE INDEX owner ON prefix_PersonalMessage USING BTREE (pm__owner, pm__box);

CREATE TABLE prefix_Photo (
  ph_id INTEGER  NOT NULL AUTO_INCREMENT,
  ph_tid INTEGER  NULL DEFAULT 0,
  ph_key CHARACTER(8) NULL,
  CONSTRAINT prefix_Photo PRIMARY KEY(ph_id)
)  WITHOUT OIDS;

prefix_Photo CREATE INDEX Photo_TID ON UNIQUE USING BTREE (ph_tid);

CREATE TABLE prefix_Poll (
  pl_id SERIAL,
  pl_tid BIGINT  NOT NULL DEFAULT 0,
  pl_title CHARACTER VARYING(60) NOT NULL DEFAULT ''::character varying,
  pl_enddate BIGINT  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_Poll PRIMARY KEY(pl_id)
)  WITHOUT OIDS;

CREATE INDEX PL_topicid ON prefix_Poll USING BTREE (pl_tid);

CREATE TABLE prefix_PollVariant (
  pv_id SERIAL,
  pv_plid BIGINT  NOT NULL DEFAULT 0,
  pv_text CHARACTER VARYING(80) NOT NULL,
  pv_count INT  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_PollVariant PRIMARY KEY(pv_id)
)  WITHOUT OIDS;

CREATE INDEX XIF47PollVariant ON prefix_PollVariant USING BTREE (pv_plid);

CREATE TABLE prefix_Post (
  p_id SERIAL,
  p_tid BIGINT  NOT NULL DEFAULT 0,
  p_uid BIGINT NOT NULL DEFAULT 0,
  p_text MEDIUMTEXT NOT NULL,
  p__modcomment TEXT NULL,
  p__time BIGINT  NOT NULL DEFAULT 0,
  p__edittime BIGINT  NOT NULL DEFAULT 0,
  p_signature  NOT NULL DEFAULT 0,
  p__smiles  NOT NULL DEFAULT 0,
  p__bcode  NOT NULL DEFAULT 0,
  p__html  NOT NULL DEFAULT 0,
  p_attach BIGINT NOT NULL DEFAULT 0,
  p_uname CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,
  p__ip BIGINT  NOT NULL DEFAULT 0,
  p_title CHARACTER VARYING(64) NOT NULL DEFAULT ''::character varying,
  p__premoderate  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_post_pkey PRIMARY KEY(p_id,p_uid,p__premoderate)
)  WITHOUT OIDS;

CREATE INDEX prefix_fulltext_idx ON prefix_Post USING gist (fulltext_idx);

CREATE INDEX prefix_tid ON prefix_Post USING btree (p_tid, p__premoderate);

CREATE INDEX prefix_time ON prefix_Post USING btree (p__time);

CREATE INDEX prefix_uid ON prefix_Post USING btree (p_uid);

CREATE INDEX prefix_attach ON prefix_Post USING btree (p_attach);

CREATE TRIGGER tg_fulltext_prefix_Post BEFORE INSERT OR UPDATE ON prefix_post FOR EACH ROW EXECUTE PROCEDURE tsearch2('fulltext_idx', 'p_text', 'p_title');

CREATE TABLE prefix_Smile (
  sm_code CHARACTER(12) NOT NULL DEFAULT ''::character varying,
  sm_file CHARACTER(20) NULL,
  sm_show TINYINT  NOT NULL DEFAULT 1,
  CONSTRAINT prefix_Smile PRIMARY KEY(sm_code)
)  WITHOUT OIDS;

CREATE INDEX XPKSmile ON prefix_Smile USING BTREE (sm_code);

CREATE TABLE prefix_StyleSet (
  st_id SERIAL,
  st_name CHARACTER VARYING(40) NOT NULL DEFAULT ''::character varying,
  st_file CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  st_show  NOT NULL DEFAULT 1,
  st_parent CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  st_integrated TINYINT NOT NULL DEFAULT 1,
  CONSTRAINT prefix_StyleSet PRIMARY KEY(st_id)
)  WITHOUT OIDS;

CREATE TABLE prefix_Subscription (
  uid BIGINT  NOT NULL DEFAULT 0,
  tid BIGINT  NOT NULL DEFAULT 0,
  fid BIGINT NOT NULL DEFAULT 0,
  CONSTRAINT prefix_Subscription PRIMARY KEY(uid, tid, fid)
)  WITHOUT OIDS;

CREATE INDEX uid ON prefix_Subscription USING BTREE (uid);

CREATE INDEX tid ON prefix_Subscription USING BTREE (tid);

CREATE TABLE prefix_Topic (
  t_id SERIAL,
  t_fid BIGINT  NOT NULL DEFAULT 0,
  t_title CHARACTER VARYING(80) NOT NULL DEFAULT ''::character varying,
  t_descr CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  t__sticky  NOT NULL DEFAULT 0,
  t__stickypost  NOT NULL DEFAULT 0,
  t__status  NOT NULL DEFAULT 0,
  t__rate  NOT NULL DEFAULT 0,
  t__pcount INT  NOT NULL DEFAULT 0,
  t__startpostid INT  NOT NULL DEFAULT 0,
  t__lastpostid INT  NOT NULL DEFAULT 0,
  t__ratingsum INT  NOT NULL DEFAULT 0,
  t__ratingcount INT  NOT NULL DEFAULT 0,
  t_link CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,
  t__lasttime INTEGER  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_Topic PRIMARY KEY(t_id),
)  WITHOUT OIDS;

CREATE INDEX XIF32Topic ON prefix_Topic USING BTREE (t_fid,t__pcount,t__lastpostid);

CREATE INDEX lastpost ON prefix_Topic USING BTREE (t__lastpostid,t_fid,t__pcount);

CREATE TRIGGER tg_fulltxt_prefix_topic BEFORE INSERT OR UPDATE ON prefix_topic FOR EACH ROW EXECUTE PROCEDURE tsearch2('fulltext_idx', 't_title', 't_descr');


CREATE TABLE prefix_TopicRate (
  tid BIGINT  NOT NULL DEFAULT 0,
  uid BIGINT  NOT NULL DEFAULT 0,
  tr_value  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_TopicRate PRIMARY KEY(tid, uid)
)  WITHOUT OIDS;

CREATE INDEX XIF42TopicRate ON prefix_TopicRate USING BTREE (uid);

CREATE TABLE prefix_TopicView (
  tid BIGINT  NOT NULL DEFAULT 0,
  uid BIGINT  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_TopicView PRIMARY KEY(tid, uid)
)  WITHOUT OIDS;

CREATE INDEX XIF35TopicView ON prefix_TopicView USING BTREE (uid);

CREATE TABLE prefix_TopicVC (
  tid INTEGER  NOT NULL,
  t__views MEDIUMINT  NOT NULL,
  CONSTRAINT prefix_TopicVC PRIMARY KEY(tid)
)  WITHOUT OIDS;

CREATE TABLE prefix_UGroup (
  g_id SERIAL,
  g_title CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  g_setlevel SMALLINT NOT NULL DEFAULT 0,
  g_ljoin SMALLINT NOT NULL DEFAULT 0,
  g_lview SMALLINT NOT NULL DEFAULT 0,
  g_lautojoin SMALLINT NOT NULL DEFAULT 0,
  g_descr CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  g_allowquit  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_UGroup PRIMARY KEY(g_id)
)  WITHOUT OIDS;

CREATE TABLE prefix_UGroupAccess (
  gid BIGINT  NOT NULL DEFAULT 0,
  fid BIGINT  NOT NULL DEFAULT 0,
  ga_level SMALLINT NULL DEFAULT 0,
  CONSTRAINT prefix_UGroupAccess PRIMARY KEY(gid, fid)
)  WITHOUT OIDS;

CREATE INDEX XIF44GroupAccess ON prefix_UGroupAccess USING BTREE (fid);

CREATE INDEX XIF51GroupAccess ON prefix_UGroupAccess USING BTREE (ga_level);

CREATE TABLE prefix_UGroupMember (
  gid BIGINT  NOT NULL DEFAULT 0,
  uid BIGINT  NOT NULL DEFAULT 0,
  gm_status  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_UGroupMember PRIMARY KEY(gid, uid)
)  WITHOUT OIDS;

CREATE INDEX XIF28GroupMember ON prefix_UGroupMember USING BTREE (uid);

CREATE TABLE prefix_User (
  u_id INTEGER  NOT NULL AUTO_INCREMENT,
  u__email CHARACTER VARYING(48) NOT NULL DEFAULT ''::character varying,
  u_lnid BIGINT  NOT NULL DEFAULT 0,
  u_stid BIGINT  NOT NULL DEFAULT 0,
  u__level SMALLINT NOT NULL DEFAULT 0,
  u__name CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,
  u__active  NOT NULL DEFAULT 0,
  u__password CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,
  u_usesignature  NOT NULL DEFAULT 0,
  u_showmail  NOT NULL DEFAULT 0,
  u_usesmiles  NOT NULL DEFAULT 0,
  u_signature CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  u_gender  NOT NULL DEFAULT 0,
  u_sformat CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  u_lformat CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  u_location CHARACTER VARYING(40) NOT NULL DEFAULT ''::character varying,
  u_bday TINYINT  NOT NULL DEFAULT 0,
  u_bmonth TINYINT  NOT NULL DEFAULT 0,
  u_byear SMALLINT  NOT NULL DEFAULT 0,
  u_bmode TINYINT  NOT NULL DEFAULT 0,
  u_tperpage SMALLINT NOT NULL DEFAULT 0,
  u_mperpage SMALLINT NOT NULL DEFAULT 0,
  u_homepage CHARACTER VARYING(128) NOT NULL DEFAULT ''::character varying,
  u_showavatars  NOT NULL DEFAULT 0,
  u_avatartype  NOT NULL DEFAULT 0,
  u__avatar CHARACTER VARYING(128) NOT NULL DEFAULT ''::character varying,
  u__pavatar_id BIGINT  NOT NULL DEFAULT 0,
  u__photo_id BIGINT  NOT NULL DEFAULT 0,
  u_encrypted  NOT NULL DEFAULT 0,
  u__regdate BIGINT  NOT NULL DEFAULT 0,
  u__profileupdate BIGINT  NOT NULL DEFAULT 0,
  u__newpassword CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,
  u__title CHARACTER VARYING(48) NOT NULL DEFAULT ''::character varying,
  u_timeregion BIGINT NOT NULL DEFAULT 0,
  u_icq BIGINT NOT NULL DEFAULT 0,
  u_interests CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  u_hidden  NOT NULL DEFAULT 0,
  u__key CHARACTER VARYING(12) NOT NULL DEFAULT ''::character varying,
  u_detrans  NOT NULL DEFAULT 0,
  u_nomails  NOT NULL DEFAULT 0,
  u_sortposts  NOT NULL DEFAULT 0,
  u_multilang  NOT NULL DEFAULT 0,
  u_diary CHARACTER VARYING(128) NOT NULL DEFAULT ''::character varying,
  u_timelimit SMALLINT NOT NULL DEFAULT 0,
  u_aol CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,
  u_yahoo CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,
  u_msn CHARACTER VARYING(64) NOT NULL DEFAULT ''::character varying,
  u_jabber CHARACTER VARYING(64) NOT NULL DEFAULT ''::character varying,
  u_extform  NOT NULL DEFAULT 1,
  u_aperpage SMALLINT  NOT NULL DEFAULT 0,
  u__rating INT NOT NULL DEFAULT 0,
  u__warnings INT NOT NULL DEFAULT 0,
  u__pmcount INT  NOT NULL DEFAULT 0,
  u__pmtime INT  NOT NULL DEFAULT 0,
  u__warntime INT  NOT NULL DEFAULT 0,
  u__lastlogin INT  NOT NULL DEFAULT 0,
  u_pmnotify TINYINT  NOT NULL DEFAULT 1,
  u_realname CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  u__noedit TINYINT  NOT NULL DEFAULT 0,
  u_nosigns TINYINT  NOT NULL DEFAULT 0,
  u_prevmsgs TINYINT  NOT NULL DEFAULT 5,
  u__blog_fid INTEGER  NOT NULL DEFAULT 0,
  u__gallery_fid INTEGER  NOT NULL DEFAULT 0,
  u_goto TINYINT  NOT NULL DEFAULT 0,
  u_firstpost TINYINT  NOT NULL DEFAULT 1,
  u__canonical CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,
  u_showenemies TINYINT  NOT NULL DEFAULT 1,
  CONSTRAINT prefix_User PRIMARY KEY(u_id)
)  WITHOUT OIDS;

CREATE UNIQUE INDEX gallery_fid ON prefix_User USING BTREE email(u__email);

CREATE UNIQUE INDEX uname ON prefix_User USING BTREE (u__name);

CREATE INDEX level ON prefix_User USING BTREE (u__level);

CREATE INDEX blog_fid ON prefix_User USING BTREE (u__blog_fid);

CREATE INDEX gallery_fid ON prefix_User USING BTREE (u__gallery_fid);  


CREATE TABLE prefix_UserAccess (
  uid BIGINT  NOT NULL DEFAULT 0,
  fid BIGINT  NOT NULL DEFAULT 0,
  ua_level SMALLINT NULL DEFAULT 0,
  CONSTRAINT prefix_UserAccess PRIMARY KEY(uid, fid)
)  WITHOUT OIDS;

CREATE INDEX XIF37UserAccess ON prefix_UserAccess USING BTREE (fid);

CREATE INDEX XIF50UserAccess ON prefix_UserAccess USING BTREE (ua_level);


CREATE TABLE prefix_UserLevel (
  l_level SMALLINT NOT NULL DEFAULT 0,
  l_title CHARACTER VARYING(48) NOT NULL DEFAULT ''::character varying,
  l_minpost BIGINT NULL DEFAULT 0,
  l_custom  NOT NULL DEFAULT 0,
  l_pic CHARACTER VARYING(255) NOT NULL DEFAULT'',
  CONSTRAINT prefix_UserLevel PRIMARY KEY(l_level)
)  WITHOUT OIDS;

CREATE INDEX Minpost ON prefix_UserLevel USING BTREE (l_minpost, l_custom);

CREATE TABLE prefix_UserRating (
  uid BIGINT  NOT NULL DEFAULT 0,
  ur_rated BIGINT  NOT NULL DEFAULT 0,
  ur_time BIGINT  NOT NULL DEFAULT 0,
  ur_value  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_UserRating PRIMARY KEY(uid, ur_rated, ur_time)
)  WITHOUT OIDS;

CREATE TABLE prefix_UserStat (
  uid INT  NOT NULL DEFAULT 0,
  fid INT  NOT NULL DEFAULT 0,
  us_count INT  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_UserStat PRIMARY KEY (fid,uid)
)  WITHOUT OIDS;

CREATE INDEX uids ON prefix_UserStat USING BTREE (uid);

CREATE TABLE prefix_UserWarning (
  uw_id SERIAL,
  uw_uid BIGINT  NOT NULL DEFAULT 0,
  uw_value  NOT NULL DEFAULT 0,
  uw_warner BIGINT  NOT NULL DEFAULT 0,
  uw_validtill BIGINT  NOT NULL DEFAULT 0,
  uw_comment CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  CONSTRAINT prefix_UserWarning PRIMARY KEY(uw_id, uw_uid)
)  WITHOUT OIDS;

CREATE INDEX XIF64UserWarning ON prefix_UserWarning USING BTREE (uw_uid);

CREATE TABLE prefix_Vote (
  pvid BIGINT  NOT NULL DEFAULT 0,
  uid BIGINT  NOT NULL DEFAULT 0,
  tid BIGINT  NOT NULL DEFAULT 0,
  CONSTRAINT prefix_Vote PRIMARY KEY(pvid, uid)
)  WITHOUT OIDS;

CREATE INDEX Topics ON prefix_Vote USING BTREE (tid);

CREATE TABLE prefix_Search (
  sr_id SERIAL,
  sr_text CHARACTER VARYING(255) NOT NULL DEFAULT ''::character varying,
  sr_mode TINYINT  NOT NULL DEFAULT 0,
  sr_type TINYINT  NOT NULL DEFAULT 0,
  sr_starttime INT  NOT NULL DEFAULT 0,
  sr_endtime INT  NOT NULL DEFAULT 0,
  sr_uname CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,
  CONSTRAINT prefix_Search PRIMARY KEY(sr_id)
)  WITHOUT OIDS;

CREATE TABLE prefix_SearchResult (
  srid BIGINT  NOT NULL DEFAULT 0,
  srpid BIGINT  NOT NULL DEFAULT 0,
  relevancy FLOAT NOT NULL DEFAULT 0.0,
  CONSTRAINT prefix_SearchResult PRIMARY KEY (srid,srpid)
)  WITHOUT OIDS;

CREATE TABLE prefix_Online (
  o_uid INTEGER  NOT NULL DEFAULT 1,
  o_key CHARACTER(32) NOT NULL DEFAULT ''::character varying,
  o_udata TEXT,
  CONSTRAINT prefix_Online PRIMARY KEY (o_uid,o_key)
)  WITHOUT OIDS;

CREATE TABLE prefix_Present (
  pu_uid INTEGER  NOT NULL DEFAULT 1,
  pu_ip INTEGER  NOT NULL DEFAULT 0,
  pu_uname CHARACTER VARYING(32) NOT NULL DEFAULT ''::character varying,
  pu_lasttime INTEGER  NOT NULL DEFAULT 0,
  pu_action CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  pu_module CHARACTER VARYING(20) NOT NULL DEFAULT ''::character varying,
  pu_tid INTEGER  NOT NULL DEFAULT 0,
  pu_fid INTEGER  NOT NULL DEFAULT 0,
  pu_hits INTEGER  NOT NULL DEFAULT 0,
  pu_hidden TINYINT  NOT NULL DEFAULT 0
)  WITHOUT OIDS;

CREATE INDEX uid ON prefix_Present USING BTREE (pu_uid);

CREATE INDEX lasttime ON prefix_Present USING BTREE (pu_lasttime);

CREATE TABLE prefix_Draft (
  dr_uid INTEGER  NOT NULL DEFAULT 0,
  dr_fid INTEGER  NOT NULL DEFAULT 0,
  dr_tid INTEGER  NOT NULL DEFAULT 0,
  dr_text LONGTEXT,
  CONSTRAINT prefix_Draft PRIMARY KEY (dr_uid,dr_fid,dr_tid)
)  WITHOUT OIDS;

CREATE TABLE prefix_AddrBook (
   u_owner INTEGER  NOT NULL,
   u_partner INTEGER  NOT NULL,
   u_status TINYINT NOT NULL,
   CONSTRAINT prefix_AddrBook PRIMARY KEY(u_owner,u_partner)
)  WITHOUT OIDS;

CREATE TABLE prefix_ForumIgnore (
  uid INTEGER NOT NULL,
  fid INTEGER NOT NULL,
  CONSTRAINT prefix_ForumIgnore PRIMARY KEY (uid,fid)
) WITHOUT OIDS;

CREATE TABLE prefix_RSSImports (
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

