@php
    $segment = Request::segment(2);
    $child_segment = Request::segment(3);
    $role_id = admin()->user()->role_id;
@endphp

<aside class="sidebar-wrapper" data-simplebar="true" data-show="on">
    <div class="sidebar-header">
        <div>
            <img src="{{ loadAssets('images/bt-logo2.png') }}" class="logo-icon" alt="logo icon" style="width: 8rem; ">
        </div>
        {{-- <div class="toggle-icon ms-auto"> <i class="fa-solid fa-bars" onclick="sidenav()"></i></div> --}}
    </div>

    <!--navigation-->
    <ul class="metismenu" id="menu">

        {{-- <li class="menu-heading-title" style="margin-top: 8px !important;"><span>Dashboard</span></li> --}}

        <li class="{{ $segment == 'dashboard' ? 'mm-active' : '' }}">
            <a href="{{ route('admin.dashboard') }}">
                <div class="parent-icon"><i class="lni lni-home"></i></div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>

        {{-- <li class="{{ $segment == 'role' ? 'mm-active' : '' }}">
            <a href="javascript:void(0);" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-user"></i>
                </div>
                <div class="menu-title">Role Access</div>
            </a>
            <ul class="{{ $segment == 'role' ? 'mm-show mm-collapse' : '' }}">
                <li class="{{ $segment . '/' . $child_segment == 'role/create' ? 'mm-active' : '' }}"> <a
                        href="{{ route('admin.role.create') }}"><i class="bi bi-circle"></i>Creat Role</a>
                </li>
                <li class="{{ $segment . '/' . $child_segment == 'role/list' ? 'mm-active' : '' }}"> <a
                        href="{{ route('admin.role.list') }}"><i class="bi bi-circle"></i>All Role</a>
                </li>
            </ul>
        </li> --}}

        {{-- @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN]))
            
            <li class="{{ $segment == 'client' ? 'mm-active' : '' }}">
                <a href="javascript:;" class="has-arrow dropdown">
                    <div class="parent-icon"><i class="fa-solid fa-user-tie"></i></i></div>
                    <div class="menu-title">Client Account</div>
                </a>
                <ul class="{{ $segment == 'client' ? 'mm-show mm-collapse' : '' }}">
                    <li class="{{ $segment . '/' . $child_segment == 'client/create' ? 'mm-active' : '' }}"> 
                        <a href="{{ route('admin.client.create') }}"><i class="fa-regular fa-circle"></i>Create Client Account</a>
                    </li>
                    <li class="{{ $segment . '/' . $child_segment == 'client/list' ? 'mm-active' : '' }}{{ $segment . '/' . $child_segment == 'client/edit' ? 'mm-active' : '' }}"> 
                        <a href="{{ route('admin.client.list') }}"><i class="fa-regular fa-circle"></i>All Clients Account</a>
                    </li>
                </ul>
            </li>

        @endif
         --}}
        {{-- <li class="{{ $segment == 'bid' ? 'mm-active' : '' }}">
            <a href="javascript:;" class="has-arrow dropdown">
                <div class="parent-icon"><i class="fa-solid fa-chart-simple"></i></i></div>
                <div class="menu-title">Bid</div>
            </a>
            <ul class="{{ $segment == 'bid' ? 'mm-show mm-collapse' : '' }}">
                <li class="{{ $segment . '/' . $child_segment == 'bid/create' ? 'mm-active' : '' }}"> 
                    <a href="{{ route('admin.bid.create') }}"><i class="fa-regular fa-circle"></i>Create Bid</a>
                </li>

                <li class="{{ $segment . '/' . $child_segment == 'bid/list' ? 'mm-active' : '' }}{{ $segment . '/' . $child_segment == 'bid/edit' ? 'mm-active' : '' }}"> 
                    <a href="{{ route('admin.bid.list') }}"><i class="fa-regular fa-circle"></i>View All Bids</a>
                </li>
            </ul>
        </li> --}}

        {{-- <li class="{{ $segment == 'lead' ? 'mm-active' : '' }}">
            <a href="javascript:;" class="has-arrow dropdown">
                <div class="parent-icon"><i class="fa-solid fa-laptop-file"></i></i></div>
                <div class="menu-title">Lead</div>
            </a>
            <ul class="{{ $segment == 'lead' ? 'mm-show mm-collapse' : '' }}">
                <li class="{{ $segment . '/' . $child_segment == 'lead/create' ? 'mm-active' : '' }}"> 
                    <a href="{{ route('admin.lead.create') }}"><i class="fa-regular fa-circle"></i>Create Lead</a>
                </li>

                <li class="{{ $segment . '/' . $child_segment == 'lead/list' ? 'mm-active' : '' }}{{ $segment . '/' . $child_segment == 'lead/edit' ? 'mm-active' : '' }}"> 
                    <a href="{{ route('admin.lead.list') }}"><i class="fa-regular fa-circle"></i>View All Leads</a>
                </li>
            </ul>
        </li>

        @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN]))
            <li class="{{ $segment == 'log' ? 'mm-active' : '' }}">
                <a href="{{ route('admin.log.list') }}" >
                    <i class="fa-solid fa-file-lines"></i>
                    <div class="menu-title">View Logs</div>
                </a>
            </li>
        @endif --}}

        {{-- @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN, \App\Models\Role::MANAGER]))
            <li class="{{ $segment == 'report' ? 'mm-active' : '' }}">
                <a href="{{ route('admin.report.list') }}" >
                    <i class="fa-solid fa-file"></i>
                    <div class="menu-title">Report</div>
                </a>
            </li>
        @endif --}}


        <li class="menu-heading-title"><span>Admin Workplace</span></li>

        @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN, \App\Models\Role::MANAGER]))
            <li class="{{ $segment == 'cmspage' ? 'mm-active' : '' }}">
                <a href="{{ route('admin.cmspage.index') }}">
                    <i class="fa-solid fa-file"></i>
                    <div class="menu-title">CMS Pages</div>
                </a>
            </li>
        @endif

        @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN, \App\Models\Role::MANAGER]))
            <li class="categories {{ in_array($segment, ['categories', 'categoryformfield']) ? 'mm-active' : '' }}">
                <a href="javascript:void(0);"
                    class="has-arrow {{ in_array($segment, ['categories', 'categoryformfield']) ? 'mm-active' : '' }}">
                    <i class="fa-solid fa-folder-tree"></i>
                    <div class="menu-title">Manage Categories</div>
                </a>
                <ul
                    class="mm-collapse new-submenu {{ in_array($segment, ['categories', 'categoryformfield']) ? 'mm-show' : '' }}">
                    <li class="{{ $segment == 'categories' ? 'mm-active' : '' }}">
                        <a href="{{ route('admin.categories.index') }}">
                            <i class="fa-solid fa-pen-to-square"></i>
                            Edit Category
                        </a>
                    </li>
                    <li class="{{ $segment == 'categoryformfield' ? 'mm-active' : '' }}">
                        <a href="{{ route('admin.categoryformfield.index') }}">
                            <i class="fa-solid fa-list-check"></i>
                            Manage Form Fields
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN, \App\Models\Role::MANAGER]))
            <li class="{{ $segment == 'user' ? 'mm-active' : '' }}">
                <a href="{{ route('admin.user.index') }}">
                    <i class="fa-solid fa-users"></i>
                    <div class="menu-title">User Management</div>
                </a>
            </li>
        @endif

        @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN, \App\Models\Role::MANAGER]))
            <li class="{{ $segment == 'banner' ? 'mm-active' : '' }}">
                <a href="{{ route('admin.banner.index') }}">
                    <i class="fa-solid fa-users"></i>
                    <div class="menu-title">Banner Management</div>
                </a>
            </li>
        @endif

        @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN, \App\Models\Role::MANAGER]))
            <li class="{{ $segment == 'faq' ? 'mm-active' : '' }}">
                <a href="{{ route('admin.faq.index') }}">
                    <i class="fa-solid fa-users"></i>
                    <div class="menu-title">FAQ Management</div>
                </a>
            </li>
        @endif

        @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN, \App\Models\Role::MANAGER]))
            <li class="{{ $segment == 'article' ? 'mm-active' : '' }}">
                <a href="{{ route('admin.article.index') }}">
                    <i class="fa-solid fa-users"></i>
                    <div class="menu-title">Article Management</div>
                </a>
            </li>
        @endif

        {{-- <li class="{{ $segment == 'project' ? 'mm-active' : '' }}">
            <a href="javascript:;" class="has-arrow dropdown">
                <div class="parent-icon"><i class="fa-solid fa-p"></i></i></div>
                <div class="menu-title">Project</div>
            </a>
            <ul class="{{ $segment == 'project' ? 'mm-show mm-collapse' : '' }}">
                <li class="{{ $segment . '/' . $child_segment == 'project/create' ? 'mm-active' : '' }}"> 
                    <a href="{{ route('admin.project.create') }}"><i class="fa-regular fa-circle"></i>Create Project</a>
                </li>

                <li class="{{ $segment . '/' . $child_segment == 'project/list' ? 'mm-active' : '' }}{{ $segment . '/' . $child_segment == 'lead/edit' ? 'mm-active' : '' }}"> 
                    <a href="{{ route('admin.project.index') }}"><i class="fa-regular fa-circle"></i>View All Project</a>
                </li>
            </ul>
        </li> --}}
    </ul>
    <!--end navigation-->
</aside>

<script>
    // $(document).ready(function(){
    //     $(".toggle-icon").click(function(){
    //         $(".page-content").addClass("full-width");
    //     });
    // });
    function sidenav() {
        let status = $(".sidebar-wrapper").attr('data-show');
        if (status == "on") {
            $(".sidebar-wrapper").attr('data-show', 'off');
            $(".sidebar-wrapper").css('width', '3.9rem');
            $(".dropdown").removeClass('has-arrow');
            $(".page-content").css('margin-left', '3.9rem');
            $(".sidebar-header").css('width', '3.9rem');
            $(".sidebar-header img").css('display', 'none');
            $(".sidebar-header .logo-text").css('display', 'none');
            $(".navbar ").css('margin-left', '-12.9rem');
        } else if (status == "off") {
            $(".sidebar-wrapper").attr('data-show', 'on');
            $(".sidebar-wrapper").css('width', '260px');
            $(".page-content").css('margin-left', '260px');
            $(".dropdown").addClass('has-arrow');
            $(".sidebar-header").css('width', '260px');
            $(".navbar ").css('margin-left', '0');
            $(".sidebar-header img").css('display', 'block');
            $(".sidebar-header .logo-text").css('display', 'block');
        }
    }
</script>
