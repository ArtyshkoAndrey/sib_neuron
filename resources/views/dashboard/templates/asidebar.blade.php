<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('index') }}" class="brand-link">
      <img src="{{asset('images/logo.png')}}" alt="Logo sib_neuron" class="brand-image"
           style="opacity: .8">
      <span class="brand-text font-weight-light">Sib Neuron</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{Auth::user()->avatar}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{Auth::user()->name}}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="{{route('user')}}" class="nav-link {{ active('user') }}">
              <i class="nav-icon fa fa-th"></i>
              <p>
                Главная
              </p>
            </a>
          </li>

          <li class="nav-header">Фотографии</li>
          <li class="nav-item">
            <a href="{{route('user_photos_vk')}}" class="nav-link {{ active('user_photos_vk') }}">
              <i class="nav-icon fab fa-vk"></i>
              <p>
                ВК Фотографии
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
