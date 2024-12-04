<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use TCPDF;

class ReleaseNotePdfService extends TCPDF
{
	public $data;

	public function Header()
	{

		// $headerHTML = view('deployment.release-note-pdf.header', $this->data)->render();		

		$releaseName = __('messages.deployment.release_note_pdf_header_title', ['RELEASE_NAME' => $this->data['release']->name]);
		$templateData = $this->data['template'];

		$headerImagePath = public_path() . '/images/default_pdf/pdf_header_logo.png';
		if ($templateData?->white_logo_header != null && File::exists(public_path() . '/images/download_pdf/' . $templateData?->white_logo_header)) {
			$headerImagePath = public_path() . '/images/download_pdf/' . $templateData?->white_logo_header;
		}

		$headerBackgroundColor = '#3D62A6';
		if ($templateData?->primary_color != null) {
			$headerBackgroundColor = $templateData?->primary_color;
		}

		$headerHTML = '<table border="0" width="100%" cellpadding="-2" cellspacing="0" style="background-color: ' . $headerBackgroundColor . ';">
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td width="8%">&nbsp;</td>
				<td width="70%" align="left" valign="middle" style="color: #FFFFFF;font-size: 26px;font-family: nunitob">
					<div style="font-size:10pt">&nbsp;</div>' . $releaseName . '
				</td>
				<td width="20%" align="right">
					<img src="' . $headerImagePath . '" style="width: 60px;" />
				</td>
				<td width="2%">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
		</table>';

		$this->writeHTML($headerHTML);
	}

	public function Footer()
	{
		$templateData = $this->data['template'];
		$footerImagePath = public_path() . '/images/default_pdf/pdf_footer_logo2.png';
		if ($templateData?->logo_footer != null && File::exists(public_path() . '/images/download_pdf/' . $templateData?->logo_footer)) {
			$footerImagePath = public_path() . '/images/download_pdf/' . $templateData?->logo_footer;
		}
		$image_file = $footerImagePath;
		$this->Image($image_file, 70, $this->GetY() - 5, 90);

		$logo = '
		<table border="0" width="100%" cellpadding="0" cellspacing="0" style="background-color: #FFFFFF;">
			<tr>
				<td width="8%">&nbsp;</td>
				<td width="25%" valign="middle" align="left" style="color: #929292;font-size: 10px;font-family: Nunito-Bold;">
					' . $this->data['initiative']->name . '
				</td>
				<td width="50%" valign="middle" align="center">

				</td>
				<td width="10%" valign="middle" align="right" style="color: #929292;font-size: 10px;font-family: Nunito-Bold;">
					' . $this->PageNo() . '
				</td>
				<td width="8%">&nbsp;</td>
			</tr>
		</table>';
		$this->writeHTML($logo);

		/*$this->data['page_number'] = $this->PageNo();
		$tcpdf_footer_html = view('releases.tcpdf.tcpdf_footer', $this->data);
		//$this->writeHTMLCell(0, 0, '', '', $tcpdf_footer_html->render(), 0, 0, false, "L", true);
		$this->writeHTML($tcpdf_footer_html->render());*/
	}
}
