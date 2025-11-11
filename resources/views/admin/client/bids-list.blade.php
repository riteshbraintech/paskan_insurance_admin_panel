@php
    // $i = 1;
@endphp

<div class="table-responsive mt-4">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                {{-- <td>#</td> --}}
                <td class="no-wrap text-center">Bid Date</td>
                <td class="no-wrap text-center">Bid Created</td>
                <td class="no-wrap text-center">User Name</td>
                <td class="no-wrap text-center">Job Title</td>
                <td class="no-wrap text-center">Portal</td>
                <td class="no-wrap text-center">Type</td>
                <td class="no-wrap text-center">Bid Quote</td>
                <td class="no-wrap text-center">Lead Convert</td>
                <td class="no-wrap text-center">Lead ID</td>
                <td class="no-wrap text-center">Status</td>
                <td class="no-wrap text-center">Bid View</td>
                <td class="no-wrap text-center">Lead View</td>
            </tr>
        </thead>
        <tbody>
            @forelse ($bids as $key => $item)

                @php $client_name = is_null($item->client) ? '' : ($item->client->client_name ?? '') @endphp

                <tr>
                    {{-- <td>{{ $i }}</td> --}}
                    <td class="no-wrap text-center">{{ date('d M Y',strtotime($item->bid_date)) }}</td>
                    <td class="no-wrap text-center">{{ date('d-M-Y',strtotime($item->created_at)) }}</td>
                    <td class="no-wrap text-center">{{ $item->user_name }}</td>
                    <td class="no-wrap text-center">{{ $item->job_title }}{{$client_name ? " (".$client_name.")" : ''}}</td>
                    <td class="no-wrap text-center"> <a href="{{ $item->job_link }}" target="_blank" class="text-success" title="Visit Job">{{ $item->portal }}</a> </td>
                    <td class="no-wrap text-center">{{ $item->project_type }}</td>
                    <td class="no-wrap text-center">{{ $item->bid_quote }}</td>
                    <td class="no-wrap text-center">{{ $item->is_lead_converted ? 'Yes' : 'No' }}</td>
                    
                    <td class="no-wrap">{{ $item->lead->lead_id ?? '' }}</td>
                    <td class="no-wrap text-center"> 
                        <span class="badge badge-{{ $item->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($item->status) }}</span>
                    </td>
                    <td class="no-wrap text-center">
                        <div class="table-actions d-flex align-items-center justify-content-center gap-3 fs-6">
                            <a href="{{ route('admin.bid.edit', ['id' => $item->id]) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="fa-solid fa-eye"></i></a>
                        </div>
                    </td>
                    @if ($item->is_lead_converted)
                        <td class="no-wrap text-center">
                            <div class="table-actions d-flex align-items-center justify-content-center gap-3 fs-6">
                                <a href="{{ route('admin.lead.edit', ['id' => $item->lead->id ?? '']) }}" class="text-success" title="Edit"><i class="fa-solid fa-eye"></i></a>
                            </div>
                        </td>
                    @else
                        <td></td>
                    @endif
                </tr>
                @php
                    // $i++;
                @endphp
            @empty
                <tr>
                    <td colspan="13" class="text-center">
                        No Record Found !
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    // function viewLead(url){
    //     jQuery.facebox({ajax: url});
    //     $('#facebox').css('height', '250%');
    //     $('#facebox .popup').css('height', '100%');
    //     $('#facebox .popup .content').css('height', '100%');
    //     $('#facebox .popup .row .col-xl-12 .card .card-body').css('height', '100%');
    // }
</script>