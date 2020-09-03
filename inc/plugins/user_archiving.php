<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}


function user_archiving_info()
{
    return array(
        "name"			=> "User Threadarchivierung",
        "description"	=> "User können eigene Threads archivieren.",
        "website"		=> "",
        "author"		=> "Ales",
        "authorsite"	=> "",
        "version"		=> "1.0",
        "guid" 			=> "",
        "codename"		=> "",
        "compatibility" => "*"
    );
}

function user_archiving_install()
{
    global $db, $mybb;

    $setting_group = array(
        'name' => 'user_archiving',
        'title' => 'User Threadarchivierung',
        'description' => 'Einstellungen für den User Threadarchivierungs Plugin.',
        'disporder' => 5, // The order your setting group will display
        'isdefault' => 0
    );

    $gid = $db->insert_query("settinggroups", $setting_group);

    $setting_array = array(
        // A text setting
        'user_archiving_inplay' => array(
            'title' => 'Inplayforum',
            'description' => 'Wähle deinen Inplayforum aus.',
            'optionscode' => 'forumselectsingle',
            'value' => '1', // Default
            'disporder' => 1
        ),
        // A text setting
        'user_archiving_archiv' => array(
            'title' => 'Archivkategorie',
            'description' => 'Wähle deine Archivkategorie aus.',
            'optionscode' => 'forumselectsingle',
            'value' => '4', // Default
            'disporder' => 2
        ),


    );

    foreach($setting_array as $name => $setting)
    {
        $setting['name'] = $name;
        $setting['gid'] = $gid;

        $db->insert_query('settings', $setting);
    }

// Don't forget this!
    rebuild_settings();
    //Templates baby
    $insert_array = array(
        'title'        => 'showthread_archiving',
        'template'    => $db->escape_string('<a href="misc.php?action=archiving&tid={$tid}" class="button new_reply_button"><span>Thread Archivieren</span></a>&nbsp;'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'        => 'showthread_archiving_misc',
        'template'    => $db->escape_string('<html>
<head>
<title>Threadarchivierung</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>Thema Archiveren</strong></td>
</tr>
<tr>
<td class="tcat" align="center" width="25%">
	<strong>Themenersteller</strong>
	</td><td class="trow1">{$user}</td>
</tr>
<tr><td class="tcat" align="center" width="25%">
	<strong>Thread</strong>
	</td><td class="trow1">{$thread}</td>
</tr>
	<tr><td class="tcat" align="center" width="25%">
	<strong>Archivieren</strong>
	</td><td class="trow1">
		<form action="misc.php?action=archiving&tid={$tid}" id="places" method="post">
			<input type="hidden" name="tid" id="tid" value="{$tid}" class="textbox" />
			<input type="text" name="my_post_key" value="{$mybb->post_code}" />
		<select name="archiv_forum">
		{$archiv_option}
		</select>

		</td>
</tr>
	<tr>
		<td colspan="2" class="trow1" align="center"><input type="submit" name="archive" value="Thread archivieren" id="submit" class="button"></td></tr>			</form>
</table>
{$footer}
</body>
</html>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
}

function user_archiving_is_installed()
{

    global $mybb;
    if(isset($mybb->settings['user_archiving_archiv']))
    {
        return true;
    }

    return false;

}

function user_archiving_uninstall()
{
    global $db;

    $db->delete_query('settings', "name IN ('user_archiving_archiv', 'user_archiving_inplay')");
    $db->delete_query('settinggroups', "name = 'user_archiving'");

// Don't forget this
    rebuild_settings();
    $db->delete_query("templates", "title = 'showthread_archiving'");
    $db->delete_query("templates", "title = 'showthread_archiving_misc'");


}

function user_archiving_activate()
{
    require MYBB_ROOT."/inc/adminfunctions_templates.php";
    find_replace_templatesets("showthread", "#".preg_quote('{$newreply}')."#i", '{$user_archiving}{$newreply}');
}

function user_archiving_deactivate()
{
    require MYBB_ROOT."/inc/adminfunctions_templates.php";
    find_replace_templatesets("showthread", "#".preg_quote('{user_archiving}')."#i", '', 0);
}
$plugins->add_hook("showthread_start", "user_archiving_showthread");
function user_archiving_showthread(){
global $db, $tid, $templates, $mybb, $user_archiving,$forum, $thread;

$inplay = $mybb->settings['user_archiving_inplay'];


    if(preg_match("/$inplay,/i", $forum['parentlist'])) {
        if(($mybb->user['uid'] == $thread['uid'])) {
            eval("\$user_archiving = \"" . $templates->get("showthread_archiving") . "\";");
        }elseif($mybb->usergroup['canmodcp'] == 1){
            eval("\$user_archiving = \"" . $templates->get("showthread_archiving") . "\";");
        }
    }
}

$plugins->add_hook('misc_start', 'user_archiving_misc');
function user_archiving_misc(){
    global $mybb, $templates, $lang, $header, $headerinclude, $footer, $db, $page, $archiv_option, $cache;

    if($mybb->get_input('action') == 'archiving')
    {
        // Do something, for example I'll create a page using the hello_world_template

        // Add a breadcrumb
        add_breadcrumb('Thread archivieren', "misc.php?action=archiving");

            $tid = $mybb->input['tid'];
            $archiv = $mybb->settings['user_archiving_archiv'];
        $forum_cache = $cache->read("forums");
            $query_thread = $db->query("SELECT *
            FROM ".TABLE_PREFIX."threads t
            LEFT JOIN ".TABLE_PREFIX."users u
            on (t.uid = u.uid)
            LEFT JOIN ".TABLE_PREFIX."forums f
            on (t.fid = f.fid)
            WHERE tid = '".$tid."'
            ");

            $thread = $db->fetch_array($query_thread);
            $old_fid = $thread['fid'];

        $username = format_name($thread['username'], $thread['usergroup'], $thread['displaygroup']);
        $user = build_profile_link($username, $thread['uid']);
        $thread['spieler'] = "<i class=\"fa fa-group\" aria-hidden=\"true\"></i> ".$thread['spieler'];

        if($thread['day'] != ''){
            $thread['datum'] = $thread['day'] . "." . $thread['month'] . "." . $thread['year'];
            $thread['datum'] = "<i class=\"fa fa-calendar\" aria-hidden=\"true\"></i> ".$thread['datum'];
        }
        if($thread['ort'] != ''){
            $thread['ort'] = "<i class=\"fa fa-map-signs\" aria-hidden=\"true\"></i> ".$thread['ort'];
        }
        $thread_info = "{$thread['spieler']} {$thread['datum']} {$thread['ort']} ";
        $thread_old = $thread['name'];
        $thread = "<a href='showthread.php?tid={$tid}'>{$thread['subject']}</a> <br /> 
                     {$thread_info}";



        $archiv_query = $db->query("SELECT *
        FROM ".TABLE_PREFIX."forums
       where parentlist LIKE '%".$archiv.",%'
       and not fid = '".$old_fid."'
       ORDER BY name ASC 
        ");


        while($forum = $db->fetch_array($archiv_query)){
            $fid = $forum['fid'];
            $forum_name = $forum['name'];
            $archiv_option .= "<option value='{$fid}'>{$forum_name}</option>";
        }


        if (isset($_POST['archive'])) {
            verify_post_check($mybb->get_input('my_post_key'));
            $tid = $_POST['tid'];
            $new_fid = $_POST['archiv_forum'];

            $db->query("UPDATE ".TABLE_PREFIX."threads SET fid ='".$new_fid."' WHERE tid = '".$tid."'");
            $db->query("UPDATE ".TABLE_PREFIX."posts SET fid ='".$new_fid."' WHERE tid = '".$tid."'");
            require_once MYBB_ROOT . "inc/functions_rebuild.php";
            rebuild_forum_counters($old_fid);
            rebuild_forum_counters($new_fid);
            redirect ("showthread.php?tid={$tid}", "Dein Thread wurde archiviert. Du wirst nun zu diesem zurückgeleitet.");
        }
        // Using the misc_help template for the page wrapper
        eval("\$page = \"".$templates->get("showthread_archiving_misc")."\";");
        output_page($page);
    }
}
