# szenenarchivierung

// Vorraussetzunge
- Inplaytracker von Ales muss installiert sein
- Font-Awesome muss eingefügt sein

// Templates

##showthread_archiving##
<a href="misc.php?action=archiving&tid={$tid}" class="button new_reply_button"><span>Thread Archivieren</span></a>&nbsp;

##showthread_archiving_misc##
<html>
<head>
<title>Threadarchivierung</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>Thema Archiveren</strong></td>
</tr>
<tr>
<td class="tcat" align="center" width="25%">
	<strong>Themenersteller</strong>
	</td><td class="trow1" style="padding-left: 10px;">{$user}</td>
</tr>
<tr><td class="tcat" align="center" width="25%">
	<strong>Thread</strong>
	</td><td class="trow1" style="padding-left: 10px;">{$thread}</td>
</tr>
	<tr><td class="tcat" align="center" width="25%">
	<strong>Aktueller Ort</strong>
	</td><td class="trow1" style="padding-left: 10px;">{$thread_old}</td>
</tr>
	<tr><td class="tcat" align="center" width="25%">
	<strong>Archivieren</strong>
	</td><td class="trow1" style="padding-left: 10px;">
		<form action="misc.php?action=archiving&tid={$tid}" id="places" method="post">
			<input type="hidden" name="tid" id="tid" value="{$tid}" class="textbox" />
			<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
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
</html>
