<li class="{{ Request::is('specialities*') ? 'active open'  : ''}}">
    <a href="#" class="dropdown-toggle">
        <i class="menu-icon fa fa-stethoscope"></i>
        <span class="menu-text"> {{ trans('lang.specialities') }}   </span>
        <b class="arrow fa fa-angle-down"></b>
    </a>
    <b class="arrow"></b>
    <ul class="submenu nav-hide">
        <li {{ Request::is('specialities') ? 'class=active' : '' }}>
            <a href="{{route('specialities.index')}}">
                <i class="menu-icon fa fa-stethoscope"></i>
                <span class="menu-text"> {{trans('lang.specialities')}} </span>
            </a>
            <b class="arrow"></b>
        </li>

        <li {{ Request::is('specialities/sponsored*') ? 'class=active' : '' }}>
            <a href="{{route('sponsored.index')}}">
                <i class="menu-icon fa fa-users"></i>
                <span class="menu-text"> {{trans('lang.sponsored-doctor')}} </span>
            </a>
            <b class="arrow"></b>
        </li>
    </ul>
</li>

<li class="{{ Request::is('location*') ? 'active open'  : ''}}">
    <a href="#" class="dropdown-toggle">
        <i class="menu-icon fas fa-building"></i>
        <span class="menu-text"> {{ trans('lang.locations') }}   </span>
        <b class="arrow fa fa-angle-down"></b>
    </a>
    <b class="arrow"></b>
    <ul class="submenu nav-hide">
        <li {{ Request::is('location/countries*') ? ' class=active' : '' }}>
            <a href="{{route('countries.index')}}">
                <i class="menu-icon fa fa-building"></i>
                <span class="menu-text"> {{trans('lang.countries')}} </span>
            </a>
            <b class="arrow"></b>
        </li>

        <li {{ Request::is('location/cities*') ? ' class=active' : '' }}>
            <a href="{{route('cities.index')}}">
                <i class="menu-icon fa fa-home"></i>
                <span class="menu-text"> {{trans('lang.cities')}} </span>
            </a>
            <b class="arrow"></b>
        </li>

        <li {{ Request::is('location/provinces*') ? 'class=active' : '' }}>
            <a href="{{route('provinces.index')}}">
                <i class="menu-icon fa fa-home"></i>
                <span class="menu-text"> {{trans('lang.provinces')}} </span>
            </a>
            <b class="arrow"></b>
        </li>
    </ul>
</li>

{{-- Doctor Plans   (remove) --}}
{{--<li {{ Request::is('plans*') ? 'class=active' : '' }}>--}}
{{--<a href="{{route('plans.index')}}">--}}
{{--<i class="menu-icon fa fa-credit-card"></i>--}}
{{--<span class="menu-text"> {{trans('lang.plans')}} </span>--}}
{{--</a>--}}
{{--<b class="arrow"></b>--}}
{{--</li>--}}

<li {{ Request::is('patient-plans*') ? 'class=active' : '' }}>
    <a href="{{route('patient-plans.index')}}">
        <i class="menu-icon fa fa-credit-card"></i>
        <span class="menu-text"> {{trans('lang.patient_plans')}} </span>
    </a>
    <b class="arrow"></b>
</li>


<li class="{{ Request::is('account*') ? 'active open'  : ''}}">
    <a href="#" class="dropdown-toggle">
        <i class="menu-icon fa fa-users"></i>
        <span class="menu-text"> {{ trans('lang.accounts') }}   </span>
        <b class="arrow fa fa-angle-down"></b>
    </a>
    <b class="arrow"></b>
    <ul class="submenu nav-hide">
        <li {{ Request::is('accounts*') ? 'class=active' : '' }}>
            <a href="{{route('accounts.index') . '?type=0'}}">
                <i class="menu-icon fa fa-users"></i>
                <span class="menu-text"> {{trans('lang.accounts')}} </span>
            </a>
            <b class="arrow"></b>
        </li>

        <li {{ Request::is('account/premium-requests*') ? 'class=active' : '' }}>
            <a href="{{route('premium-requests.index') . '?status=new'}}">
                <i class="menu-icon fa fa-users"></i>
                <span class="menu-text"> {{trans('lang.premium_requests')}} </span>
            </a>
            <b class="arrow"></b>
        </li>
    </ul>
</li>

<li class="{{ Request::is('sales*') ? 'active open' : ''}}">
    <a href="#" class="dropdown-toggle">
        <i class="menu-icon fas fa-suitcase"></i>
        <span class="menu-text"> {{ trans('lang.sales') }}   </span>
        <b class="arrow fa fa-angle-down"></b>
    </a>

    <b class="arrow"></b>
    <ul class="submenu nav-hide">
        <li class="{{ Request::is('sales/agents*')  ? 'active' : '' }}">
            <a href="{{ route('sales.index') }}">
                <span class="menu-text"> {{ trans('lang.sales_agents') }} </span>
            </a>
            <b class="arrow"></b>
        </li>
        <li class="{{ Request::is('sales/logs*')  ? 'active' : '' }}">
            <a href="{{ route('sales.logs') }}">
                <span class="menu-text"> {{ trans('lang.sales_logs') }} </span>
            </a>
            <b class="arrow"></b>
        </li>
    </ul>
</li>

<li {{ Request::is('patients/list*') ? 'class=active' : '' }}>
    <a href="{{ route('patients.all') }}">
        <i class="menu-icon fa fa-users"></i>
        <span class="menu-text"> {{ trans('lang.patients') }} </span>
    </a>
    <b class="arrow"></b>
</li>


<li class="{{ Request::is('marketing*') ? 'active open' : ''}}">
    <a href="#" class="dropdown-toggle">
        <i class="menu-icon far fa-money-bill-alt"></i>
        <span class="menu-text"> {{ trans('lang.marketing') }}   </span>
        <b class="arrow fa fa-angle-down"></b>
    </a>

    <b class="arrow"></b>
    <ul class="submenu nav-hide">
        <li {{ Request::is('marketing/insurance_company*') ? 'class=active' : '' }}>
            <a href="{{ route('insurance_company.index') }}">
                <i class="menu-icon fa fa-building"></i>
                <span class="menu-text"> {{ trans('lang.insurance_companies') }} </span>
            </a>
            <b class="arrow"></b>
        </li>
        <li {{ Request::is('marketing/influencers*') ? 'class=active' : '' }}>
            <a href="{{ route('influencers.index') }}">
                <i class="menu-icon fa fa-users"></i>
                <span class="menu-text"> {{ trans('lang.influencers') }} </span>
            </a>
            <b class="arrow"></b>
        </li>
        <li {{ Request::is('marketing/promo-code*') ? 'class=active' : '' }}>
            <a href="{{ route('promo-code.index') }}">
                <i class="menu-icon fa fa-money-bill-alt"></i>
                <span class="menu-text"> {{ trans('lang.promo') }} </span>
            </a>
            <b class="arrow"></b>
        </li>
        <li {{ Request::is('marketing/subscribers*') ? 'class=active' : '' }}>
            <a href="{{ route('subscriptions.index') }}">
                <i class="menu-icon fas fa-envelope"></i>
                <span class="menu-text"> {{ trans('lang.subscribers') }} </span>
            </a>
            <b class="arrow"></b>
        </li>

        <li {{ Request::is('marketing/subscriptions*') ? 'class=active' : '' }}>
            <a href="{{ route('subscriptions.create') }}">
                <i class="menu-icon fas fa-envelope"></i>
                <span class="menu-text"> {{ trans('lang.newsletters') }} </span>
            </a>
            <b class="arrow"></b>
        </li>
    </ul>
</li>

<li class="{{ Request::is('offer*') ? 'active open' : ''}}">
    <a href="#" class="dropdown-toggle">
        <i class="menu-icon fa fa-gift"></i>
        <span class="menu-text"> {{ trans('lang.offers') }}   </span>
        <b class="arrow fa fa-angle-down"></b>
    </a>
    <b class="arrow"></b>
    <ul class="submenu nav-hide">
        <li {{ Request::is('offer_categories*') ? 'class=active' : '' }}>
            <a href="{{ route('offer_categories.index') }}">
                <i class="menu-icon fa fa-sitemap"></i>
                <span class="menu-text"> {{ trans('lang.offer_categories') }} </span>
            </a>
            <b class="arrow"></b>
        </li>

        <li {{ Request::is('offers*') ? 'class=active' : '' }}>
            <a href="{{ route('offers.index') }}">
                <i class="menu-icon fa fa-gift"></i>
                <span class="menu-text"> {{ trans('lang.offers') }} </span>
            </a>
            <b class="arrow"></b>
        </li>

        <li {{ Request::is('offer/ads*') ? 'class=active' : '' }}>
            <a href="{{ route('ads.index') }}">
                <i class="menu-icon fa fa-shopping-basket "></i>
                <span class="menu-text"> {{ trans('lang.ads') }} </span>
            </a>
            <b class="arrow"></b>
        </li>

    </ul>
</li>

<li {{ Request::is('services*') ? 'class=active' : '' }}>
    <a href="{{route('services.index')}}">
        <i class="menu-icon fas fa-medkit"></i>
        <span class="menu-text"> {{trans('lang.services')}}  </span>
    </a>
    <b class="arrow"></b>
</li>

<li {{ Request::is('reservations-statis*') ? 'class=active' : '' }}>
    <a href="{{route('account.reservations_all')}}">
        <i class="menu-icon fa fa-calendar"></i>
        <span class="menu-text"> {{trans('lang.reservations')}}  </span>
    </a>
    <b class="arrow"></b>
</li>

<li class="{{ Request::is('market-place*') ? 'active open' : ''}}">
    <a href="#" class="dropdown-toggle">
        <i class="menu-icon fas fa-money-bill-alt"></i>
        <span class="menu-text"> {{ trans('lang.market-place') }}   </span>
        <b class="arrow fa fa-angle-down"></b>
    </a>
    <b class="arrow"></b>
    <ul class="submenu nav-hide">
        <li class="{{ Request::is('market-place/brands*')  ? 'active' : '' }}">
            <a href="{{ route('brands.index') }}">
                <span class="menu-text"> {{ trans('lang.brands') }} </span>
            </a>
            <b class="arrow"></b>
        </li>

        <li class="{{ Request::is('market-place/product*')  ? 'active' : '' }}">
            <a href="{{ route('product.index') }}">
                <span class="menu-text"> {{ trans('lang.products') }} </span>
            </a>
            <b class="arrow"></b>
        </li>

        <li class="{{ Request::is('market-place/category*')  ? 'active' : '' }}">
            <a href="{{ route('category.index') }}">
                <span class="menu-text"> {{ trans('lang.categories') }} </span>
            </a>
            <b class="arrow"></b>
        </li>
    </ul>
</li>

<li class="{{ Request::is('policies/*') ? 'active open' : ''}}">
    <a href="#" class="dropdown-toggle">
        <i class="menu-icon fas fa-file"></i>
        <span class="menu-text"> {{ trans('lang.policies-and-terms') }}   </span>
        <b class="arrow fa fa-angle-down"></b>
    </a>
    <b class="arrow"></b>
    <ul class="submenu nav-hide">
        <li class="{{ Request::is('policies/1*')  ? 'active' : '' }}">
            <a href="{{ route('policies.edit',['id' => 1]) }}">
                <span class="menu-text"> {{ trans('lang.policies') }} </span>
            </a>
            <b class="arrow"></b>
        </li>
        <li class="{{ Request::is('policies/2*')  ? 'active' : '' }}">
            <a href="{{ route('policies.edit',['id' => 2]) }}">
                <span class="menu-text"> {{ trans('lang.terms') }} </span>
            </a>
        </li>

        <li class="{{ Request::is('policies/3*')  ? 'active' : '' }}">
            <a href="{{ route('policies.edit',['id' => 3]) }}">
                <span class="menu-text"> {{ trans('lang.payment-terms') }} </span>
            </a>
        </li>

        <li class="{{ Request::is('policies/4*')  ? 'active' : '' }}">
            <a href="{{ route('policies.edit',['id' => 4]) }}">
                <span class="menu-text"> {{ trans('lang.refund-terms') }} </span>
            </a>
        </li>
    </ul>
</li>

<li {{ Request::is('reports/a*') ? 'class=active' : '' }}>
    <a href="{{route('reports')}}">
        <i class="menu-icon fas fa-folder"></i>
        <span class="menu-text"> {{trans('lang.reports')}}  </span>
    </a>
    <b class="arrow"></b>
</li>
