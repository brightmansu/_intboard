<? function ad_opt_edit($levels,$forums) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1>
<tr><td class="tablehead" colspan=2><?=MSG_opt_params;?>
<tr><td width="50%"><?=MSG_opt_title;?>:
<td><input type=text name=opt_title value="<?=$GLOBALS['opt_title'];?>">
<tr><td><?=MSG_opt_descr;?>:
<td><textarea name=opt_descr rows=4 cols=32><?=textarea($GLOBALS['opt_descr']);?></textarea>
<tr><td><?=MSG_opt_copyright;?>:<br>
<span class="descr"><?=MSG_opt_copydescr;?></span>
<td><input type=text name=opt_copyright value="<?=$GLOBALS['opt_copyright'];?>">
<tr><td><?=MSG_opt_path;?>:
<td><input type=text name=opt_dir size=32 value="<?=$GLOBALS['opt_dir'];?>">
<tr><td><?=MSG_opt_url;?>:
<td><input type=text name=opt_url size=32 value="<?=$GLOBALS['opt_url'];?>">
<tr><td><?=MSG_opt_mainpage;?>:
<td><select name=opt_mainpage><?=set_select($forums,$GLOBALS['opt_mainpage']);?></select>
<tr><td><?=MSG_opt_logo;?>:
<td><input type=text name=opt_logo size=32 value="<?=$GLOBALS['opt_logo'];?>">
<tr><td><?=MSG_opt_logo_instead;?>:<br>
<span class="descr"><?=MSG_opt_logo_instead_descr;?>
<td><label><input type=radio name=opt_logo_instead value=0 <? check($GLOBALS['opt_logo_instead']==0);?>><?=MSG_no;?></label><br>
<input type=radio name=opt_logo_instead value=1 <? check($GLOBALS['opt_logo_instead']==1);?>><?=MSG_yes;?>
<tr><td><?=MSG_opt_hurl;?>:<br>
<span class="descr"><?=MSG_opt_hurl_descr;?>
<td><label><input type=radio name=opt_hurl value=0 <? check($GLOBALS['opt_hurl']==0);?>><?=MSG_no;?></label><br>
<label><input type=radio name=opt_hurl value=1 <? check($GLOBALS['opt_hurl']==1);?>><?=MSG_yes;?></label><br>
<input type=radio name=opt_hurl value=2 <? check($GLOBALS['opt_hurl']==2);?>><?=MSG_opt_hurlindex;?>
<tr><td><?=MSG_opt_email;?>:
<td><input type=text name=opt_mailout size=32 value="<?=$GLOBALS['opt_mailout'];?>">
<tr><td><?=MSG_opt_nomailsend;?>:
<td><label><input type=radio name=opt_nomailsend value=0 <? check($GLOBALS['opt_nomailsend']==0);?>><?=MSG_on;?></label><br>
<input type=radio name=opt_nomailsend value=1 <? check($GLOBALS['opt_nomailsend']==1);?>><?=MSG_off;?>

<tr><td><?=MSG_opt_timeregion;?>:
<td><select name=opt_timeregion><?=set_select(MSG_user_timezone,$GLOBALS['opt_timeregion']);?></select>

<tr><td class="tablehead" colspan=2><?=MSG_opt_security;?>
<tr><td><?=MSG_opt_encrypt;?>:
<td><label><input type=radio name=opt_encrypted value=0 <? check($GLOBALS['opt_encrypted']==0);?>><?=MSG_opt_cryptoff;?></label><br>
<label><input type=radio name=opt_encrypted value=1 <? check($GLOBALS['opt_encrypted']==1);?>><?=MSG_opt_crypton;?></label><br>
<input type=radio name=opt_encrypted value=2 <? check($GLOBALS['opt_encrypted']==2);?>><?=MSG_opt_cryptuser;?>
<tr><td><?=MSG_opt_flood;?>:
<td><input type=text name=opt_flood size=5 maxlength=8 value="<?=floor($GLOBALS['opt_flood']);?>"> <?=MSG_opt_floodsec;?>
<tr><td><?=MSG_opt_brutetimeout;?>:
<td><input type=text name=opt_brutetimeout size=5 maxlength=8 value="<?=floor($GLOBALS['opt_brutetimeout']);?>"> <?=MSG_opt_floodsec;?>
<tr><td><?=MSG_opt_activate;?>:
<td><label><input type=radio name=opt_activate value=0 <? check($GLOBALS['opt_activate']==0);?>><?=MSG_opt_actnone;?></label><br>
<label><input type=radio name=opt_activate value=1 <? check($GLOBALS['opt_activate']==1);?>><?=MSG_opt_actemail;?></label><br>
<input type=radio name=opt_activate value=2 <? check($GLOBALS['opt_activate']==2);?>><?=MSG_opt_admin;?>
<tr><td><?=MSG_opt_keeplogs;?>:
<td><input type=text name=opt_keeplogs size=5 maxlength=8 value=<?=$GLOBALS['opt_keeplogs'];?>>
<tr><td><?=MSG_opt_secbrowser;?>:<br><div class="descr"><?=MSG_opt_secbrowser_descr;?></div>
<td><label><input type=radio name=opt_secbrowser value=0 <? check($GLOBALS['opt_secbrowser']==0);?>><?=MSG_no;?></label><br>
<input type=radio name=opt_secbrowser value=1 <? check($GLOBALS['opt_secbrowser']==1);?>><?=MSG_yes;?>
<tr><td><?=MSG_opt_ddos;?>:<br><div class="descr"><?=MSG_opt_ddos_descr;?></div>
<td><label><input type=radio name=opt_ddos value=0 <? check($GLOBALS['opt_ddos']==0);?>><?=MSG_no;?></label><br>
<label><input type=radio name=opt_ddos value=1 <? check($GLOBALS['opt_ddos']==1);?>><?=MSG_opt_ddos_reg;?></label><br>
<input type=radio name=opt_ddos value=2 <? check($GLOBALS['opt_ddos']==2);?>><?=MSG_opt_ddos_all;?>
<tr><td><?=MSG_opt_minpost;?>:
<td><input type=text name=opt_minpost size=7 maxlength=9 value="<?=$GLOBALS['opt_minpost'];?>">
<tr><td><?=MSG_opt_maxpost;?>:
<td><input type=text name=opt_maxpost size=7 maxlength=9 value="<?=$GLOBALS['opt_maxpost'];?>">

<tr><td class="tablehead" colspan=2><input type=hidden name=m value=basic>
<input type=hidden name=a value=opt_save><input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function ad_opt_edit2($levels,$forums) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1>
<tr><td class="tablehead" colspan=2><?=MSG_opt_misc;?>
<tr><td><?=MSG_opt_foreword;?>:<br><div class="descr"><?=MSG_opt_foreworddescr;?></div>
<td><textarea name=opt_foreword rows=4 cols=30><?=textarea($GLOBALS['opt_foreword']);?></textarea>
<tr><td><?=MSG_opt_announce;?>:
<td><label><input type=radio name=opt_announce value=0 <? check($GLOBALS['opt_announce']==0);?>><?=MSG_opt_annone;?></label><br>
<label><input type=radio name=opt_announce value=1 <? check($GLOBALS['opt_announce']==1);?>><?=MSG_opt_antitle;?></label><br>
<input type=radio name=opt_announce value=2 <? check($GLOBALS['opt_announce']==2);?>><?=MSG_opt_anall;?>
<tr><td><?=MSG_opt_announcetext;?>:<br><div class="descr"><?=MSG_opt_andescr;?></div>
<td><textarea name=opt_announcetext rows=4 cols=30><?=textarea($GLOBALS['opt_announcetext']);?></textarea>
<tr><td><?=MSG_opt_status;?>:
<td><label><input type=radio name=opt_status value=0 <? check($GLOBALS['opt_status']==0);?>><?=MSG_no;?></label><br>
<label><input type=radio name=opt_status value=1 <? check($GLOBALS['opt_status']==1);?>><?=MSG_yes;?></label><br>
<tr><td><?=MSG_opt_closetext;?>:
<td><textarea name=opt_closetext rows=4 cols=30><?=$GLOBALS['opt_closetext'];?></textarea>
<tr><td><?=MSG_opt_summary;?>:<td>
<label><input type=radio name=opt_summary value=1 <? check($GLOBALS['opt_summary']==1);?>><?=MSG_opt_top;?></label><br>
<label><input type=radio name=opt_summary value=2 <? check($GLOBALS['opt_summary']==2);?>><?=MSG_opt_bottom;?></label><br>
<input type=radio name=opt_summary value=0 <? check($GLOBALS['opt_summary']==0);?>><?=MSG_opt_noutput;?>
<tr><td><?=MSG_opt_fwelcome;?>:<td>
<label><input type=radio name=opt_fwelcome value=1 <? check($GLOBALS['opt_fwelcome']==1);?>><?=MSG_opt_ftop;?></label><br>
<label><input type=radio name=opt_fwelcome value=2 <? check($GLOBALS['opt_fwelcome']==2);?>><?=MSG_opt_fbottom;?></label><br>
<input type=radio name=opt_fwelcome value=0 <? check($GLOBALS['opt_fwelcome']==0);?>><?=MSG_opt_noutput;?>
<tr><td><?=MSG_opt_gzip;?>:
<td><input type=radio name=opt_gzip value=1 <? check($GLOBALS['opt_gzip']==1);?>><?=MSG_yes;?> &nbsp;
<input type=radio name=opt_gzip value=0 <? check($GLOBALS['opt_gzip']==0);?>><?=MSG_no;?>
<tr><td><?=MSG_opt_hot;?>:
<td><input type=text name=opt_hot size=5 maxlength=8 value="<?=$GLOBALS['opt_hot'];?>">
<tr><td><?=MSG_opt_exttopic;?>:
<td><input type=radio name=opt_exttopic value=1 <? check($GLOBALS['opt_exttopic']==1);?>><?=MSG_yes;?> &nbsp;
<input type=radio name=opt_exttopic value=0 <? check($GLOBALS['opt_exttopic']==0);?>><?=MSG_no;?>

<tr><td><?=MSG_opt_posttitle;?>:
<td><input type=radio name=opt_posttitles value=1 <? check($GLOBALS['opt_posttitles']==1);?>><?=MSG_opt_pton;?> &nbsp;
<input type=radio name=opt_posttitles value=0 <? check($GLOBALS['opt_posttitles']==0);?>><?=MSG_opt_ptoff;?>
<tr><td><?=MSG_opt_defvotecount;?>:
<td><input type=text name=opt_defvotecount size=5 maxlength=8 value="<?=$GLOBALS['opt_defvotecount'];?>">
<tr><td><?=MSG_opt_here;?>:
<td><input type=text name=opt_heretime size=5 maxlength=8 value="<?=floor($GLOBALS['opt_heretime']);?>"> <?=MSG_opt_hereminutes;?>
<tr><td><?=MSG_opt_visittime;?>:
<td><input type=text name=opt_visittime size=5 maxlength=8 value="<?=floor($GLOBALS['opt_visittime']);?>"> <?=MSG_opt_hereminutes;?>
<tr><td><?=MSG_opt_updated_time;?>:
<td><input type=text name=opt_updated_time size=5 maxlength=8 value="<?=floor($GLOBALS['opt_updated_time']);?>">
<tr><td><?=MSG_opt_hinttext;?>:
<td><input type=text name=opt_hinttext size=5 maxlength=8 value="<?=$GLOBALS['opt_hinttext'];?>">
<tr><td><?=MSG_opt_location_bottom;?>:
<td><input type=radio name=opt_location_bottom value=0 <? check($GLOBALS['opt_location_bottom']==0);?>><?=MSG_no;?>
<input type=radio name=opt_location_bottom value=1 <? check($GLOBALS['opt_location_bottom']==1);?>><?=MSG_yes;?>
<tr><td><?=MSG_opt_showpresent;?>:
<td><label><input type=radio name=opt_showpresent value=0 <? check($GLOBALS['opt_showpresent']==0);?>><?=MSG_opt_nowhere;?></label><br>
<label><input type=radio name=opt_showpresent value=1 <? check($GLOBALS['opt_showpresent']==1);?>><?=MSG_opt_titlepage;?></label><br>
<label><input type=radio name=opt_showpresent value=2 <? check($GLOBALS['opt_showpresent']==2);?>><?=MSG_opt_forums;?></label><br>
<input type=radio name=opt_showpresent value=3 <? check($GLOBALS['opt_showpresent']==3);?>><?=MSG_opt_topics;?>
<tr><td><?=MSG_opt_submenu;?>:
<td><label><input type=radio name=opt_submenu value=0 <? check($GLOBALS['opt_submenu']==0);?>><?=MSG_no;?></label><br>
<input type=radio name=opt_submenu value=1 <? check($GLOBALS['opt_submenu']==1);?>><?=MSG_yes;?>
<tr><td><?=MSG_opt_noname_mail;?><br>
<span class="descr"><?=MSG_opt_noname_mail_descr;?>:
<td><label><input type=radio name=opt_noname_mail value=0 <? check($GLOBALS['opt_noname_mail']==0);?>><?=MSG_no;?></label><br>
<input type=radio name=opt_noname_mail value=1 <? check($GLOBALS['opt_noname_mail']==1);?>><?=MSG_yes;?>
<tr><td><?=MSG_opt_logs;?>:<br><div class="descr"><?=MSG_opt_logs_descr;?></div>
<td><label><input type=radio name=opt_log value=2 <? check($GLOBALS['opt_log']==2);?>><?=MSG_opt_logall;?></label><br>
<label><input type=radio name=opt_log value=1 <? check($GLOBALS['opt_log']==1);?>><?=MSG_opt_logreg;?></label><br>
<input type=radio name=opt_log value=0 <? check($GLOBALS['opt_log']==0);?>><?=MSG_opt_lognone;?>
<tr><td><?=MSG_opt_directlink;?>:<br><div class="descr"><?=MSG_opt_direct_descr;?></div><td>
<label><input type=radio name=opt_directlink value=1 <? check($GLOBALS['opt_directlink']==1);?>><?=MSG_yes;?></label><br>
<input type=radio name=opt_directlink value=0 <? check($GLOBALS['opt_directlink']==0);?>><?=MSG_no;?>
<tr><td><?=MSG_opt_article_split;?>:
<div class="descr"><?=MSG_opt_article_split_descr;?></div>
<td><input type=text name=opt_article_split size=5 maxlength=8 value="<?=floor($GLOBALS['opt_article_split']);?>">
<tr><td><?=MSG_opt_complain;?>:
<td><label><input type=radio name=opt_complain value=0 <? check($GLOBALS['opt_complain']==0);?>><?=MSG_opt_complain_all;?></label><br>
<label><input type=radio name=opt_complain value=1 <? check($GLOBALS['opt_complain']==1);?>><?=MSG_opt_complain_noguest;?></label><br>
<input type=radio name=opt_complain value=2 <? check($GLOBALS['opt_complain']==2);?>><?=MSG_opt_complain_nobody;?>

<tr><td class="tablehead" colspan=2><?=MSG_opt_search;?>
<tr><td><?=MSG_opt_search_limit;?>:
<td><input type=text name=opt_search_limit size=5 maxlength=5 value="<?=floor($GLOBALS['opt_search_limit']);?>">
<tr><td><?=MSG_opt_search_count;?>:
<td><input type=text name=opt_search_count size=5 maxlength=5 value="<?=floor($GLOBALS['opt_search_count']);?>">
<tr><td><?=MSG_opt_search_ext;?>:<br><div class="descr"><?=MSG_opt_search_ext_descr;?></div><td>
<label><input type=radio name=opt_search_ext value=1 <? check($GLOBALS['opt_search_ext']==1);?>><?=MSG_yes;?></label><br>
<input type=radio name=opt_search_ext value=0 <? check($GLOBALS['opt_search_ext']==0);?>><?=MSG_no;?>

<tr><td class="tablehead" colspan=2><?=MSG_opt_user;?>
<tr><td><?=MSG_opt_nameletters;?>:<br>
<span class='descr'><?=MSG_opt_nameletters_descr;?></span>
<td><input type=text name=opt_nameletters value="<?=$GLOBALS['opt_nameletters'];?>">
<tr><td><?=MSG_opt_noquickreg;?>:
<td><input type=radio name=opt_noquickreg value=1 <? check($GLOBALS['opt_noquickreg']==1);?>><?=MSG_no;?> &nbsp;
<input type=radio name=opt_noquickreg value=0 <? check($GLOBALS['opt_noquickreg']==0);?>><?=MSG_yes;?>
<tr><td><?=MSG_opt_mustfields;?>:
<td><input type=text size=40 name=opt_mustfields value="<?=$GLOBALS['opt_mustfields'];?>">
<tr><td><?=MSG_opt_mustmsg;?>:
<td><input type=text size=40 name=opt_mustmsg value="<?=htmlspecialchars($GLOBALS['opt_mustmsg']);?>">
<tr><td><?=MSG_opt_reginfo;?>:
<td><label><input type=radio name=opt_reginfo value=0 <? check($GLOBALS['opt_reginfo']==0);?>><?=MSG_opt_regon;?></label><br>
<input type=radio name=opt_reginfo value=1 <? check($GLOBALS['opt_reginfo']==1);?>><?=MSG_opt_regoff;?>
<tr><td><?=MSG_opt_fixviews;?>:<br><?=MSG_opt_fixviews_descr;?>
<td><input type=radio name=opt_fixviews value=1 <? check($GLOBALS['opt_fixviews']==1);?>><?=MSG_opt_fixon;?> &nbsp;
<input type=radio name=opt_fixviews value=0 <? check($GLOBALS['opt_fixviews']==0);?>><?=MSG_opt_fixoff;?>
<tr><td><?=MSG_opt_topiccount;?>:<br>
<span class="descr"><?=MSG_opt_topiccount_descr;?></span>
<td><input type=radio name=opt_topiccount value=0 <? check($GLOBALS['opt_topiccount']==0);?>><?=MSG_yes;?> &nbsp;
<input type=radio name=opt_topiccount value=1 <? check($GLOBALS['opt_topiccount']==1);?>><?=MSG_no;?>
<tr><td><?=MSG_opt_warnstoban;?>:
<td><input type=text name=opt_warnstoban size=5 maxlength=8 value="<?=$GLOBALS['opt_warnstoban'];?>">
<tr><td><?=MSG_opt_ratetime;?>:
<td><input type=text name=opt_ratetime size=5 maxlength=8 value="<?=$GLOBALS['opt_ratetime'];?>">
<tr><td><?=MSG_opt_norateperiod;?>:
<td><input type=text name=opt_norateperiod size=5 maxlength=8 value="<?=$GLOBALS['opt_norateperiod'];?>">
<tr><td><?=MSG_opt_ltitle;?>:
<td><select name=opt_ltitle><?=set_select($levels,$GLOBALS['opt_ltitle']);?></select>
<tr><td><?=MSG_opt_ratinglevel;?>:
<td><select name=opt_ratinglevel><?=set_select($levels,$GLOBALS['opt_ratinglevel']);?></select>
<tr><td><?=MSG_opt_rating;?>:<br>
<span class="descr"><?=MSG_opt_rating_descr;?></span>
<td><input type=radio name=opt_rating value=0 <? check($GLOBALS['opt_rating']==0);?>><?=MSG_yes;?> &nbsp;
<input type=radio name=opt_rating value=1 <? check($GLOBALS['opt_rating']==1);?>><?=MSG_no;?>
<tr><td><?=MSG_opt_reputaion;?>:<br>
<span class="descr"><?=MSG_opt_reputation_descr;?></span>
<td><input type=radio name=opt_reputation value=0 <? check($GLOBALS['opt_reputation']==0);?>><?=MSG_yes;?> &nbsp;
<input type=radio name=opt_reputation value=1 <? check($GLOBALS['opt_reputation']==1);?>><?=MSG_no;?>
<tr><td><?=MSG_opt_impersonation;?>:<br>
<span class="descr"><?=MSG_opt_impersonation_descr;?></span>
<td><input type=radio name=opt_impersonation value=0 <? check($GLOBALS['opt_impersonation']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=opt_impersonation value=1 <? check($GLOBALS['opt_impersonation']==1);?>><?=MSG_yes;?>
<tr><td class="tablehead" colspan=2><?=MSG_opt_news;?>
<tr><td><?=MSG_opt_news_mainmode;?>:
<td><label><input type=radio name=opt_news_main_mode value=1 <? check($GLOBALS['opt_news_main_mode']==1);?>><?=MSG_opt_news_days;?></label><br>
<label><input type=radio name=opt_news_main_mode value=2 <? check($GLOBALS['opt_news_main_mode']==2);?>><?=MSG_opt_news_count;?></label><br>
<input type=radio name=opt_news_main_mode value=3 <? check($GLOBALS['opt_news_main_mode']==3);?>><?=MSG_opt_news_both;?>
<tr><td><?=MSG_opt_news_maindays;?>:
<td><input type=text name=opt_news_main_days size=8 maxlength=12 value="<?=$GLOBALS['opt_news_main_days'];?>">
<tr><td><?=MSG_opt_news_maincount;?>:
<td><input type=text name=opt_news_main_count size=8 maxlength=12 value="<?=$GLOBALS['opt_news_main_count'];?>">
<tr><td><?=MSG_opt_sendmail_days;?>:
<td><input type=text name=opt_sendmail_days size=3 maxlength=4 value="<?=$GLOBALS['opt_sendmail_days'];?>">
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=basic>
<input type=hidden name=opt_ibversion value="<?=$GLOBALS['opt_ibversion'];?>">
<input type=hidden name=a value=opt_save><input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function ad_opt_edit3($levels,$forums) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1>
<tr><td class="tablehead" colspan=2><?=MSG_opt_tagimg;?>
<tr><td width="54%"><?=MSG_opt_imgtag;?>:
<td><label><input type=radio name=opt_imgtag value=0 <? check($GLOBALS['opt_imgtag']==0);?>><?=MSG_opt_imgpics;?></label><br>
<input type=radio name=opt_imgtag value=1 <? check($GLOBALS['opt_imgtag']==1);?>><?=MSG_opt_imgicons;?>
<tr><td><?=MSG_opt_imglimit_x;?>:
<td><input type=text name=opt_imglimit_x size=4 maxlength=4 value="<?=floor($GLOBALS['opt_imglimit_x']);?>">
<tr><td><?=MSG_opt_imglimit_y;?>:
<td><input type=text name=opt_imglimit_y size=4 maxlength=4 value="<?=floor($GLOBALS['opt_imglimit_y']);?>">
<tr><td><?=MSG_opt_sigpics;?>:
<td><label><input type=radio name=opt_sigpics value=0 <? check($GLOBALS['opt_sigpics']==0);?>><?=MSG_disabled;?></label><br>
<input type=radio name=opt_sigpics value=1 <? check($GLOBALS['opt_sigpics']==1);?>><?=MSG_enabled;?>
<tr><td class="tablehead" colspan=2><?=MSG_opt_avatars;?>
<tr><td><?=MSG_opt_GD2;?>?<br><div class="descr"><?=MSG_opt_GD2descr;?>
<td><label><input type=radio name=opt_GD2 value=0 <? check($GLOBALS['opt_GD2']==0);?>><?=MSG_no;?></label><br>
<input type=radio name=opt_GD2 value=1 <? check($GLOBALS['opt_GD2']==1);?>><?=MSG_yes;?>
<tr><td><?=MSG_opt_maxavatarsize;?>:<br>
<span class="descr"><?=MSG_opt_maxavsize_descr;?></span>
<td><input type=text name=opt_maxavatarsize size=8 maxlength=12 value="<?=$GLOBALS['opt_maxavatarsize'];?>"> <?=MSG_opt_bytes;?>
<tr><td><?=MSG_opt_maxavatarx;?>:
<td><input type=text name=opt_maxavatarx size=5 maxlength=8 value="<?=$GLOBALS['opt_maxavatarx'];?>"> <?=MSG_opt_pixels;?>
<tr><td><?=MSG_opt_maxavatary;?>:
<td><input type=text name=opt_maxavatary size=5 maxlength=8 value="<?=$GLOBALS['opt_maxavatary'];?>"> <?=MSG_opt_pixels;?>
<tr><td><?=MSG_opt_maxphoto;?>:
<td><input type=text name=opt_maxphoto size=8 maxlength=12 value="<?=$GLOBALS['opt_maxphoto'];?>"> <?=MSG_opt_bytes;?>
<tr><td><?=MSG_opt_maxphotox;?>:
<td><input type=text name=opt_maxphotox size=5 maxlength=8 value="<?=$GLOBALS['opt_maxphotox'];?>"> <?=MSG_opt_pixels;?>
<tr><td><?=MSG_opt_maxphotoy;?>:
<td><input type=text name=opt_maxphotoy size=5 maxlength=8 value="<?=$GLOBALS['opt_maxphotoy'];?>"> <?=MSG_opt_pixels;?>

<tr><td><?=MSG_opt_avatarx;?>:<br>
<span class="descr"><?=MSG_opt_avatarx_descr;?></span>
<td><input type=text name=opt_avatarx size=5 maxlength=8 value="<?=$GLOBALS['opt_avatarx'];?>"> <?=MSG_opt_pixels;?>
<tr><td><?=MSG_opt_avatary;?>:<br>
<span class="descr"><?=MSG_opt_avatary_descr;?></span>
<td><input type=text name=opt_avatary size=5 maxlength=8 value="<?=$GLOBALS['opt_avatary'];?>"> <?=MSG_opt_pixels;?>
<tr><td class="tablehead" colspan=2><?=MSG_opt_attach_settings;?>
<tr><td><?=MSG_opt_maxfileattach;?>:
<td><input type=text name=opt_maxfileattach size=8 maxlength=12 value="<?=$GLOBALS['opt_maxfileattach'];?>"> <?=MSG_opt_bytes;?>
<tr><td><?=MSG_opt_previewx;?>:
<td><input type=text name=opt_previewx size=5 maxlength=8 value="<?=$GLOBALS['opt_previewx'];?>"> <?=MSG_opt_pixels;?>
<tr><td><?=MSG_opt_previewy;?>:
<td><input type=text name=opt_previewy size=5 maxlength=8 value="<?=$GLOBALS['opt_previewy'];?>"> <?=MSG_opt_pixels;?>

<tr><td class="tablehead" colspan=2><?=MSG_opt_galery;?>
<tr><td><?=MSG_opt_photos_line;?>:
<td><input type=text name=opt_photos_line size=3 maxlength=3 value="<?=$GLOBALS['opt_photos_line'];?>">
<tr><td><?=MSG_opt_photo_thumb_y;?>:
<div class="descr"><?=MSG_opt_photo_size_x_descr;?></div>
<td><input type=text name=opt_photo_thumb_y size=5 maxlength=7 value="<?=$GLOBALS['opt_photo_thumb_y'];?>">
<tr><td><?=MSG_opt_photo_size_x;?>:
<td><input type=text name=opt_photo_size_x size=5 maxlength=7 value="<?=$GLOBALS['opt_photo_size_x'];?>">
<tr><td><?=MSG_opt_thumb_qlty;?>:
<div class="descr"><?=MSG_opt_thumb_qlty_descr;?></div>
<td><input type=text name=opt_thumb_qlty size=3 maxlength=3 value="<?=$GLOBALS['opt_thumb_qlty'];?>">
<tr><td><?=MSG_opt_photo_qlty;?>:
<td><input type=text name=opt_photo_qlty size=3 maxlength=3 value="<?=$GLOBALS['opt_photo_qlty'];?>">
<tr><td><?=MSG_opt_photo_order;?>:
<td><input type=radio name=opt_photo_order value=0 <?=check($GLOBALS['opt_photo_order']==0);?>> <?=MSG_opt_photo_inverse;?><br>
<input type=radio name=opt_photo_order value=1 <?=check($GLOBALS['opt_photo_order']==1);?>> <?=MSG_opt_photo_linear;?>
<tr><td><?=MSG_opt_photo_maxsize;?>:
<td><input type=text name=opt_photo_maxsize size=8 maxlength=8 value="<?=$GLOBALS['opt_photo_maxsize'];?>"> <?=MSG_bytes;?>

<tr><td class="tablehead" colspan=2><input type=hidden name=m value=basic>
<input type=hidden name=opt_ibversion value="<?=$GLOBALS['opt_ibversion'];?>">
<input type=hidden name=a value=opt_save><input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function ad_opt_edit4($levels,$forums,$categs) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1>

<tr><td class="tablehead" colspan=2><?=MSG_opt_blogparams;?>
<tr><td><?=MSG_opt_blog_level;?>:
<td><select name=opt_blog_level><?=set_select($levels,$GLOBALS['opt_blog_level']);?></select>
<tr><td><?=MSG_opt_blog_cat;?>:
<td><select name=opt_blog_cat><?=set_select($categs,$GLOBALS['opt_blog_cat']);?></select>
<tr><td><?=MSG_opt_blog_container;?>:
<td><select name=opt_blog_container><?=set_select($forums,$GLOBALS['opt_blog_container']);?></select>

<tr><td class="tablehead" colspan=2><?=MSG_opt_galleryparams;?>
<tr><td><?=MSG_opt_gallery_level;?>:
<td><select name=opt_gallery_level><?=set_select($levels,$GLOBALS['opt_gallery_level']);?></select>
<tr><td><?=MSG_opt_gallery_cat;?>:
<td><select name=opt_gallery_cat><?=set_select($categs,$GLOBALS['opt_gallery_cat']);?></select>
<tr><td><?=MSG_opt_gallery_container;?>:
<td><select name=opt_gallery_container><?=set_select($forums,$GLOBALS['opt_gallery_container']);?></select>

<tr><td class="tablehead" colspan=2><input type=hidden name=m value=basic>
<input type=hidden name=opt_ibversion value="<?=$GLOBALS['opt_ibversion'];?>">
<input type=hidden name=a value=opt_save><input type=submit value="<?=MSG_save;?>">
</table></form>
<? }


function ad_bw_list_start() { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=4>
<?=MSG_bw_list;?>
<tr><td class="tablehead" width="40%"><?=MSG_bw_word;?>
<td class="tablehead" width="40%"><?=MSG_bw_replace;?>
<td width="10%" class="tablehead"><?=MSG_bw_onlyname;?>
<td class="tablehead"><?=MSG_delete;?>
<? }

function ad_bw_list_entry($badword) { ?>
<tr><td><input type=text name=badword[<?=$badword['w_id'];?>] size=20 maxlength=32 value="<?=$badword['w_bad'];?>">
<td><input type=text name=goodword[<?=$badword['w_id'];?>] size=20 maxlength=32 value="<?=$badword['w_good'];?>">
<td align=center><input type=checkbox name=onlyname[<?=$badword['w_id'];?>] value=1 <?=check($badword['w_onlyname']==1);?>>
<td align=center><input type=checkbox name=delete[<?=$badword['w_id'];?>] value=1>
<? }

function ad_bw_list_end() { ?>
<tr><td class="tablehead" colspan=4><input type=hidden name=m value=basic>
<input type=hidden name=a value="save_badword"><input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function ad_bw_add_start() { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=3>
<?=MSG_bw_add;?>
<tr><td class="tablehead" width="45%"><?=MSG_bw_word;?>
<td class="tablehead" width="45%"><?=MSG_bw_replace;?>
<td width="10%" class="tablehead"><?=MSG_bw_onlyname;?>
<? }

function ad_bw_add_entry($number) { ?>
<tr><td><input type=text name="badword[<?=$number;?>]" size=20 maxlength=32>
<td><input type=text name="goodword[<?=$number;?>]" size=20 maxlength=32>
<td align=center><input type=checkbox name="onlyname[<?=$number;?>]" value=1>
<? }

function ad_bw_add_end() { ?>
<tr><td class="tablehead" colspan=3><input type=hidden name=m value=basic>
<input type=hidden name=a value="add_badword"><input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function lang_select($newaction,$newmodule,$langs,$msg) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_lang_selection;?> <?=$msg;?>
<tr><td width="50%"><?=MSG_lang_select;?><td><select name="lang"><?=$langs;?></select>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=<?=$newmodule;?>>
<input type=hidden name=a value=<?=$newaction;?>><input type=submit value="<?=MSG_edit;?>">
</table></form>
<? }

function edit_rules_form($rules) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead">
<?=MSG_rules_edit;?>
<tr><td align=center><textarea name=rules_text rows=10 cols=50><?=$rules;?></textarea>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=basic><input type=hidden name=a value=save_rules>
<input type=hidden name=lang value=<?=getvar("lang");?>><input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function edit_template_form($template,$msg) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1>
<tr><td class="tablehead"><?=$msg;?>
<tr><td><?=MSG_sys_oldpass;?>: <input type=password name=sys_pass size=32 maxlength=32>
<? if (getvar("file")=="tmplate1.php" || getvar("file")=="tmplate2.php") { ?>
<tr><td><?=MSG_template_descr;?><br>
<?=MSG_template_comment1;?><br>
<?=MSG_template_comment2;?><br>
<?=MSG_template_comment3;?><br>
<?=MSG_template_comment4;?><br>
<?=MSG_template_comment15;?><br>
<?=MSG_template_comment5;?><br>
<?=MSG_template_comment16;?><br>
<?=MSG_template_comment6;?><br>
<?=MSG_template_comment7;?><br>
<?=MSG_template_comment8;?><br>
<?=MSG_template_comment9;?><br>
<?=MSG_template_comment10;?><br>
<?=MSG_template_comment11;?><br>
<?=MSG_template_comment12;?><br>
<?=MSG_template_comment13;?><br>
<?=MSG_template_comment14;?><br>
<?=MSG_template_comment17;?><br>
<?=MSG_template_comment18;?><br>
<?=MSG_template_comment19;?><br>
<?=MSG_template_comment20;?><br>
<?=MSG_template_comment21;?><br>
<?=MSG_template_comment22;?><br>

<? } ?>
<tr><td align=center><textarea name=template rows=10 cols=50><?=$template;?></textarea>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=basic><input type=hidden name=a value=save_template>
<input type=hidden name=file value=<?=getvar("file");?>><input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function mailsend_form($text,$days,$forums) { ?>
<script type="text/javascript"><!--
function ch(st) {
e=document.mailforum.elements['fs[]'];
for (i=0;i<e.options.length;i++) e.options[i].selected=st;
}
function sbm() {
f=0; e=document.mailforum.elements['fs[]'];
for (i=0;i<e.options.length;i++) if (e.options[i].selected) f++;
if (!f) alert('<?=MSG_ad_mail_noforums;?>');
return (f!=0);
}
--></script>
<form action="" method="get" name="mailforum" onsubmit="return sbm();">
<table class="innertable" width="100%">
<tr><td class="tablehead">
<?=MSG_ad_mail_generation;?>
<tr><td>
<?=MSG_ad_mail_period;?>
 <input type="text" size=3 name="days" maxlength=4 value="<?=$days;?>"> <?=MSG_ad_mail_days;?>
<tr><td style="text-align: center">
<?=MSG_ad_mail_forums;?>:<br>
<select style="width: 90%" name='fs[]' multiple size=10><?=$forums;?></select>
<tr><td class="tablehead">
<input type=hidden name=m value=search>
<input type=button onClick="ch(true)" value="<?=MSG_selectall;?>">
<input type=button onClick="ch(false)" value="<?=MSG_unselectall;?>">
<tr><td class="tablehead">
<input type=hidden name=m value=basic><input type=hidden name=a value=mailsend>
<input type="submit" value="<?=MSG_ad_mail_generate;?>">
</table></form>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead">
<?=MSG_ad_mailsend;?>
<tr><td align=center><?=MSG_ad_mailtext;?>:
<div class="descr"><?=MSG_ad_mailtext_descr;?>.</div><br>
<textarea name=m_text rows=12 cols=40><?=$text;?></textarea><br><br>
<tr><td class="tablehead"><input type=hidden name=m value=basic><input type=hidden name=a value=mailsend_process>
<input type=submit value="<?=MSG_send;?>">
</table></form>
<? }


function avatar_form() { ?>
<form action="admin/index.php" method=POST enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_avatar_upload;?><?
for ($i=0; $i<10; $i++) { ?>
<tr><td width="50%"><?=MSG_avatar_local;?>:<td><input type=file name="avatar<?=$i;?>">
<? } ?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=basic><input type=hidden name=a value=avatar_upload>
<input type=submit value="<?=MSG_upload;?>">
</table></form>
<? }

function smile_form() { ?>
<form action="admin/index.php" method=POST enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=3>
<?=MSG_smile_upload;?>
<tr><td width="40%"><?=MSG_smile_code;?>
<td width="40%"><?=MSG_smile_local;?><td><?=MSG_smile_show;?><?
for ($i=0; $i<10; $i++) { ?>
<tr><td><input type=text name="sm_code<?=$i;?>"><td>
<input type=file name="smile<?=$i;?>"><td>
<input type=checkbox name="sm_show<?=$i;?>" value=1 checked>
<? } ?>
<tr><td class="tablehead" colspan=3><input type=hidden name=m value=basic><input type=hidden name=a value=smile_upload>
<input type=submit value="<?=MSG_upload;?>">
</table></form>
<? }

function mail_form($buffer) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_template_mail;?> <?=$msg;?>
<tr><td width="50%"><?=MSG_template_select;?><td><select name=mail><?=$buffer;?></select>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=basic>
<input type=hidden name=a value=edit_mail><input type=hidden name=lang value="<?=getvar("lang");?>">
<input type=submit value="<?=MSG_edit;?>">
</table></form>
<? }

function edit_mail_form($text) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead">
<?=MSG_template_edit;?>
<tr><td align=center><textarea name=text rows=10 cols=50><?=$text;?></textarea>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=basic><input type=hidden name=a value=save_mail>
<input type=hidden name=mail value="<?=getvar("mail");?>"><input type=hidden name=lang value="<?=getvar("lang");?>">
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function sql_query_form() { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead">
<?=MSG_sql;?>
<tr><td><?=MSG_sys_oldpass;?>: <input type=password name=sys_pass size=32 maxlength=32>
<tr><td align=center><?=MSG_sql_text;?>:<br><textarea name=sqltext rows=10 cols=50></textarea>
<tr><td class="tablehead"><input type=hidden name=m value=basic><input type=hidden name=a value=sql_process>
<input type=submit value="<?=MSG_sql_exec;?>">
</table></form>
<? }

function sql_field_start($count) { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=<?=$count;?>>
<?=MSG_sql_results;?><tr>
<? }

function sql_field_entry($fieldname) { ?>
<td class="tablehead"><?=$fieldname;?>
<? }

function sql_field_end() { ?>

<? }

function sql_row_start() { ?>
<tr>
<? }

function sql_row_entry($text) { ?>
<td><?=$text;?>
<? }

function sql_row_end() { ?>

<? }

function sql_query_end() { ?>
</table>
<? }

function sys_pass_form() { ?>
<form action="admin/index.php" method=POST>
<table width="100%" class="innertable" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_sys_passchange;?>
<tr><td width="50%"><?=MSG_sys_oldpass;?>:<td>
<input type=password name=old_pass size=30 maxlength=32>
<tr><td width="50%"><?=MSG_sys_newpass;?>:<td>
<input type=password name=new_pass1 size=30 maxlength=32>
<tr><td width="50%"><?=MSG_sys_newpass_confirm;?>:<td>
<input type=password name=new_pass2 size=30 maxlength=32>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=basic>
<input type=hidden name=a value=sys_pass_process><input type=submit value=<?=MSG_save;?>>
</table></form>
<? }

function smile_list_start() { ?>
<form action="admin/index.php" method=POST>
<table width="100%" class="innertable" cellspacing=1><tr><td class="tablehead" colspan=3><?=MSG_smile_list;?>
<tr><td class="tablehead" width="25%"><?=MSG_smile_code;?>
<td class="tablehead" width="25%"><?=MSG_smile_pic;?>
<td class="tablehead" width="50%"><?=MSG_actions;?>
<? }

function smile_entry($smdata) { ?>
<tr><td align=center><?=$smdata['sm_code'];?>
<td align=center><img src="smiles/<?=$smdata['sm_file'];?>" alt="<?=$smdata['sm_code'];?>">
<td align=center>
<a href="admin/index.php?m=basic&a=edit_sm_code&amp;sm_code=<?=urlencode($smdata['sm_code']);?>&amp;sm_file=<?=$smdata['sm_file'];?>&amp;sm_show=<?=$smdata['sm_show'];?>"><?=MSG_smile_edit_code;?></a> &nbsp;
<a href="admin/index.php?m=basic&a=edit_sm_file&amp;sm_code=<?=urlencode($smdata['sm_code']);?>"><?=MSG_smile_edit_file;?></a> &nbsp;
<a href="admin/index.php?m=basic&a=delete_smile&amp;sm_code=<?=urlencode($smdata['sm_code']);?>"><?=MSG_delete;?></a>
<? }

function smile_list_end() { ?>
</table>
<? }

function edit_sm_code() { ?>
<form action="admin/index.php" method="post">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_smile_edit_code;?>
<tr><td width="50%"><?=MSG_smile_code;?><td>
<input type=text size=12 maxlength=12 name="sm_code" value="<?=getvar('sm_code');?>">
<tr><td width="50%"><?=MSG_smile_show;?><td>
<input type=radio name=sm_show value=1 <?=check(getvar('sm_show')==1);?>> <?=MSG_yes;?>
<input type=radio name=sm_show value=0 <?=check(getvar('sm_show')==0);?>> <?=MSG_no;?>
<tr><td class="tablehead" colspan=3><input type=hidden name=m value="basic"><input type=hidden name=a value="do_edit_sm_code">
<input type=hidden name="sm_file" value="<?=getvar('sm_file');?>">
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function edit_sm_file() { ?>
<form action="admin/index.php" method="post" enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_smile_edit_file;?>
<tr><td width="50%"><?=MSG_smile_file;?><td>
<input type=file name="smile">
<tr><td class="tablehead" colspan=3><input type=hidden name=m value="basic"><input type=hidden name=a value="do_edit_sm_file">
<input type=hidden name="sm_code" value="<?=getvar('sm_code');?>">
<input type=submit value="<?=MSG_upload;?>">
</table></form>
<? }

function backup_form() { ?>
<script><!--
function ChBtn() {
document.forms.backup.strt.value="<?=MSG_backup_process;?>";
document.forms.backup.strt.disabled=true;
}
--></script>
<form action="admin/index.php" method=POST name="backup" onSubmit="ChBtn();">
<table width="100%" class="innertable" cellspacing=1><tr><td class="tablehead" colspan=3><?=MSG_backup_title;?>
<tr><td width="50%"><?=MSG_sys_oldpass;?>
<td width="50%"><input type=password name=sys_pass size=32 maxlength=32>
<tr><td><?=MSG_backup_mode;?>
<td><label><input type=radio name=mode value="f" checked><?=MGS_backup_nocomp;?></label><br>
<label><input type=radio name=mode value="gz"><?=MSG_backup_gzip;?></label><br>
<input type=radio name=mode value="bz"><?=MSG_backup_bzip;?>
<tr><td colspan=2 class="tablehead"><input type="submit" name=strt value="<?=MSG_backup_start;?>">
<input type=hidden name=a value=do_backup>
</table></form>
<? }

function backup_start() { ?>
<table width="100%" class="innertable" cellspacing=1><tr><td class="tablehead" colspan=3><?=MSG_backup_files;?>
<? }

function backup_entry($fname,$fsize) { ?>
<tr><td width="70%"><a href="<?=$GLOBALS['opt_url'];?>/temp/<?=$fname;?>"><?=$fname;?></a> (<?=intval($fsize/1024);?> Kb)
<td width="30%"><a href="admin/index.php?m=basic&a=backup_confirm&bfile=<?=$fname;?>"><?=MSG_delete;?></a>
<? }

function backup_end() { ?>
</table>
<? }

function convert_file_next($number) { ?>
<form action="admin/index.php" method=GET>
<table width="100%" class="innertable" cellspacing=1><tr><td class="tablehead">
<input type=hidden name=m value=basic><input type=hidden name=a value=convert_files>
<input type=hidden name=st value="<?=$number;?>"><input type=submit value="<?=MSG_convert_next;?>">
</table></form>
<? }

function convert_photo_next($number) { ?>
<form action="admin/index.php" method=GET>
<table width="100%" class="innertable" cellspacing=1><tr><td class="tablehead">
<input type=hidden name=m value=basic><input type=hidden name=a value=convert_photos>
<input type=hidden name=st value="<?=$number;?>"><input type=submit value="<?=MSG_convert_next;?>">
</table></form>
<? }

function bcode_edit_start() { ?>
<form action="admin/index.php" method=post>
<table width="100%" class="innertable" cellspacing=1><tr><td colspan=2 class="tablehead">
<?=MSG_bcodes_list;?>
<tr><td colspan=2><?=MSG_bcodes_descr;?>
<tr><td class="tablehead"><?=MSG_bcodes_regexp;?><td class="tablehead"><?=MSG_bcodes_replace;?>
<? }

function bcode_edit_entry($str1,$str2) { ?>
<tr><td width=50%><input type=text size=20 value="<?=htmlspecialchars($str1);?>" name="codes[]">
<td><input type=text size=60 value="<?=htmlspecialchars($str2);?>" name="replace[]">
<? }

function bcode_edit_end() { ?>
<tr><td class="tablehead" colspan=2><input type=submit value="<?=MSG_save;?>">
<input type=hidden name=m value=basic><input type=hidden name=a value="do_edit_bcode"></table></form>
<? }

function edit_ip_start() { ?>
<form action="admin/index.php" method=post>
<table width="100%" class="innertable" cellspacing=1 style="text-align: center">
<col width="50%"><col>
<tr><td colspan=2 class="tablehead">
<?=MSG_ipban_list;?>
<tr><td colspan=2 class="descr"><?=MSG_ipban_descr;?>
<? }

function edit_ip_entry($data) { ?>
<tr><td><input type=text size=15 name="ips1[]" value="<?=$data[0];?>">
<td><input type=text size=15 name="ips2[]" value="<?=$data[1];?>">
<? }

function edit_ip_end() { ?>
<tr><td class="tablehead" colspan=2><input type=submit value="<?=MSG_save;?>">
<input type=hidden name=m value=basic><input type=hidden name=a value="do_edit_ip"></table></form>
<? }
