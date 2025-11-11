@php $i = 1; @endphp

<style>
    #facebox {
        top: 78.3px;
        left: 25%;
        width: 90%;
    }

    .close img{
        height: 12px;
        width: 12px;
    }
    #facebox .close {
        position: absolute;
        top: 6px;
        right: 9px;
        padding: 2px;
        background: #fff;
    }
        #facebox .content {
        width: 100%;
    }
</style>
<div class="table-responsive mt-3" style=" overflow-y: auto;max-height: 80vh;height:auto;">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                <td>#</td>
                {{-- <td>Admin ID</td> --}}
                <td>Username</td>
                <td>Lead ID</td>
                <td>Filed</td>
                <td>Old Value</td>
                <td>New Value</td>
                <td>Messages</td>
                <td>Created At</td>
            </tr>
        </thead>
        <tbody>
            @if($logs && !empty($logs))
                @forelse ($logs as $key => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        {{-- <td>{{ $item->admin_id }}</td> --}}
                        <td>{{ $item->user_name }}</td>
                        <td class="no-wrap">{{ $item->lead_show_id }}</td>
                        <td class="no-wrap">{{ $item->page }}</td>
                        <td class="no-wrap">{{ $item->old_status }}</td>
                        <td class="no-wrap">{{ $item->new_status }}</td>
                        <td>{{ $item->messages }}</td>
                        <td class="no-wrap">{{ date('d M Y',strtotime($item->created_at)) }}</td>
                    </tr>
                    @php $i++; @endphp
                @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            No Record Found !
                        </td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>
</div>