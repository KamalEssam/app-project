<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:48 PM
 */

namespace App\Http\Repositories\Web;

use App\Http\Interfaces\Web\SubSpecialityInterface;
use App\Models\SubSpeciality;
use Illuminate\Database\Eloquent\Collection;

class SubSpecialityRepository extends ParentRepository implements SubSpecialityInterface
{
    public $speciality;

    public function __construct()
    {
        $this->speciality = new SubSpeciality();
    }

    /**
     *  get speciality by id
     *
     * @param $id
     * @return mixed
     */
    public function getSubSpecialityById($id)
    {
        try {
            return $this->speciality->find($id);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    /**
     *  get sub speciality by id with locale
     *
     * @param $slug
     * @return mixed
     */
    public function getSubSpecialityBySlugWithLocale($slug)
    {
        try {
            return $this->speciality
                ->select('id', app()->getLocale() . '_speciality as speciality')->where("slug", $slug)
                ->first();


        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }


    /**
     *  get all specialties
     *
     * @param $id
     * @return mixed
     */
    public function getAllSubSpecialities($id)
    {
        try {
            return $this->speciality
                ->where('speciality_id', $id)
                ->orderBy('created_at')
                ->select('id', app()->getLocale() . '_name as name')->get();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }

    /**
     *  create new speciality
     *
     * @param $data
     * @return mixed
     */
    public function createSubSpeciality($data)
    {
        try {
            return $this->speciality->create($data);
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }

    }

    /**
     *  update sub speciality
     *
     * @param $sub_speciality
     * @param $request
     * @return mixed
     */
    public function updateSubSpeciality($sub_speciality, $request)
    {
        try {
            return $sub_speciality->update($request->all());

        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return false;
        }
    }

    public function apiAllSubSpecialities($id)
    {
        try {
            return $this->speciality
                ->where('speciality_id', $id)
                ->select(app()->getLocale() . '_name as name', 'id')->get()->toArray();
        } catch (\Exception $e) {
            self::logErr($e->getMessage());
            return new Collection();
        }
    }
}
