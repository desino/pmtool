<p style="font-size: 32px;color: #FFFFFF;font-family: Nunito-Bold;">
    {{ __('messages.solution_design_pdf_cover_page_title')}}
</p>
<p style="font-size: 15px;font-family: Nunito-Bold;color: #FFFFFF;">
    {{ __('messages.solution_design_pdf_cover_page_client_title', ['CLIENT_NAME' => $initiative->client->name])}}
</p>
<p style="font-size: 17px;font-family: Nunito-BoldItalic;color: #FFFFFF;line-height: 0.5em;">
    {{ __('messages.solution_design_pdf_cover_page_initiative_title', ['INITIATIVE_NAME' => $initiative->name])}}
</p>