<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset_url() }}" data-template="vertical-menu-template"
    lang="{{ str_replace('_', '-', app()->getLocale()) }}">

</html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title')</title>

    <meta name="description" content="" />
        <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset_url('img/favicon.svg') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset_url('fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset_url('fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset_url('fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset_url('css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset_url('css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset_url('css/demo.css') }}?{{ time() }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset_url('libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset_url('libs/smoothness/jquery-ui.css') }}" />
    <link rel="stylesheet" href="{{ asset_url('libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset_url('libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset_url('libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset_url('libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset_url('libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.2/css/dataTables.dataTables.css" />
    


    <!-- Page CSS -->
    @auth
        <link rel="stylesheet" href="{{ asset_url('css/pages/cards-advance.css') }}" />
    @endauth

    @stack('css')

    <script src="{{ asset_url('js/jquery.js') }}"></script>

    <!-- Helpers -->
    <script src="{{ asset_url('js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset_url('js/template-customizer.js') }}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset_url('js/config.js') }}"></script>
    <script src="{{ asset_url('js/form-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset_url('js/form-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset_url('js/autocomplete-jquery-ui.js') }}"></script>

    <script src="https://cdn.datatables.net/2.1.2/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.7.3/jquery.bootstrap-touchspin.min.js" integrity="sha512-uztszeSSfG543xhjG/I7PPljUKKbcRnVcP+dz9hghb9fI/AonpYMErdJQtLDrqd9M+STTHnTh49h1Yzyp//d6g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <div id="app">
    <!-- <nav class="navbar navbar-expand-md navbar-light bg-white shadow-s p-3">

<div class="container-fluid px-0">

  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse " id="navbarNav">
    <ul class="navbar-nav ms-auto align-items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-menu-2">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M4 6l16 0" />
        <path d="M4 12l16 0" />
        <path d="M4 18l16 0" />
      </svg>
      <span class="text-black">MENU</span>
      </li>
      <li class="nav-item">
        <div class="avatar avatar-sm"><img src="{{ asset('img/Mask group (20).png') }}" alt="Avatar" class="rounded-circle pull-up" width="30px" height="30px"></div>
      </li>
    </ul>
  </div>
</div>
</nav> -->

        <main class="">
            @yield('content')
        </main>
    </div>

    <script type="module" src="{{ asset_url('libs/popper/popper.js') }}"></script>
    <script src="{{ asset_url('js/bootstrap.js') }}"></script>
    <!-- <script type="module" src="{{ asset_url('libs/datatables-bs5/datatables-bootstrap5.js') }}"></script> -->
    <script type="module" src="{{ asset_url('libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset_url('libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset_url('libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset_url('libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset_url('libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset_url('js/menu.js') }}"></script>
    
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script type="module" src="{{ asset_url('libs/swiper/swiper.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset_url('js/main.js') }}"></script>
    
    @stack('scripts')
</body>

</html>