<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\ServiceInterface;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository extends ParentRepository implements ServiceInterface
{
    public $service;

    public function __construct()
    {
        $this->service = new Service();
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
     * @return mixed
     */
    public function getServices()
    {
        try {
            return $this->service->orderBy('created_at')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  insert multiple row of data
     *
     * @param $data
     * @return mixed
     */
    public function insertServices($data)
    {
        try {
            return $this->service->insert($data);

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get array of en_name
     *
     * @return mixed
     */
    public function getArrayOfServices()
    {
        try {
            return $this->service->pluck('en_name')->toArray();
        } catch (\Exception $e) {
            \DB::rollBack();
            self::logErr($e->getMessage());
            return array();
        }
    }


    /**
     *  get array of en_name
     *
     * @return mixed
     */
    public function getListOfServices()
    {
        try {
            return $this->service->pluck(app()->getLocale() . '_name', 'id');
        } catch (\Exception $e) {
            \DB::rollBack();
            self::logErr($e->getMessage());
            return array();
        }
    }


    /**
     *  get all services by id
     *
     * @return mixed
     */
    public function apiGetServices()
    {
        try {
            return $this->service->select('id', app()->getLocale() . '_name as name')->get();
        } catch (\Exception $e) {
            \DB::rollBack();
            self::logErr($e->getMessage());
            return array();
        }
    }

    /**
     *  add service from import
     *
     * @param $service
     * @return bool
     */
    public function addServiceFromImport($service)
    {
        try {
            return $this->service->create($service);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get list of services
     *
     * @param $id
     * @return Collection
     */
    public function getAllservices($id)
    {
        try {
            return $this->service
                ->where('speciality_id', $id)
                ->orderBy('created_at')
                ->select('id', app()->getLocale() . '_name as name')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }
}
