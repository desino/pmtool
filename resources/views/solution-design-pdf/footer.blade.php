<table border="0" width="100%" cellpadding="0" cellspacing="0" style="background-color: #FFFFFF;">
	<tr>
		<td width="8%">&nbsp;</td>
		<td width="22%" valign="middle" align="left" style="color: #929292;font-size: 10px;font-family: Nunito-Bold;">
			{{ $project->name.' - Release '.$release->semver }}
		</td>
		<td width="50%" valign="middle" align="center">
			<img src="{{ public_path().'/images/pdf_footer_logo2.png' }}" style="width:350px;" />
		</td>
		<td width="12%" valign="middle" align="right" style="color: #929292;font-size: 10px;font-family: Nunito-Bold;">
			{{ $page_number }}
		</td>
		<td width="8%">&nbsp;</td>
	</tr>
</table>