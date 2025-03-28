@php
    $titleBlockColor = '#f6cf47';
    if ($template?->title_block_color != null) {
        $titleBlockColor = $template->title_block_color;
    }
    $primaryColor = '#3D62A6';
    if ($template?->primary_color != null) {
        $primaryColor = $template->primary_color;
    }
@endphp
<table style="margin:0;padding:0;" border="0" width="100%" cellpadding="3" cellspacing="0">
    <tr>
        <td width="4%">&nbsp;</td>
        <td width="2%" style="background-color: {{ $titleBlockColor }};">&nbsp;</td>
        <td width="2%">&nbsp;</td>
        <td width="84%" align="left" valign="middle"
            style="font-size:21px;color:{{ $primaryColor }};font-family: Nunito-Bold;">
            {{ trans('messages.solution_design_pdf_table_of_content_section_title') }}
        </td>
        <td width="8%" align="left" valign="middle">&nbsp;</td>
    </tr>
    @forelse ($sectionsWithFunctionalities as $eachSectionsWithFunctionality)
        <tr>
            <td width="8%">&nbsp;</td>
            <td width="84%" align="left" valign="middle">
                <div style="font-size:14px;color:{{ $primaryColor }};font-family: Nunito-Bold;">
                    {{ $eachSectionsWithFunctionality->display_name ?? '' }}
                </div>
                @forelse ($eachSectionsWithFunctionality->functionalities as $eachFunctionality)
                    <div style="font-size:14px;color:#000000;font-family: Nunito-Regular;">
                        &nbsp;&nbsp;&nbsp;&nbsp; {{ $eachFunctionality->display_name }}
                    </div>
                @empty
                @endforelse
            </td>
            <td width="8%">&nbsp;</td>
        </tr>
    @empty
    @endforelse
</table>
