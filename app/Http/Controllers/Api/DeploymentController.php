<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Functionality;
use App\Models\Release;
use App\Services\InitiativeService;
use Illuminate\Http\Request;
use App\Services\MytcpdfService;
use App\Services\ReleaseNotePdfService;

class DeploymentController extends Controller
{

    public function index(Request $request, $initiative_id)
    {
        $status = false;
        $initiative = InitiativeService::getInitiative($request, $initiative_id);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        $filters = $request->filters;
        $release = Release::select(
            'id',
            'initiative_id',
            'name',
            'is_major',
            'version',
            'status',
            'tags',
            'processed_at',
            'created_at',
        )
            ->withCount('tickets')
            ->where('initiative_id', $initiative_id)
            ->when($filters['name'] != '', function ($query) use ($filters) {
                $query->whereLike('name', '%' . $filters['name'] . '%');
            })
            ->when($filters['ticket_name'] != '', function ($query) use ($filters) {
                $query->whereHas('tickets.ticket', function ($query) use ($filters) {
                    $query->whereLike('composed_name', '%' . $filters['ticket_name'] . '%');
                });
            })
            ->when(!empty($filters['functionalities']), function ($query) use ($filters) {
                $query->whereHas('tickets.ticket', function ($query) use ($filters) {
                    $query->whereHas('functionality', function ($query) use ($filters) {
                        $query->whereIn('id', array_column($filters['functionalities'], 'id'));
                    });
                });
            })
            ->paginate(10);
        return ApiHelper::response(true, '', $release, 200);
    }

    public function getInitiativeDataForDeployments(Request $request, $initiative_id)
    {
        $status = false;
        $initiative = InitiativeService::getInitiative($request, $initiative_id);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $functionalities = Functionality::whereHas('section', function ($query) use ($initiative_id) {
            $query->where('initiative_id', $initiative_id);
        })->get(['id', 'display_name']);
        $data = array(
            'functionalities' => $functionalities,
        );
        return ApiHelper::response(true, '', $data, 200);
    }

    public function downloadReleaseNotes(Request $request)
    {
        $status = false;
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $release = Release::select(
            'id',
            'initiative_id',
            'name',
        )
            ->with([
                'tickets' => function ($query) {
                    $query->select(
                        'id',
                        'release_id',
                        'ticket_id',
                    )
                        ->with([
                            'ticket' => function ($query) {
                                $query->select('id', 'name', 'composed_name', 'release_note');
                            }
                        ]);
                }
            ])
            ->where('initiative_id', $initiative->id)
            ->where('id', $request->input('release_id'))
            ->first();

        if (!$release) {
            return ApiHelper::response($status, __('messages.solution_design.section.release_not_exist'), '', 400);
        }
        $pdfTitle = trans('messages.deployment.download_release_notes_pdf_title', ['INITIATIVE_NAME' => $initiative->name, 'RELEASE_NAME' => $release->name]);

        $pdf = new ReleaseNotePdfService();
        $pdf->SetTitle($pdfTitle);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetMargins(0, 30, 0);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setFontSubsetting(false);
        $tagvs = [
            'p' => [
                0 => ['h' => 0, 'n' => 0],
            ],
            'ul' => [
                0 => ['h' => 0.5, 'n' => 1],
                1 => ['h' => 0.5, 'n' => 1],
            ],
        ];
        $pdf->setHtmlVSpace($tagvs);

        $fontname = \TCPDF_FONTS::addTTFfont(public_path() . '/fonts/Nunito/Nunito-Regular.ttf', 'TrueTypeUnicode');
        $pdf->SetFont($fontname);
        $fontname = \TCPDF_FONTS::addTTFfont(public_path() . '/fonts/Nunito/Nunito-Italic.ttf', 'TrueTypeUnicode');
        $pdf->SetFont($fontname);
        $fontname = \TCPDF_FONTS::addTTFfont(public_path() . '/fonts/Nunito/Nunito-SemiBold.ttf', 'TrueTypeUnicode');
        $pdf->SetFont($fontname);
        $fontname = \TCPDF_FONTS::addTTFfont(public_path() . '/fonts/Nunito/Nunito-Bold.ttf', 'TrueTypeUnicode');
        $pdf->SetFont($fontname);
        $fontname = \TCPDF_FONTS::addTTFfont(public_path() . '/fonts/Nunito/Nunito-BoldItalic.ttf', 'TrueTypeUnicode');
        $pdf->SetFont($fontname);

        $pdf->data = compact('release');

        $coverHtml = view('deployment.release-note-pdf.cover_html', compact('initiative', 'release'))->render();

        $pdf->setPrintHeader(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->AddPage();
        $pdf->Rect(0, 0, $pdf->getPageWidth(), $pdf->getPageHeight() - 17, 'DF', array('width' => 0),  array(61, 98, 166));
        $img_file = public_path() . '/images/pdf_cover2.png';
        $pdf->Image($img_file, 0, 50, 90);
        $pdf->writeHTMLCell(0, 0, 95, 72, $coverHtml);

        $pdf->SetMargins(0, 30, 0);
        $pdf->setPrintHeader(true);

        $tickets = $release->tickets;
        $tableContentHTML = view('deployment.release-note-pdf.table_content_html', compact('tickets'));
        $pdf->AddPage();
        $pdf->WriteHTML($tableContentHTML);

        foreach ($tickets as $ticket) {
            $pdfHtml = view('deployment.release-note-pdf.main_content_pdf_html', compact('ticket'));
            $pdf->AddPage();
            $pdf->WriteHTML($pdfHtml, true, 0, true, 0);
        }

        $pdfContent = $pdf->Output($pdfTitle . '.pdf', 'S');
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="example.pdf"');
    }

    public function downloadTestResults(Request $request)
    {
        $status = false;
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $release = Release::select(
            'id',
            'initiative_id',
            'name',
        )
            ->with([
                'tickets.ticket' => function ($query) {
                    $query->select('id', 'name', 'composed_name')
                        ->with([
                            'testCases' => function ($query) {
                                $query->select(
                                    'id',
                                    'ticket_id',
                                    'expected_behaviour',
                                    'owner_id',
                                    'status',
                                    'observations',
                                );
                            }
                        ]);
                }
            ])
            ->where('initiative_id', $initiative->id)
            ->where('id', $request->input('release_id'))
            ->first();

        if (!$release) {
            return ApiHelper::response($status, __('messages.solution_design.section.release_not_exist'), '', 400);
        }

        // $pdfTitle = trans('messages.deployment.download_release_notes_pdf_title', ['RELEASE_NAME' => $release->name]);
        $pdfTitle = trans('messages.deployment.download_test_case_pdf_title', ['RELEASE_NAME' => $release->name]);

        $pdf = new MytcpdfService();
        $pdf->SetTitle($pdfTitle);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetMargins(0, 30, 0);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setFontSubsetting(false);
        $tagvs = [
            'p' => [
                0 => ['h' => 0, 'n' => 0],
            ],
            'ul' => [
                0 => ['h' => 0.5, 'n' => 1],
                1 => ['h' => 0.5, 'n' => 1],
            ],
        ];
        $pdf->setHtmlVSpace($tagvs);

        $fontname = \TCPDF_FONTS::addTTFfont(public_path() . '/fonts/Nunito/Nunito-Regular.ttf', 'TrueTypeUnicode');
        $pdf->SetFont($fontname);
        $fontname = \TCPDF_FONTS::addTTFfont(public_path() . '/fonts/Nunito/Nunito-Italic.ttf', 'TrueTypeUnicode');
        $pdf->SetFont($fontname);
        $fontname = \TCPDF_FONTS::addTTFfont(public_path() . '/fonts/Nunito/Nunito-SemiBold.ttf', 'TrueTypeUnicode');
        $pdf->SetFont($fontname);
        $fontname = \TCPDF_FONTS::addTTFfont(public_path() . '/fonts/Nunito/Nunito-Bold.ttf', 'TrueTypeUnicode');
        $pdf->SetFont($fontname);
        $fontname = \TCPDF_FONTS::addTTFfont(public_path() . '/fonts/Nunito/Nunito-BoldItalic.ttf', 'TrueTypeUnicode');
        $pdf->SetFont($fontname);

        $pdf->data = compact('initiative');

        $coverHtml = view('deployment.release-note-pdf.cover_html', compact('initiative'))->render();

        $pdf->setPrintHeader(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->AddPage();
        $pdf->Rect(0, 0, $pdf->getPageWidth(), $pdf->getPageHeight() - 17, 'DF', array('width' => 0),  array(61, 98, 166));
        $img_file = public_path() . '/images/pdf_cover2.png';
        $pdf->Image($img_file, 0, 50, 90);
        $pdf->writeHTMLCell(0, 0, 95, 72, $coverHtml);

        $pdfContent = $pdf->Output($pdfTitle . '.pdf', 'S');
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="example.pdf"');
    }
}
