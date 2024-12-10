<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FunctionalityRequest;
use App\Http\Requests\Api\SectionRequest;
use App\Models\Functionality;
use App\Models\Section;
use App\Models\Template;
use App\Services\ClientService;
use App\Services\InitiativeService;
use App\Services\MytcpdfService;
use App\Services\SolutionDesignService;
use TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolutionDesignController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.solution_design.dont_have_permission'), null, 404);
        }
        $initiative = InitiativeService::getInitiative($request, $request->get('initiative_id'));
        if (!$initiative) {
            return ApiHelper::response(false, __('messages.solution_design.section.initiative_not_exist'), '', 404);
        }
        $getSectionsWithFunctionalities = SolutionDesignService::getSectionsWithFunctionalities($request);
        $meta_data['initiative'] = $initiative;
        return ApiHelper::response(true, '', $getSectionsWithFunctionalities, 200, $meta_data);
    }

    public function getSectionFunctionality(Request $request)
    {
        $initiative = InitiativeService::getInitiative($request, $request->get('initiative_id'));
        if (!$initiative) {
            return ApiHelper::response(false, __('messages.solution_design.section.initiative_not_exist'), '', 404);
        }
        $functionality = Functionality::select(
            'id',
            'id AS functionality_id',
            'section_id',
            'name',
            'description',
            DB::RAW('CASE WHEN include_in_solution_design = 1 THEN true ELSE false END AS include_in_solution_design'),
        )->find($request->get('id'));
        if (!$functionality) {
            return ApiHelper::response(false, __('messages.solution_design.functionality.functionality_not_exist'), '', 404);
        }
        $functionality->setAttribute('initiative_id', $initiative->id);
        $retData = [
            'functionalityData' => $functionality
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }

    public function solutionDesignRead(Request $request)
    {
        // return ApiHelper::response(false, __('messages.solution_design.dont_have_permission'), null, 404);
        $authUser = Auth::user();
        // if (!$authUser->is_admin) {
        //     return ApiHelper::response(false, __('messages.solution_design.dont_have_permission'), null, 404);
        // }
        $initiative = InitiativeService::getInitiative($request, $request->get('initiative_id'));
        if (!$initiative) {
            return ApiHelper::response(false, __('messages.solution_design.section.initiative_not_exist'), '', 404);
        }
        $getSectionsWithFunctionalities = SolutionDesignService::solutionDesignRead($request);
        $meta_data['initiative'] = $initiative;
        return ApiHelper::response(true, '', $getSectionsWithFunctionalities, 200, $meta_data);
    }
    public function downloadList(Request $request)
    {
        $authUser = Auth::user();
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response(false, __('messages.solution_design.initial_data_not_found'), '', 200);
        }
        if (!$authUser->is_admin && $initiative->functional_owner_id != $authUser->id && $initiative->technical_owner_id != $authUser->id) {
            return ApiHelper::response(false, __('messages.solution_design.dont_have_permission'), null, 404);
        }
        $getSectionsWithFunctionalities = SolutionDesignService::getSectionsWithFunctionalitiesForDownloadList($request);
        return ApiHelper::response(true, '', $getSectionsWithFunctionalities, 200);
    }
    public function downloadPDF(Request $request)
    {
        $authUser = Auth::user();
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response(false, __('messages.solution_design.initial_data_not_found'), '', 200);
        }
        if (!$authUser->is_admin && $initiative->functional_owner_id != $authUser->id && $initiative->technical_owner_id != $authUser->id) {
            return ApiHelper::response(false, __('messages.solution_design.dont_have_permission'), null, 404);
        }
        $sectionsWithFunctionalities = SolutionDesignService::getSectionsWithFunctionalitiesForDownloadList($request);


        $pdfTitle = trans('messages.solution_design_pdf_title', ['INITIATIVE_NAME' => $initiative->name]);
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

        $template = $initiative->template;
        $coverHtmlPageColor = array(61, 98, 166);
        if ($template?->primary_color) {
            $coverHtmlPageColor = Template::hexToRgb($template?->primary_color);
        }
        $pdf->data = compact('initiative', 'template');


        $coverHtml = view('solution-design-pdf.cover_html', compact('initiative'))->render();

        $pdf->setPrintHeader(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->AddPage();
        $pdf->Rect(0, 0, $pdf->getPageWidth(), $pdf->getPageHeight() - 17, 'DF', array('width' => 0),  $coverHtmlPageColor);
        $img_file = public_path() . '/images/default_pdf/pdf_cover2.png';
        $pdf->Image($img_file, 0, 50, 90);
        $pdf->writeHTMLCell(0, 0, 95, 72, $coverHtml);

        $pdf->SetMargins(0, 30, 0);
        $pdf->setPrintHeader(true);

        $solutionDesignTableContentHTML = view('solution-design-pdf.table_content_html', compact('sectionsWithFunctionalities', 'template'));
        $pdf->AddPage();
        $pdf->WriteHTML($solutionDesignTableContentHTML);

        foreach ($sectionsWithFunctionalities as $sectionsWithFunctionality) {
            $pdfHtml = view('solution-design-pdf.solution_design_pdf_html', compact('sectionsWithFunctionality', 'template'));
            $pdf->AddPage();
            $pdf->WriteHTML($pdfHtml, true, 0, true, 0);
        }

        $pdfContent = $pdf->Output($pdfTitle . '.pdf', 'S');
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="example.pdf"');
    }
    public function getInitiative(Request $request)
    {
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response(false, __('messages.solution_design.initial_data_not_found'), '', 200);
        }
        return ApiHelper::response(true, '', $initiative, 200);
    }

    public function storeSection(SectionRequest $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.solution_design.dont_have_permission'), null, 404);
        }
        $status = false;
        $section = collect([]);

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        $status = true;
        $message = __('messages.solution_design.section.store_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $postData = $request->post();
            $postData['name'] = $request->post('section_name');
            $section = Section::create($postData);
            $section->functionalities;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, $section, $statusCode);
    }

    public function updateSection(SectionRequest $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.solution_design.dont_have_permission'), null, 404);
        }
        $status = false;
        $section = collect([]);

        $section = SolutionDesignService::getSection($request->post('section_id'), $request->post('initiative_id'));
        if (!$section) {
            return ApiHelper::response($status, __('messages.solution_design.section.store.section_not_exist'), '', 400);
        }

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        $status = true;
        $statusCode = 200;
        $message = __('messages.solution_design.section.update_success');
        DB::beginTransaction();
        try {
            $section = SolutionDesignService::updateSection($request, $section);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, $section, $statusCode);
    }

    public function storeUpdateFunctionality(FunctionalityRequest $request)
    {
        $validateData = $request->validated();
        $status = false;
        $retData = [
            'functionality' => ""
        ];

        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response($status, __('messages.solution_design.dont_have_permission'), null, 404);
        }
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.sectino.client_not_exist'), '', 400);
        }

        $section = SolutionDesignService::getSection($request->post('section_id'), $request->post('initiative_id'));
        if (!$section) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.section_not_exist'), '', 400);
        }

        if ($request->post('functionality_id')) {
            $functionality = Functionality::find($request->post('functionality_id'));
            if (!$functionality) {
                return ApiHelper::response($status, __('messages.solution_design.functionality.functionality_not_exist'), '', 400);
            }
        }

        DB::beginTransaction();
        try {
            $status = true;
            $transactionType = "";
            if (isset($validateData['functionality_id'])) {
                $functionality = SolutionDesignService::updateFunctionality($request, $validateData);
                $message = __('messages.solution_design.functionality.update_success');
                $transactionType = "updated";
            } else {
                $functionality = SolutionDesignService::storeFunctionality($request, $validateData);
                $message = __('messages.solution_design.functionality.store_success');
                $transactionType = "created";
            }
            $statusCode = 200;
            $retData = [
                'functionality' => $functionality,
                'transactionType' => $transactionType
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, $retData, $statusCode);
    }

    public function deleteFunctionality(Request $request)
    {
        $status = false;

        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.solution_design.dont_have_permission'), null, 404);
        }
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        $section = SolutionDesignService::getSection($request->post('section_id'), $request->post('initiative_id'));
        if (!$section) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.section_not_exist'), '', 400);
        }

        $functionality = Functionality::with('tickets')->where('section_id', $request->post('section_id'))->find($request->post('id'));
        if (!$functionality) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.functionality_not_exist'), '', 400);
        }
        if ($functionality->tickets->count() > 0) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.functionality_has_ticket'), '', 400);
        }

        $status = true;
        $statusCode = 200;
        $message = __('messages.solution_design.functionality.delete_success');
        DB::beginTransaction();
        try {
            $functionality->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }
    public function deleteSection(Request $request)
    {
        $status = false;
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response($status, __('messages.solution_design.dont_have_permission'), null, 404);
        }
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        $section = SolutionDesignService::getSection($request->post('id'), $request->post('initiative_id'));
        if (!$section) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.section_not_exist'), '', 400);
        }
        $hasTickets = $section->functionalities()->whereHas('tickets')->exists();
        if ($hasTickets) {
            return ApiHelper::response($status, __('messages.solution_design.section.has_ticket'), '', 400);
        }

        $status = true;
        $statusCode = 200;
        $message = __('messages.solution_design.section.delete_success');
        DB::beginTransaction();
        try {
            $section->functionalities()->delete();
            $section->delete();
            // SolutionDesignService::deleteSection($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }

    public function updateFunctionalityOrderNo(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.solution_design.dont_have_permission'), null, 404);
        }
        $statusCode = 200;
        $status = false;
        $message = __('messages.solution_design.section.update_functionality_order_no_success');

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        $section = SolutionDesignService::getSection($request->post('section_id'), $request->post('initiative_id'));
        if (!$section) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.section_not_exist'), '', 400);
        }

        $functionality = Functionality::where('section_id', $request->post('section_id'))->find($request->post('id'));
        if (!$functionality) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.functionality_not_exist'), '', 400);
        }

        DB::beginTransaction();
        try {
            SolutionDesignService::updateFunctionalityOrderNo($request);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
            return ApiHelper::response($status, $message, collect([]), $statusCode);
        }

        $updatedFunctionality = Functionality::find($request->post('id'));
        return ApiHelper::response($status, $message, $updatedFunctionality, $statusCode);
    }

    public function updateSectionOrderNo(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.solution_design.dont_have_permission'), null, 404);
        }
        $status = false;

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        $section = SolutionDesignService::getSection($request->post('id'), $request->post('initiative_id'));
        if (!$section) {
            return ApiHelper::response($status, __('messages.solution_design.functionality.section_not_exist'), '', 400);
        }

        $status = true;
        $statusCode = 200;
        $message = __('messages.solution_design.section.update_section_order_no_success');
        DB::beginTransaction();
        try {
            $retData = SolutionDesignService::updateSectionOrderNo($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }
}
