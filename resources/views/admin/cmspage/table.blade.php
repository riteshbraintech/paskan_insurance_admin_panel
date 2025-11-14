@php
    $i = $records->firstItem();
@endphp
<div class="table-responsive mt-3">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                <th class="no-wrap">@sortablelink('id','Sl No.')</th>
                <th class="no-wrap">Page Slug</th>
                @foreach (langueses() as $langCode => $language)
                    <th class="no-wrap">Title ({{ $language }})</th>
                @endforeach
                <th class="no-wrap">@sortablelink('is_published','Is published')</th>
                <th class="no-wrap text-center" width="100px">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $key => $item)

                <tr class="">
                    <td>{{ $i }}</td>
                    <td>{{ $item->page_slug }}</td>
                    {{-- // display title for each language --}}
                    @foreach (langueses() as $langCode => $language)
                        <td>{{ $item->translations->where('lang_code', $langCode)->first()->title ?? 'N/A' }}</td>
                    @endforeach

                    <td>
                        <span
                            class="badge badge-{{ $item->is_published == 1 ? 'success' : 'danger' }} status-{{ $item->id }}"
                            onclick="changeStatus(event,{{ $item->id }})">{{ ucfirst($item->is_published == 1 ? 'Yes' : 'No') }}
                        </span>
                    </td>
    
                    <td>
                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                            <a href="{{ route('admin.cmspage.show', ['cmspage' => $item->id]) }}"
                                class="text-success facebox" rel="facebox" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="View"><i
                                    class="fa-solid fa-eye text-success fs-5"></i></a>

                            <a href="{{ route('admin.cmspage.edit', ['cmspage' => $item->id]) }}" class="text-warning"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                    class="fa-solid fa-pen-to-square text-warning fs-5"></i></a>

                            <a href="javascript:void(0);"
                                onclick="deleteItem('{{ route('admin.cmspage.destroy', ['cmspage' => $item->id]) }}')"
                                class="text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                title="Delete"><i class="fa-solid fa-trash fs-5"></i></a>
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
<script></script>
{{-- pagination with filter --}}
@include('admin.elements.filter-with-pagi', ['data' => $records])
