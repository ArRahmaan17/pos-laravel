<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" data-bg-class="bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard.index') }}" class="app-brand-link">
            <span
                class="app-brand-text demo menu-text fw-bolder ms-2 text-capitalize text-wrap col-12">{{ !session('userLogged')['company']['name'] ? env('APP_NAME') : session('userLogged')['company']['name'] }}</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>
    <div class="menu-inner py-1 ps ps--active-y">
        {!! buildMenu($menus) !!}
    </div>
</aside>
