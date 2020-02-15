<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\ServiceRepository;
use App\Http\Requests\ServiceRequest;
use App\Imports\ServicesImport;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ServiceController extends WebController
{
    private $service;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->service = $serviceRepository;
    }

    /**
     *  get all the services for the account
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $services = $this->service->getServices();
        if (!$services) {
            $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_services'));
        }
        return view('admin.rk-admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.rk-admin.services.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(ServiceRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = array();
            if ($request['en_name'] && $request['ar_name'] && $request['speciality_id']) {
                // get array of english name
                $names = $this->service->getArrayOfServices();
                $speciality_id = $request['speciality_id'];
                // get old services first
                for ($i = 0, $iMax = count($request['en_name']); $i < $iMax; $i++) {

                    $en_name = $request['en_name'][$i];
                    $ar_name = $request['ar_name'][$i];
                    // in case that the english name dont exists on the database and not null
                    if ($en_name != NULL && $ar_name != NULL) {
                        // check for unique
                        if (!in_array($en_name, $names)) {
                            $data[] = array(
                                'ar_name' => $ar_name,
                                'en_name' => $en_name,
                                'speciality_id' => $speciality_id,
                            );
                        }
                    }
                }
            }

            if (count($data) > 0) {
                $this->service->insertServices($data);
            }
        } catch (\Exception $ex) {
            self::logErr($ex);
            DB::rollBack();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.service_add_err'));
        }
        DB::commit();

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.service_added_ok'), 'services.index');
    }

    public function edit($id)
    {
        $service = $this->service->getServiceById($id);
        if (!$service) {
            $this->messageAndRedirect(self::STATUS_ERR, trans('lang.service_not_found'));
        }
        return view('admin.rk-admin.services.edit', compact('service'));
    }

    public function service_edit($id)
    {
        $service = $this->service->getServiceById($id);
        if (!$service) {
            $this->messageAndRedirect(self::STATUS_ERR, trans('lang.service_not_found'));
        }
        return view('admin.rk-admin.specialities.service-edit', compact('service'));
    }

    /**
     * Update the specified service in DB
     *
     * @param ServiceRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(ServiceRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $service = $this->service->getServiceById($id);
            $this->service->updateService($service, $request);
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->messageAndRedirect(self::STATUS_ERR, trans('lang.service_update_err'));
        }
        DB::commit();

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.service_update_ok'), 'services.index');
    }

    /**
     * Remove the specified service from DB.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->service->service, $id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importServices(Request $request)
    {
        if (!$request['services']) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.service_import_err'));
        }
        Excel::import(new ServicesImport(), $request['services']);
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.service_update_ok'), 'services.index');
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function all(Request $request)
    {
        if ($request->has('speciality_id')) {
            return $this->service->getAllservices($request['speciality_id']);
        }
    }
}
