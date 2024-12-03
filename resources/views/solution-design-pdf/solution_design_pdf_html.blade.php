<style>
    p {
        margin: 0;
        padding: 0;
    }
</style>
@php
    $titleBlockColor = '#f6cf47';
    if ($template->title_block_color != null) {
        $titleBlockColor = $template->title_block_color;
    }
    $primaryColor = '#3D62A6';
    if ($template->primary_color != null) {
        $primaryColor = $template->primary_color;
    }
@endphp
<table style="margin:0;padding:0;" border="0" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="3" width="100%" align="left">
            <table style="margin:0;padding:0;" border="0" width="100%" cellpadding="3" cellspacing="0">
                <tr>
                    <td width="4%">&nbsp;</td>
                    <td width="2%" style="background-color: {{ $titleBlockColor }};">&nbsp;</td>
                    <td width="2%">&nbsp;</td>
                    <td width="84%" align="left" valign="middle"
                        style="font-size:21px;color:{{ $primaryColor }};font-family: Nunito-Bold;">
                        {{ $sectionsWithFunctionality->display_name }}
                    </td>
                    <td width="8%" align="left" valign="middle">&nbsp;</td>
                </tr>
                @forelse ($sectionsWithFunctionality->functionalities as $eachFunctionality)
                    <tr>
                        <td width="8%" align="left" valign="middle">&nbsp;</td>
                        <td width="84%" align="left" valign="middle">
                            <div style="font-size:14px;color:{{ $primaryColor }};{{ ' font-family: Nunito-Bold;' }}">
                                {{ $eachFunctionality->display_name }}
                            </div>
                            <div style="font-size: 12px;color: #000000;font-family: Nunito-Regular; margin-top: 6px">
                                {!! $eachFunctionality->description !!}
                            </div>
                        </td>
                        <td width="8%" align="left" valign="middle">&nbsp;</td>
                    </tr>
                @empty
                @endforelse
            </table>
        </td>
    </tr>
</table>
