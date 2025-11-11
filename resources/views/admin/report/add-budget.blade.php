<style>
    .close img{
        height: 12px;
        width: 12px;
    }
    #facebox .close {
        position: absolute;
        top: 12px;
        right: 18px;
        padding: 2px;
        background: #fff;
    }
</style>
{{-- <div class="card"> --}}
    {{-- <div class="card-body"> --}}
        <div class="p-4 border rounded">
            <div class="card-header">
                <strong>Add Budget</strong> 
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    <form action="" method="POST" id="budgetform">
                        @csrf
                        <input type="hidden" id="leadId" name="leadId" value="{{$lead_id}}" />
                        <div class="col-md-12 mb-2">
                            <label for="budget" class="form-label"><strong>Date:</strong></label>
                            <input type="date" id="budget_date" name="budget_date" class="mx-3"/>
                            <div class="text-danger"></div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="budget" class="form-label"><strong>Budget:</strong></label>
                            <input type="text" id="budget" name="budget" class="w-50">
                            <div class="text-danger"></div>
                        </div>
                        <button type="submit" class="btn btn-primary submit">Submit</button>
                        <div class="text-danger add-msg mt-2"></div>
                    </form>
                </div>
            </div>
        </div>
    {{-- </div> --}}
{{-- </div> --}}
<script>
    $("#budgetform").on('submit',function(event){
        event.preventDefault();
        $.ajax({
            url:"{{ route('admin.report.store-budget') }}",
            type:'post',
            data:$(this).serializeArray(),
            dataType:'json',
            success:function(response){
                if (response.status == true) {
                    $('.submit').siblings('.add-msg').html("Budget Added !!");
                    setTimeout(() => {
                        $.facebox.close();  
                        ajaxTableData();  
                    }, 1000);
                }
                if (response.status == false) {
                    var errors = response['error'];
                    if(errors['budget_date']){
                        $('#budget_date').siblings('div').html(errors['budget_date']);
                    }else{
                        $('#budget_date').siblings('div').html("");
                    }
                    if(errors['budget']){
                        $("#budget").siblings('div').html(errors['budget']);
                    }
                    else{
                        $('#budget').siblings('div').html("");
                    }
                }
            }
        });
    });
</script>

