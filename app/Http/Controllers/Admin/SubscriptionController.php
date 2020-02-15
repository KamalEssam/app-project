<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\WebController;
use App\Http\Repositories\Web\SubscriptionRepository;
use App\Http\Requests\SubscriptionRequest;

class SubscriptionController extends WebController
{
    private $subscriptionRepository;

    /**
     * DoctorController constructor.
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * get all subscribers
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $subscribers = $this->subscriptionRepository->getAllSubscribers();
        return view('admin.rk-admin.subscriptions.index', compact('subscribers'));
    }

    /**
     * send mail to all subscribers
     * @param SubscriptionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SubscriptionRequest $request)
    {
        // get all subscribers
        $subscribers = $this->subscriptionRepository->getAllSubscribers();

        // if no subscribers
        if ($this->subscriptionRepository->getSubscribersCount() <= 0) {
            return $this->messageAndRedirect(self::STATUS_ERR,'No subscriptions');
        }

        // loop on all subscribers send mails
        foreach ($subscribers as $subscriber) {
            $send_mail = $this->sendMail($subscriber->email, 'newsletters', 'emails.subscription', $request->news);
            if($send_mail == false){
                return $this->messageAndRedirect(self::STATUS_ERR, 'Failed send mail');
            }
            return $this->messageAndRedirect(self::STATUS_OK, 'Mail sent successfully', 'subscriptions.create');
        }


    }
}