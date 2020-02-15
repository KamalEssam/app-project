<!-- Second navbar for categories -->
<nav class="navbar navbar">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a target="_blank" href="{{url('http://rkanjel.com/')}}" class="navbar-brand">
                <img src="{{ asset('logo1.png') }}" style="width: 55px;">
            </a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="{{url('/')}}">{{trans('lang.home')}}</a></li>
                <li>
                    <a href="{{route('login')}}"
                       aria-expanded="false">{{trans('lang.login')}}</a>
                </li>
                <li>
                    <a href="{{route('register')}}" class="btn btn-default "
                       aria-expanded="false">{{trans('lang.get_started')}}</a>
                </li>
            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container -->
</nav><!-- /.navbar -->
