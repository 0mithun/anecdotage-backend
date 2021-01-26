<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminReportResource;
use App\Models\Report;
use App\Models\Thread;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user', 'reported'])->paginate();
        return AdminReportResource::collection($reports);
    }


    public function destroy(Request $request, Report $report)
    {
        // $report->delete();

        return response(['success' => true, 'message' => 'Report Review Successfully!'], Response::HTTP_NO_CONTENT);
    }
}
