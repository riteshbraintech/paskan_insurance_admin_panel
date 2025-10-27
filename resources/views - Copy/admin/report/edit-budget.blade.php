<div class="card">
    <div class="card-body">
        <div class="p-4 border rounded">
            <div class="card-header">
                <strong>Edit Budget</strong> 
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    <form action="" method="POST" id="budgetform">
                        @csrf
                        <input type="hidden" id="budgetId" name="budgetId" value="{{$budget->id}}" />
                        <div class="col-md-12 mb-2">
                            <label for="budget" class="form-label"><strong>Date:</strong></label>
                            <input type="date" id="budget_date" name="budget_date" class="mx-3" value="{{$budget->added_at}}"/>
                            <div class="text-danger"></div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="budget" class="form-label"><strong>Budget:</strong></label>
                            <input type="text" id="budget" name="budget" class="w-50" value={{$budget->budget}}>
                            <div class="text-danger"></div>
                        </div>
                        <button type="submit" class="btn btn-primary submit">Update</button>
                        <div class="text-danger add-msg mt-2"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $("#budgetform").on('submit',function(event){
        event.preventDefault();
        $.ajax({
            url:"{{ route('admin.report.update-budget') }}",
            type:'post',
            data:$(this).serializeArray(),
            dataType:'json',
            success:function(response){
                if (response.status == true) {
                    $('.submit').siblings('.add-msg').html("Budget updated !!");
                    setTimeout(() => {
                        $.facebox.close();    
                    }, 2000);
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