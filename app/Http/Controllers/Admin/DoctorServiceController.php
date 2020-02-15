<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\AccountRepository;
use App\Http\Repositories\Web\DoctorServiceRepository;
use App\Http\Requests\DoctorServiceRequest;
use DB;

class DoctorServiceController extends WebController
{
    private $service;

    public function __construct(DoctorServiceRepository $serviceRepository)
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
        $services = $this->service->getDoctorServices(auth()->user()->account_id);
        if (!$services) {
            $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_services'));
        }
        return view('admin.doctor.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services_list = $this->service->getListOfServices();
        return view('admin.doctor.services.create', compact('services_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DoctorServiceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(DoctorServiceRequest $request)
    {
        DB::beginTransaction();
        $doctorAccount = AccountRepository::getAccountById(auth()->user()->account_id);
        // getting array of current services
        $current_services = array_values((new DoctorServiceRepository())->getArrayOfDoctorServices(auth()->user()->account_id));
        try {
            if ($request['service_id'] && $request['price']) {
                $data = array();
                // get old services first
                for ($i = 0, $iMax = count($request['service_id']); $i < $iMax; $i++) {

                    if (!in_array($request['service_id'][$i], $current_services)) {
                        if (isset($request['premium_price']) && !empty($request['price'][$i]) && !empty($request['premium_price'][$i])) {
                            if ($request['price'][$i] >= $request['premium_price'][$i]) {
                                $data[$request['service_id'][$i]] =
                                    array(
                                        'price' => $request['price'][$i],
                                        'premium_price' => $request['premium_price'][$i]
                                    );
                            }
                        } else if (!empty($request['price'][$i])) {
                            $data[$request['service_id'][$i]] = array(
                                'price' => $request['price'][$i]
                            );
                        }
                    }
                }
                if (count($data)) {
                    $doctorAccount->services()->attach($data);
                }
            }
        } catch (\Exception $ex) {
            self::logErr($ex);
            DB::rollBack();
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.service_add_err'));
        }
        DB::commit();
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.service_added_ok'), 'doctor-services.index');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $service = $this->service->getServiceById($id);
        if (!$service) {
            $this->messageAndRedirect(self::STATUS_ERR, trans('lang.service_not_found'));
        }
        return view('admin.doctor.services.edit', compact('service'));
    }

    /**
     * Update the specified service in DB
     *
     * @param DoctorServiceRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(DoctorServiceRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            if ($request->has('premium_price')) {
                if ($request['premium_price'] > $request['price']) {
                    $this->messageAndRedirect(self::STATUS_ERR, trans('lang.service_update_err'));
                }
            }
            $service = $this->service->getDoctorServiceByIdForEditing($id);
            $this->service->updateService($service, $request);

        } catch (\Exception $ex) {
            DB::rollBack();
            $this->messageAndRedirect(self::STATUS_ERR, trans('lang.service_update_err'));
        }
        DB::commit();

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.service_update_ok'), 'doctor-services.index');
    }

    /**
     * Remove the specified service from DB.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->service->service, $id);
    }

}
