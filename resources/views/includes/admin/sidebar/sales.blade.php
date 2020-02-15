<li {{ Request::is('sale/accounts*') ? 'class=active' : '' }}>
    <a href="{{ route('sale.accounts') }}">
        <i class="menu-icon fa fa-users"></i>
        <span class="menu-text"> {{ trans('lang.accounts') }} </span>
    </a>
    <b class="arrow"></b>
</li>

<li {{ Request::is('sale/leads*') ? 'class=active' : '' }}>
    <a href="{{ route('leads.index') }}">
        <i class="menu-icon fa fa-users"></i>
        <span class="menu-text"> {{ trans('lang.leads') }} </span>
    </a>
    <b class="arrow"></b>
</li>
