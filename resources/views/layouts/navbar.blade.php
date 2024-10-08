<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <li class="nav-item d-flex justify-content-center align-items-center">
            <a class="nav-link h5 dropdown" href="#" data-target="#lang" data-toggle="dropdown" role="button">
                <div class="d-flex gap-2 align-items-center">
                    <span class="lang-icon lang-icon-{{ app()->getLocale() }}"></span>
                    <span class="ml-2 text-uppercase">{{ app()->getLocale() }}</span>
                </div>
                <div class="dropdown-menu" id="lang">
                    <ul id="lang-dropdown" class="d-flex flex-column gap-2"
                        style="max-height: 12rem;overflow-y: scroll;"></ul>
                </div>
            </a>
        </li>
        <li class="nav-item d-flex justify-content-center align-items-center">
            <a class="nav-link h5" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        @if (Auth::user()->role->id <= 2)
            <li class="nav-item dropdown d-flex justify-content-center align-items-center">
                <a class="nav-link h5 position-relative" href="#" id="notificationDropdown" data-toggle="dropdown"
                    role="button">
                    <i class="fas fa-solid fa-inbox"></i>
                    @if (App\Models\GoodsIn::where('status', 0)->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ App\Models\GoodsIn::where('status', 0)->count() }}
                        </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right">
                    <span class="dropdown-header">{{ App\Models\GoodsIn::where('status', 0)->count() }} {{ __('Approval Notifications') }} </span>
                    <div class="dropdown-divider"></div>
                    @foreach (App\Models\GoodsIn::where('status', 0)->get() as $approval)
                        <a href="#" class="dropdown-item d-flex justify-content-between mb-2">
                            {{ Str::limit($approval->invoice_number, 15, '...') }}
                            {{ Str::limit($approval->item->name, 5, '...') }}
                            <span
                                class="float-right text-muted text-sm">{{ $approval->created_at->diffForHumans() }}</span>
                        </a>
                        <div class="dropdown-divider"></div>
                    @endforeach
                    <a href="{{ route('transaksi.masuk') }}" class="dropdown-item dropdown-footer">{{ __('See All Approvals') }}</a>
                </div>
            </li>
        @endif


        <li class="nav-item dropdown" data-toggle="dropdown">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex justify-content-center align-items-center">
                <div class="info font-weight-bold" style="text-transform:capitalize;">
                    <a href="javascript:void(0)" class="d-block" style="color:gray !important;"
                        id="user">{{ Auth::user()->name }}</a>
                </div>
                <div class="image">
                    <img src="{{ empty(Auth::user()->image) ? asset('user.png') : asset('storage/profile/' . Auth::user()->image) }}"
                        class="img-circle elevation-2"
                        style="width:100% !important;max-width:35px !important;aspect-ratio:1 !important;object-fit:cover !important;"
                        id="img_profile" alt="User Image">
                </div>
            </div>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-divider"></div>
                <a onclick="window.location.href=`{{ route('settings.profile') }}`" class="dropdown-item w-100">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <div class="dropdown-divider"></div>
                <a onclick="window.location.href=`{{ route('login.delete') }}`" class="dropdown-item w-100">
                    <i class="fas fa-sign-out-alt mr-2"></i> LogOut
                </a>
            </div>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
