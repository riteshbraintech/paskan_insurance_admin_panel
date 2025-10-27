@extends('admin.layouts.app')

@section('content')
@include('admin.components.FlashMessage')

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Edit CMS Page</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.cmspage.index') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active"><a href="{{ route('admin.cmspage.index') }}">CMS Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit CMS Page</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xl-9 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="p-4 border rounded">

                    <form action="{{ route('admin.cmspage.update', $record->id) }}" method="POST" class="row g-3 needs-validation" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Multilingual Title --}}
                        @foreach (langueses() as $langCode => $language)
                            @php
                                $titleValue = old("trans.$langCode.title", $translations[$langCode]['title'] ?? '');
                                $idValue = $translations[$langCode]['id'] ?? '';
                            @endphp

                            <input type="hidden" name="trans[{{ $langCode }}][id]" class="form-control"  value="{{ $idValue }}" />

                            <div class="col-md-6">
                                <label class="form-label">Title ({{ $language }}) <span class="text-danger">*</span></label>
                                <input type="text" name="trans[{{ $langCode }}][title]" class="form-control" 
                                    value="{{ $titleValue }}" 
                                    placeholder="Enter Title in {{ $language }}">
                                @error("trans.$langCode.title")
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach

                        {{-- Multilingual Content --}}
                        @foreach (langueses() as $langCode => $language)
                            @php
                                $contentValue = old("trans.$langCode.content", $translations[$langCode]['content'] ?? '');
                            @endphp
                            <div class="col-md-12">
                                <label class="form-label">Content ({{ $language }})</label>
                                <textarea name="trans[{{ $langCode }}][content]" class="form-control" rows="3">{{ $contentValue }}</textarea>
                                @error("trans.$langCode.content")
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach

                        {{-- SEO Fields --}}
                        @foreach (langueses() as $langCode => $language)
                            @php
                                $metaTitle = old("trans.$langCode.meta_title", $translations[$langCode]['meta_title'] ?? '');
                                $metaKeywords = old("trans.$langCode.meta_keywords", $translations[$langCode]['meta_keywords'] ?? '');
                                $metaDescription = old("trans.$langCode.meta_description", $translations[$langCode]['meta_description'] ?? '');
                            @endphp
                            <div class="col-md-4">
                                <label class="form-label">Meta Title ({{ $language }})</label>
                                <input type="text" name="trans[{{ $langCode }}][meta_title]" class="form-control" value="{{ $metaTitle }}">
                                @error("trans.$langCode.meta_title")
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Meta Keywords ({{ $language }})</label>
                                <input type="text" name="trans[{{ $langCode }}][meta_keywords]" class="form-control" value="{{ $metaKeywords }}">
                                @error("trans.$langCode.meta_keywords")
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Meta Description ({{ $language }})</label>
                                <input type="text" name="trans[{{ $langCode }}][meta_description]" class="form-control" value="{{ $metaDescription }}">
                                @error("trans.$langCode.meta_description")
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach

                        <div class="col-12">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-success px-5 radius-30">Update</button>
                                </div>
                                <div class="col">
                                    <a href="{{ route('admin.cmspage.index') }}" class="btn btn-outline-secondary px-5 radius-30">Back</a>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
