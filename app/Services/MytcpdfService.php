<?php

namespace App\Services;

use TCPDF;

class MytcpdfService extends TCPDF
{
    public $data;

    public function Header()
    {
        $headerHTML = view('solution-design-pdf.header', $this->data)->render();
        $this->writeHTML($headerHTML);
    }

    public function Footer()
    {
        $image_file = public_path() . '/images/pdf_footer_logo2.png';
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
