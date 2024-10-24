<p style="font-size: 32px;color: #FFFFFF;font-family: Nunito-Bold;">
    {{ __('messages.release_note_pdf_cover_page_title') }}
</p>
<p style="font-size: 15px;font-family: Nunito-Bold;color: #FFFFFF;">
    {{ __('messages.release_note_pdf_cover_page_release_name', ['RELEASE_NAME' => $release->name]) }}
</p>
<p style="font-size: 15px;font-family: Nunito-Bold;color: #FFFFFF;">
    {{ __('messages.release_note_pdf_cover_page_client_title', ['CLIENT_NAME' => $initiative->client->name]) }}
</p>
<p style="font-size: 15px;font-family: Nunito-Bold;color: #FFFFFF;">
    {{ __('messages.release_note_pdf_cover_page_initiative_title', ['INITIATIVE_NAME' => $initiative->name]) }}
</p>
<p style="font-size: 15px;font-family: Nunito-Bold;color: #FFFFFF;">
    {{ __('messages.release_note_pdf_cover_page_export_at', ['DATE' => \Carbon\Carbon::now()->format('M d, Y')]) }}
</p>
{{-- <p style="font-size: 17px;font-family: Nunito-BoldItalic;color: #FFFFFF;line-height: 0.5em;">
    {{ __('messages.solution_design_pdf_cover_page_initiative_title', ['INITIATIVE_NAME' => $initiative->name]) }}
</p> --}}
