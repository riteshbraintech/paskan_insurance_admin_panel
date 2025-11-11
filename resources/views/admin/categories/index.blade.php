@extends('admin.layouts.app')
@section('content')

    <style>
        .rolesList>button {
            margin-left: 10px;
        }
    </style>

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">All Categories</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.categories.index') }}">Category Page</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Categories</li>
                </ol>
            </nav>
        </div>

        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('admin.categories.create') }}">
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ Add Category</a>
                </a>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="card-header">
                <div class="row g-2">
                    @include('admin.elements.perPage', ['datas' => $records])
                    @include('admin.elements.search')
                </div>
            </div>

            <div class="card-body">
                <div class="load-table-data">
                    @include('admin.categories.table')
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>

            function changeStatus(event,id){
                let url = "{{route('admin.categories.change.status')}}"+"/"+id;
                $.ajax({
                    url : url,
                    type: "post",
                    data: {"_token": "{{ csrf_token() }}"},
                    dataType: "json",
                    success: function(response){
                        if(response.status == "active"){
                            $(`.status-${id}`).html("Active");
                            $(`.status-${id}`).addClass("badge-success");
                            $(`.status-${id}`).removeClass("badge-danger");
                        }else{
                            $(`.status-${id}`).html("Inactive");
                            $(`.status-${id}`).addClass("badge-danger");
                            $(`.status-${id}`).removeClass("badge-success");
                        }
                    }
                }); 
            }
            
        // $(document).on("click",".selectItems",function(){
        //     if($('.selectItems:checked').length > 1){
        //         $('.mergeIcon').show();
        //     }else{
        //         $('.mergeIcon').hide();
        //     }
        // });
    </script>
@endpush
