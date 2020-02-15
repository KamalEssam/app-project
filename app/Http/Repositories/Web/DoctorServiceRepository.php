<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\DoctorServiceInterface;
use App\Models\AccountService;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class DoctorServiceRepository extends ParentRepository implements DoctorServiceInterface
{
    public $service;

    public function __construct()
    {
        $this->service = new AccountService();
    }

    /**
     *  get speciality by id
     *
     * @param $id
     * @return mixed
     */
    public function getServiceById($id)
    {
        try {
            return $this->service
                ->join('services', 'services.id', 'account_service.service_id')
                ->where('account_service.id', $id)
                ->select('account_service.id', 'services.' . app()->getLocale() . '_name as name', 'account_service.price', 'account_service.premium_price')
                ->first();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function getDoctorServiceByIdForEditing($id)
    {
        try {
            return $this->service->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  create new speciality
     *
     * @param $request
     * @return mixed
     */
    public function createService($request)
    {
        try {
            return $this->service->create($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  update speciality
     *
     * @param $service
     * @param $request
     * @return mixed
     */
    public function updateService($service, $request)
    {
        try {
            return $service->update($request->all());
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get all services by id
     *
     * @param $account_id
     * @return mixed
     */
    public function getDoctorServices($account_id)
    {
        try {
            return $this->service
                ->join('services', 'services.id', 'account_service.service_id')
                ->where('account_service.account_id', $account_id)
                ->select('account_service.id', 'services.' . app()->getLocale() . '_name as name', 'account_service.price', 'account_service.premium_price')
                ->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  set status to premium request
     *
     * @param $service_id
     * @param $value
     * @return mixed
     */
    public function updateServicePremiumPrice($service_id, $value)
    {
        try {
            $service = $this->service->where('id', $service_id)->update([
                'premium_price' => $value
            ]);
            return $service;
        } catch (\Exception $e) {
            \DB::rollBack();
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *
     */
    public function getListOfServices()
    {
        try {
            return (new Service())->pluck(app()->getLocale() . '_name as name', 'id');
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    public function getArrayOfDoctorServices($account_id)
    {
        try {
            return $this->service->where('account_id', $account_id)->pluck('service_id')->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return array();
        }
    }

}
