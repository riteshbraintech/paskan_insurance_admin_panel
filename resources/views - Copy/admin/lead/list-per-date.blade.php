@php
    // $i = 1;
@endphp

<style scoped>
    #facebox {
        top: 140px !important;
        left: 25%;
        width: 70%;
        position: fixed !important;
        height: auto;
        max-height:500px;
    }
    #facebox .popup {
        height: 100%;
        overflow-y: auto;
    }

    #facebox .content {
        width: 100%;
        height: 100%;
        border-radius:0;
    }
    
    .close img{
        height: 12px;
        width: 12px;
    }
    #facebox .close {
        position: absolute;
        top: 5px;
        right: 9px;
        padding: 2px;
        background: #fff;
    }
</style>

<div class="table-responsive mt-4">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                {{-- <td class="no-wrap">#</td> --}}
                <td class="no-wrap">Lead ID</td>
                <td class="no-wrap">Bid Date</td>
                <td class="no-wrap">Followup</td>
                <td class="no-wrap">Lead Created</td>
                <td class="no-wrap">User Name</td>
                <td class="text-wrap">Job Title</td>
                <td class="no-wrap">Portal</td>
                <td class="no-wrap">Type</td>
                <td class="no-wrap">Status</td>
                {{-- <td>Action</td> --}}
            </tr>
        </thead>
        <tbody>
            @forelse ($leads as $key => $item)

                @php $client_name = is_null($item->client) ? '' : ($item->client->client_name ?? '') @endphp

                <tr>
                    {{-- <td class="no-wrap">{{ $i }}</td> --}}
                    {{-- <td> --}}
                    <td class="text-success no-wrap" style="cursor:pointer;" onclick="viewLead('{{ route('admin.lead.view', ['id' => $item->id]) }}');" title="{{$item->client->client_name ?? 'Client Not Available'}}">
                        {{ $item->lead_id }}
                    </td>
                    <td class="no-wrap">{{ date('d M Y', strtotime($item->bid_date)) }}</td>
                    <td class="no-wrap">{{ date('d M Y', strtotime($item->next_followup)) }}</td>
                    <td class="no-wrap">{{ date('d M Y', strtotime($item->created_at)) }}</td>
                    <td class="no-wrap">{{ $item->user_name }}</td>
                    <td class="text-wrap" style="width:15%">{{ $item->job_title }}{{$client_name ? " (".$client_name.")" : ''}}</td>
                    <td class="no-wrap">
                        <a href="{{ $item->job_link }}" target="_blank" class="text-success" title="Visit Job">{{ $item->portal }}</a>
                    </td>
                    <td class="no-wrap">{{ $item->project_type }}</td>
                    <td class="no-wrap">{{$item->status}}</td>
                </tr>
                @php
                    // $i++;
                @endphp
            @empty
                <tr>
                    <td colspan="10" class="text-center">
                        No Record Found !
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(function() {
        $('a[rel*=facebox]').facebox();
    });

    function viewLead(url){
        jQuery.facebox({ajax: url});
        // $('#facebox').css('height', '250%');
        // $('#facebox .popup').css('height', '100%');
        // $('#facebox .popup .content').css('height', '100%');

        // $('#facebox .popup .row .col-xl-12 .card .card-body').css('height', '100%');
    }
</script>