@forelse ($projects as $project)
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row my-2">
                    <div class="remarlist col-md-12"><strong>App Name :</strong> {{ $project->app_name }}</div>
                </div>
                <div class="row my-2">
                    <div class="remarlist col-md-12"><strong>Technology :</strong> {{ implode(',',$project->technology) }}</div>
                </div>
                <div class="row my-2">
                    <div class="remarlist col-md-12"><strong>Play Store URL :</strong> <a href="{{ $project->play_store_url }}" target="_blank">{{ $project->play_store_url }}</a></div>
                </div>
                <div class="row my-2">
                    <div class="remarlist col-md-12"><strong>App Store URL :</strong> <a href="{{ $project->app_store_url }}" target="_blank">{{ $project->app_store_url }}</a></div>  
                </div>
                <div class="row my-2">
                    <div class="remarlist col-md-12"><strong>Website URL :</strong> <a href="{{ $project->website_url }}" target="_blank">{{ $project->website_url }}</a></div>
                </div>
                <div class="row my-2">
                    <div class="remarlist col-md-4"><strong>Application Status :</strong> {{ $project->application_status }}</div>
                </div>
                <div class="row my-2">
                    <div class="remarlist col-md-12"><strong>Description :</strong> {{ $project->description }}</div>
                </div>
                <div class="row mt-2">
                    <div class="d-flex flex-row gap-3">
                        <div class="">
                            <a class="" href="{{route('admin.project.edit',['id' => $project->id])}}"><i class="fs-5 fa-solid fa-pen-to-square text-warning"></i></a>
                        </div>
                        <div class="">
                            <a class="" href="{{route('admin.project.delete',['id' => $project->id])}}"><i class="fa-solid fa-trash text-danger fs-5"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="row">
        <div class="col-md-12 text-center">Projects Not Available</div>
    </div>
@endforelse

{{-- pagination with filter --}}
@include('admin.elements.filter-with-pagi', ['data' => $projects])
