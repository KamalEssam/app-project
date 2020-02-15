<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LeadsExport;
use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\saleLeadsRepository;
use App\Http\Requests\SalesLeadsRequest;
use App\Imports\LeadsImport;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SalesLeadsController extends WebController
{
    private $saleLeadRepository;

    public function __construct(saleLeadsRepository $saleLeadRepository)
    {
        $this->saleLeadRepository = $saleLeadRepository;
    }

    /**
     *  show list of all cities
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $leads = $this->saleLeadRepository->getAllLeads();
        return view('admin.sale.leads.index', compact('leads'));
    }

    /**
     * Store new lead in database
     *
     * @param SalesLeadsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(SalesLeadsRequest $request)
    {
        // add lead
        DB::beginTransaction();
        try {
            $request['sale_id'] = auth()->user()->id;
            $this->saleLeadRepository->createLead($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.lead_add_err'));
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.lead_added_ok'), 'leads.index');
    }

    /**
     *  show edit lead form
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $lead = $this->saleLeadRepository->getLeadById($id);
        if (!$lead) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.lead_not_found'));
        }
        return view('admin.sale.leads.edit', compact('lead'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SalesLeadsRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(SalesLeadsRequest $request, $id)
    {
        $lead = $this->saleLeadRepository->getLeadById($id);
        if (!$lead) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.lead_not_found'));
        }

        DB::beginTransaction();
        // update lead data
        try {
            $this->saleLeadRepository->updateLead($lead, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.lead_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.lead_update_ok'), 'leads.index');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportLeads()
    {
        return Excel::download(new LeadsExport(), 'leads.xlsx');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importLeads(Request $request)
    {
        if(!$request['leads']){
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.lead_import_err'));
        }
        Excel::import(new leadsImport(), $request['leads']);
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.lead_update_ok'), 'leads.index');
    }

    /**
     * Remove Lead
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->saleLeadRepository->lead, $id);
    }
}
