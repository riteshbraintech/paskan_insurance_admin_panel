@php
    $i = $clients->firstItem();
@endphp
<div class="table-responsive mt-3">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                <td>
                    <div title="Merge clients" class="mergeIcon" style="display: none;cursor:pointer;">
                        <i class="fa-regular fa-object-group text-primary" style="font-size: 18px;"></i>
                    </div>
                </td>
                <td class="no-wrap">Sl No.</td>
                <td class="no-wrap">Client Name</td>
                <td class="no-wrap">Bid Count</td>
                <td class="no-wrap">Mobile</td>
                <td class="no-wrap">Email</td>
                <td class="no-wrap">Skype</td>
                <td class="no-wrap">Linked IN</td>
                <td class="no-wrap">Other</td>
                <td class="no-wrap" width="100px">Action</td>
            </tr>
        </thead>
        <tbody>
            @forelse ($clients as $key => $item)
                <tr class="{{($item->is_test == 1 && admin()->user()->role_id == 'superadmin') ? 'table-warning' : ''}}">
                    <td>
                        <input type="checkbox" class="selectItems" name="client[]" value="{{ $item->id }}">
                    </td>
                    <td>{{ $i }}</td>
                    <td>{{ $item->client_name }}</td>
                    <td>
                        <a href="{{ route('admin.client.client-bids-list', ['id' => $item->id]) }}" class="text-success client-bids-list" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View" rel="facebox">{{ $item->getTotalBidsCount()}}</a>
                    </td>
                    <td>{{ $item->mobile }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->skype }}</td>
                    <td>{{ $item->linkedin }}</td>
                    <td>{{ $item->other }}</td>
                    <td>
                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                            <a href="{{ route('admin.client.edit', ['id' => $item->id]) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="fa-solid fa-pen-to-square text-warning fs-5"></i></a>
                            
                            <a href="javascript:void(0);" onclick="deleteItem('{{ route('admin.client.delete', ['id' => $item->id]) }}')" class="text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="fa-solid fa-trash fs-5"></i></a>
                        </div>
                    </td>
                </tr>
                @php
                    $i++;
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
</script>
{{-- pagination with filter --}}
@include('admin.elements.filter-with-pagi', ['data' => $clients])
