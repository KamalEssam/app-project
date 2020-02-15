<?php
/**
 * Created by PhpStorm.
 * User: rkanjel
 * Date: 10/2/18
 * Time: 11:41 AM
 */

namespace App\Http\Interfaces\Api;


interface DoctorInterface
{

    /**
     * get doctor by unique id
     * @param $unique_id
     * @return mixed
     */
    public static function getDoctorByUniqueId($unique_id);

    /**
     * get doctor by account id
     * @param $account_id
     * @return mixed
     */
    public static function getDoctorByAccountId($account_id);

    /**
     *  get the active doctor
     * @param $user_id
     * @return mixed
     */
    public function getActiveDoctor($user_id);

    /** get active doctor account
     * @param $user_id
     * @return mixed
     */
    public static function getActiveDoctorAccount($user_id);

    /**
     *
     *  get all doctors
     *
     * @param $request
     * @return mixed
     */
    public function getAllDoctors($request);

    /**
     * get doctor profile details
     * @param $doctor_id
     * @return mixed
     */
    public function getDoctorProfile($doctor_id);

    /**
     * add and remove doctors to user favourite doctors list
     * @param $user
     * @param $account_id
     * @return mixed
     */
    public function addDoctorToFavouriteList($user, $account_id);

    /**
     * get all specialities
     * @return mixed
     */
    public function getSpecialities();


    /**
     * get my doctors list
     * @param $request
     * @param $user
     * @return mixed
     */
    public function getMyDoctorsList($request, $user);

    /**
     *  this method called when user is authenticated
     *  then we mark the favourite doctors for this authenticated user
     *
     * @param $user
     * @param $doctors
     * @param  $is_single_doctor
     * @param $doctor
     * @return mixed
     */
    public function isSetToMyFavouriteDoctors($user, $doctors, $is_single_doctor, $doctor);

    /**
     * get all doctor clinics
     * @param $doctor_id
     * @return mixed
     */
    public function getDoctorClinics($doctor_id);

    /**
     * add image , is_recommended and is_favourite to doctor object
     * @param $is_auth
     * @param $doctor
     * @param $min_featured_stars
     * @param $is_not_empty
     * @param $subscribed_accounts
     * @return mixed
     */
    public function addIsRecommendedAndIsFavouriteToDoctor($is_auth, $doctor, $min_featured_stars, $is_not_empty, $subscribed_accounts);

    /**
     *  deactivate the current doctor and activate the given doctor
     *
     * @param $auth
     * @param $account_id
     * @return mixed
     */
    public function deactivateCurrentDoctorAndActivateTheGivenDoctor($auth, $account_id);

    /**
     * @param $request
     * @return mixed
     */
    public function SiteAllDoctors($request);

    /**
     * check if this doctor is published = 1 that mean doctor have clinics and due date > today
     * @param $account_id
     * @return mixed
     */
    public function CheckIfDoctorPublished($account_id);

    /**
     *increase views when patient view doctor account
     * @param $account_id
     * @return mixed
     */
    public function counterViewsForAccount($account_id);

    /**
     * recommend this doctor
     * @param $user_id
     * @param $account_id
     * @param $receiver_serial
     * @return mixed
     */
    public function recommendDoctorAccount($user_id, $account_id, $receiver_serial);

    /**
     * @param $user_id
     * @param $account_id
     * @param $serial
     * @return mixed
     */
    public function checkIfRecommendationExists($user_id, $account_id, $serial);

    /**
     *  get sort by for all Doctors
     *
     * @param $sortType
     * @param $sort_type
     * @return array
     */
    public function getAllDoctorsSortType($sortType, $sort_type);


    /**
     *  get the range of prices of Doctors
     *
     * @param $range_from_request
     * @param $price_range
     * @return mixed
     */
    public function getAllDoctorsPriceRange($range_from_request, $price_range);
}
