<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 9/29/18
 * Time: 5:45 PM
 */

namespace App\Http\Interfaces\Web;

interface SpecialityInterface
{

    /**
     * get all doctors belong to this speciality
     * @param $slug
     * @return mixed
     */
    public function getDoctorsBySpecialitySlug($slug);

    /**
     * get first speciality
     *
     * @return mixed
     */
    public static function getFirstSpeciality();

    /**
     *  get speciality by id
     *
     * @param $id
     * @return mixed
     */
    public static function getSpecialityById($id);

    /**
     *  get speciality by id with locale
     * @param $id
     * @return mixed
     */

    public function getSpecialityBySlugWithLocale($id);


    /**
     *  get all specialties
     *
     * @return mixed
     */
    public function getAllSpecialities();

    /**
     *  create new speciality
     *
     * @param $request
     * @return mixed
     */
    public function createSpeciality($request);

    /**
     *  update speciality
     *
     * @param $speciality
     * @param $request
     * @return mixed
     */
    public function updateSpeciality($speciality, $request);

    /**
     * get count of featured specialities
     *
     * @param string $id
     * @return mixed
     */
    public function getFeaturedSpecialitiesCount($id = '');

    /**
     *  set featured status
     *
     * @param $speciality
     * @param $status
     * @return mixed
     */
    public function setFeatured($speciality, $status);

    /**
     *  get list of featured specialities
     *
     * @param $limit
     * @param $featured
     * @return mixed
     */
    public function getFeaturedSpecialities($limit, $featured);


    /**
     *  get list of specialities with sponsored doctors
     *
     * @return mixed
     */
    public function getAllSpecialitiesWithSponsored();

    /**
     *  get specific specialty with sponsor
     *
     * @param $id
     * @return mixed
     */
    public function getSpecialityWithSponsord($id);
}
