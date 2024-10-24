<style>
    p {
        margin: 0;
        padding: 0;
    }
</style>
<table style="margin:0;padding:0;" border="0" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="3" width="100%" align="left">
            <table style="margin:0;padding:0;" border="0" width="100%" cellpadding="3" cellspacing="0">
                <tr>
                    <td width="4%">&nbsp;</td>
                    <td width="2%" style="background-color: #f6cf47;">&nbsp;</td>
                    <td width="2%">&nbsp;</td>
                    <td width="84%" align="left" valign="middle"
                        style="font-size:21px;color:#3D62A6;font-family: Nunito-Bold;">
                        {{ $ticket->ticket->composed_name ?? '' }}
                    </td>
                    <td width="8%" align="left" valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <td width="8%" align="left" valign="middle">&nbsp;</td>
                    <td width="84%" align="left" valign="middle">
                        <div style="font-size: 12px;color: #000000;font-family: Nunito-Regular; margin-top: 6px">
                            {!! $ticket->ticket->release_note ?? '' !!}
                        </div>
                    </td>
                    <td width="8%" align="left" valign="middle">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
