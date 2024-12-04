<style>
    p {
        margin: 0;
        padding: 0;
    }
</style>
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
                        {{ $ticket->ticket->composed_name ?? '' }}
                    </td>
                    <td width="8%" align="left" valign="middle">&nbsp;</td>
                </tr>
                @forelse ($ticket->ticket->testCases as $eachTestCaseKey => $eachTestCase)
                    <tr>
                        <td width="8%" align="left" valign="middle">&nbsp;</td>
                        <td width="84%" align="left" valign="middle">
                            <table style="margin:0;padding:0;" border="0" width="100%" cellpadding="1"
                                cellspacing="0">
                                <tr>
                                    <td colspan="2"
                                        style="font-size: 18px;color: {{ $primaryColor }};font-family: Nunito-Bold; ">
                                        Test {{ $eachTestCaseKey + 1 }}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="4%">&nbsp;</td>
                                    <td>
                                        <div
                                            style="font-size: 11px;color: {{ $primaryColor }};font-family: Nunito-Bold; ">
                                            {{ __('messages.deployment.test_case_pdf_main_content_page_expected_behaviour') }}
                                        </div>
                                        <div>
                                            {!! $eachTestCase->expected_behaviour !!}
                                        </div>
                                    </td>
                                </tr>
                                @if ($eachTestCase->status != -1)
                                    <tr>
                                        <td width="4%">&nbsp;</td>
                                        <td>
                                            <div
                                                style="font-size: 11px;color: {{ $primaryColor }};font-family: Nunito-Bold; ">
                                                {{ __('messages.deployment.test_case_pdf_main_content_page_observerd_behaviour') }}
                                            </div>
                                            <div>
                                                {!! $eachTestCase->observations ?? '' !!}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="4%">&nbsp;</td>
                                        <td style="color: @if ($eachTestCase->status == 1) green @else red @endif">
                                            {{ $eachTestCase->status_label }}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                        <td width="8%" align="left" valign="middle">&nbsp;</td>
                    </tr>
                @empty
                @endforelse
            </table>
        </td>
    </tr>
</table>
