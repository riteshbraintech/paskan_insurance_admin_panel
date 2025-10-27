<style>
    #facebox {
        top: 78.3px;
        left: 25%;
        width: 38%;
    }

    #facebox .content {
        width: 100%;
    }
</style>
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="p-4 border rounded">
                    <form id="portalForm" method="post" class="row g-3 needs-validation" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" placeholder="Enter name" >
                                <div class="text-danger"></div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="slug" class="form-label">Slug<span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control" id="slug" value="{{ old('slug') }}" placeholder="Enter slug" readonly>
                                <div class="text-danger"></div>
                            </div>
                        </div>
                    
                        <div class="col-12">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-success px-5 radius-30">Create</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    

<script>
    $("#name").on('keyup',function(event){
        event.preventDefault();
        element = $(this);
        $('button[type=submit]').prop("disabled",true);
        $.ajax({
            url : '{{route("admin.portal.name-slug")}}',
            type: 'get',
            data: {name:element.val()},
            dataType: 'json',
            success: function(response){
                if(response['status'] == true){
                    $('#slug').val(response['slug']);
                }
                $('button[type=submit]').prop("disabled",false);
            }
        });
    });

    $("#portalForm").on('submit',function(event){
        event.preventDefault();
        $.ajax({
            url: '{{ route('admin.portal.store') }}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                
                if (response.status == true) {
                    let options = `<option disabled value=''>Choose...</option>`;
                    $('.portal').empty();
                    response.data.forEach(function(value,key,array){
                        options = options + `<option ${value.id == response.id ? "selected" : ""} value="${value.slug}">${value.name}</option>`;
                    });
                    options = options + `<option value="other">Other</option>`;
                    $.facebox.close();
                    $('.portal').html(options);
                }
                if (response.status == false) {
                    var errors = response['error'];
                    if(errors['name']){
                        $('#name').addClass('is-invalid').siblings('div').addClass('invalid-feedback').html(errors['name']);
                    }else{
                        $('#name').removeClass('is-invalid').siblings('div').removeClass('invalid-feedback').html("");
                    }
                    if(errors['slug']){
                        $("#slug").addClass('is-invalid').siblings('div').addClass('invalid-feedback').html(errors['slug']);
                    }
                    else{
                        $('#slug').removeClass('is-invalid').siblings('div').removeClass('invalid-feedback').html("");
                    }
                }
            }
        });
    });
</script>