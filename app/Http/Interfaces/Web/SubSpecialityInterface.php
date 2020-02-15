<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface SubSpecialityInterface
{

    /**
     *  get sub speciality by id
     *
     * @param $id
     * @return mixed
     */
    public function getSubSpecialityById($id);

    /**
     *  get sub speciality by id with locale
     * @param $id
     * @return mixed
     */

    public function getSubSpecialityBySlugWithLocale($id);


    /**
     *  get all sub specialties
     *
     * @param $id
     * @return mixed
     */
    public function getAllSubSpecialities($id);

    /**
     *  create new speciality
     *
     * @param $data
     * @return mixed
     */
    public function createSubSpeciality($data);

    /**
     *  update sub speciality
     *
     * @param $speciality
     * @param $request
     * @return mixed
     */
    public function updateSubSpeciality($speciality, $request);
}
