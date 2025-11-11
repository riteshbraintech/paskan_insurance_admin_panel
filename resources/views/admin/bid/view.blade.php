<style scoped>
    #facebox {
        top: 78.3px;
        left: 25%;
        width: 55%;
        /* height: 250%; */
    }
    
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

    #facebox .popup {
        /* width: 100%; */
        height: 100%;
    }

    #facebox .content {
        width: 100%;
        height: 100%;
    }

    .box-list{
        /* border: 1px solid #ededed; */
        padding: 7px;
        margin-bottom: 15px;
    }
    .remarlist {
        border-bottom: 1px solid #ebebeb;
        margin-bottom: 5px;
    }

    .remarlist>p {
        margin-bottom: 0px;
    }

    .remarlist>p:last-child {
        font-size: 11px;
        color: black;
    }
    .card-body{
        height: 100%;
    }
</style>

{{-- <div class="row"> --}}
    {{-- <div class="col-xl-12"> --}}
        {{-- <div class="card"> --}}
            {{-- <div class="card-body"> --}}
                <div class="border rounded">
                    {{-- <div class="card"> --}}
                        <div class="card-header">
                           <strong>Bid Information</strong> 
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 ">
                                    @if(!blank($bid))
                                        
                                        @php $client_name = is_null($bid->client) ? '' : ($bid->client->client_name ?? '') @endphp

                                        <div class="row">
                                            <div class="remarlist col-md-6"><strong>Bid Date :</strong>{{ date('d M Y',strtotime($bid->bid_date)) }}</div>
                                            <div class="remarlist col-md-6"><strong>Client Name :</strong> {{ $bid->client->client_name }}</div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="remarlist col-md-6"><strong>Project Type :</strong> {{ $bid->project_type }}</div>
                                            <div class="remarlist col-md-6"><strong>Portal :</strong>
                                                <a href="{{ $bid->job_link }}" target="_blank" class="text-success" title="Visit Job">{{ $bid->portal }}</a>
                                            </div>    
                                        </div>

                                        <div class="row">
                                            <div class="remarlist col-md-6"><strong>Username :</strong>  {{ $bid->user_name }}</div>
                                            <div class="remarlist col-md-6"><strong>Technology :</strong> {{ $bid->technology }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="remarlist col-md-6"><strong>Profile :</strong> {{ $bid->profile }}</div>
                                            <div class="remarlist col-md-6"><strong>Connects Needed :</strong> {{ $bid->connects_needed }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="remarlist col-md-6"><strong>Client Budget : {{$bid->currency}}</strong> {{" "}}{{ $bid->client_budget }}</div>
                                            <div class="remarlist col-md-6"><strong>Bid Quote :</strong> {{ $bid->bid_quote }}</div>
                                        </div> 
                                        <div class="row">
                                            <div class="remarlist col-md-6"><strong>Bid Converted to Lead :</strong> {{ $bid->is_lead_converted ? 'Yes' : 'No' }}</div>
                                            <div class="remarlist col-md-6"><strong>Bid Status :</strong> {{ $bid->status }}</div>
                                        </div> 
                                        <div class="row">
                                            <div class="remarlist col-md-12"><strong>Job Title :</strong> {{ $bid->job_title }}{{$client_name ? " (".$client_name.")" : ''}}</div>
                                            <div class="remarlist col-md-12"><strong>Job Description :</strong> {{ $bid->description }}</div>
                                            <div class="remarlist col-md-12"><strong>Job Link :</strong>
                                                <a href="{{ $bid->job_link }}" target="_blank" class="text-success" title="Visit Job">{{ $bid->job_link }}</a>
                                            </div>
                                        </div>
                                    @endif 
                                </div>
                            </div>
                        </div>
                    {{-- </div> --}}
                </div>
            {{-- </div> --}}
        {{-- </div> --}}
    {{-- </div> --}}
{{-- </div> --}}

<script>
    
    $(function() {
        $('a[rel*=facebox]').facebox();
    });

    function enableFields(){
        document.getElementsByClassName('status')[0].disabled = false;
        document.getElementsByClassName('project_type')[0].disabled = false;
    }

    function disable(event){
        $data = $('#next_followup').val();
        if (event.target.checked) {
            $('#next_followup').prop('disabled', false); // If checked enable item 
            $('#disable_btn').attr("checked",false);      
        } else {
            // $('#next_followup').val(null); // If checked disable item 
            $('#next_followup').prop('disabled', true); // If checked disable item 
            $('#disable_btn').attr("checked",true);                                    
        }
    }
</script>