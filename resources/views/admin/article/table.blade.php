@php
    $i = $records->firstItem();
@endphp
<div class="table-responsive mt-3">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                <th class="no-wrap">@sortablelink('id', 'Sl.No.')</th>
                <th class="no-wrap">Image</th>
                @foreach (langueses() as $langCode => $language)
                    <th class="no-wrap">Title ({{ $language }}) </th>
                @endforeach
                <th class="no-wrap"> @sortablelink('is_active', 'Status')</th>
                <th class="no-wrap text-center" width="100px">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $key => $item)

                <tr class="">
                    <td>{{ $i }}</td>
                    <td>
                        <img src="{{ $item->image_url }}" alt="Banner Image"
                            style="object-fit: contain;border-radius: 6px;border: 1px solid #ddd;background-color: #f8f9fa;max-width: 35px;height: 35px;padding: 4px;">
                    </td>

                    {{-- // display title for each language --}}
                    @foreach (langueses() as $langCode => $language)
                        <td>{{ $item->translations->where('lang_code', $langCode)->first()->title ?? 'N/A' }}</td>
                    @endforeach

                    <td>
                        <span
                            class="badge badge-{{ $item->is_active == 1 ? 'success' : 'danger' }} status-{{ $item->id }}"
                            onclick="changeStatus(event,{{ $item->id }})">{{ ucfirst($item->is_active == 1 ? 'Active' : 'Inactive') }}
                        </span>
                    </td>

                    <td>
                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                            <a href="{{ route('admin.article.view', ['id' => $item->id]) }}"
                                class="text-success facebox" rel="facebox" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="View"><i class="fa-solid fa-eye fs-5"></i></a>

                            <a href="{{ route('admin.article.edit', ['id' => $item->id]) }}" class="text-warning"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                    class="fa-solid fa-pen-to-square text-warning fs-5"></i></a>

                            <a href="javascript:void(0);"
                                onclick="deleteItem('{{ route('admin.article.delete', ['id' => $item->id]) }}')"
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
