<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\InfluencersRepository;
use App\Http\Requests\InfluencerRequest;
use DB;

class InfluencersController extends WebController
{
    private $influencersRepository;

    public function __construct(InfluencersRepository $influencersRepository)
    {
        $this->influencersRepository = $influencersRepository;
    }

    /**
     *  show list of all cities
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $influencers = $this->influencersRepository->getAllInfluencers();
        return view('admin.rk-admin.influencers.index', compact('influencers'));
    }


    /**
     * Store new city in database
     *
     * @param InfluencerRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(InfluencerRequest $request)
    {
        // add city
        DB::beginTransaction();
        try {
            $this->influencersRepository->createInfluencers($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.influencer_add_err'));
        }

        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.influencer_add_ok'), 'influencers.index');
    }

    /**
     *  show edit city form
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $influencer = $this->influencersRepository->getInfluencerById($id);
        if (!$influencer) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_influencers'));
        }
        return view('admin.rk-admin.influencers.edit', compact('influencer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param InfluencerRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(InfluencerRequest $request, $id)
    {
        $influencer = $this->influencersRepository->getInfluencerById($id);
        if (!$influencer) {
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.no_influencers'));
        }

        DB::beginTransaction();
        // update city data
        try {
            $this->influencersRepository->updateInfluencers($influencer, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            self::logErr($e->getMessage());
            return $this->messageAndRedirect(self::STATUS_ERR, trans('lang.influencer_update_err'));
        }
        return $this->messageAndRedirect(self::STATUS_OK, trans('lang.influencer_update_ok'), 'influencers.index');
    }

    /**
     * Remove city
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->deleteItem($this->influencersRepository->influencer, $id);
    }
}
