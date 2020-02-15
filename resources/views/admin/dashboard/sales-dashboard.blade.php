<div class="page-content">
    <div class="page-header">
        <h1 class="text-center">{{ trans('lang.dashboard') }}</h1>
    </div>
    <div class="col-md-4">
        <div class="dash-box dash-box-color">
            <div class="dash-box-icon">
                <i class="icon fa fa-users"></i>
            </div>
            <div class="dash-box-body">
                        <span class="dash-box-count">
                            {{ (new \App\Http\Repositories\Web\SalesRepository())->getSalesAccountCount() }}
                        </span>
                <span class="dash-box-title">{{ trans('lang.total_accounts') }}</span>
            </div>

            <div class="dash-box-action">
                <button><a href="{{ route('sale.accounts') }}">{{ trans('lang.more_info') }}</a></button>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="dash-box dash-box-color">
            <div class="dash-box-icon">
                <i class="icon fa fa-users"></i>
            </div>
            <div class="dash-box-body">
                        <span class="dash-box-count">
                            {{ (new \App\Http\Repositories\Web\saleLeadsRepository())->getCountOfSalesLeads() }}
                        </span>
                <span class="dash-box-title">{{ trans('lang.total_leads') }}</span>
            </div>

            <div class="dash-box-action">
                <button><a href="{{ route('leads.index') }}">{{ trans('lang.more_info') }}</a></button>
            </div>
        </div>
    </div>
</div>
