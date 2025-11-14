@extends('admin.layouts.app')
@section('content')
    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Create User Page</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.user.index') }}">User
                            Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create User Page</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form action="{{ route('admin.user.store') }}" method="post" class="row g-3 needs-validation"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                                    id="name" placeholder="Enter full name">
                                @if ($errors->has('name'))
                                    <div class="text-danger">{{ $errors->first('name') }}</div>
                                @endif
                            </div>

                            <!-- ID Number -->
                            <div class="col-md-6">
                                <label for="id_number" class="form-label">ID Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="id_number" value="{{ old('id_number') }}" class="form-control"
                                    id="id_number" placeholder="Enter ID number">
                                @if ($errors->has('id_number'))
                                    <div class="text-danger">{{ $errors->first('id_number') }}</div>
                                @endif
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                                    id="email" placeholder="Enter email">
                                @if ($errors->has('email'))
                                    <div class="text-danger">{{ $errors->first('email') }}</div>
                                @endif
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" id="password"
                                    placeholder="Enter password">
                                @if ($errors->has('password'))
                                    <div class="text-danger">{{ $errors->first('password') }}</div>
                                @endif
                            </div>

                            

                            <!-- Date of Birth -->
                            <div class="col-md-6">
                                <label for="dob" class="form-label">Date of Birth <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="dob" value="{{ old('dob') }}" class="form-control"
                                    id="dob">
                                @if ($errors->has('dob'))
                                    <div class="text-danger">{{ $errors->first('dob') }}</div>
                                @endif
                            </div>

                            <!-- Gender -->
                            <div class="col-md-6">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select" name="gender" id="gender">
                                    <option selected disabled value="">Choose...</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female
                                    </option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @if ($errors->has('gender'))
                                    <div class="text-danger">{{ $errors->first('gender') }}</div>
                                @endif
                            </div>

                            <!-- Nationality -->
                            <div class="col-md-6">
                                <label for="nationality" class="form-label">Nationality <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nationality" value="{{ old('nationality') }}"
                                    class="form-control" id="nationality" placeholder="Enter nationality">
                                @if ($errors->has('nationality'))
                                    <div class="text-danger">{{ $errors->first('nationality') }}</div>
                                @endif
                            </div>

                            <!-- Marital Status -->
                            <div class="col-md-6">
                                <label for="marital_status" class="form-label">Marital Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="marital_status" id="marital_status">
                                    <option selected disabled value="">Choose...</option>
                                    <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>
                                        Single
                                    </option>
                                    <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>
                                        Married</option>
                                    <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>
                                        Divorced</option>
                                </select>
                                @if ($errors->has('marital_status'))
                                    <div class="text-danger">{{ $errors->first('marital_status') }}</div>
                                @endif
                            </div>

                            <!-- Mobile -->
                            <div class="col-md-12">
                                <label for="phone" class="form-label">Mobile Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control"
                                    id="phone" placeholder="Enter mobile number">
                                @if ($errors->has('phone'))
                                    <div class="text-danger">{{ $errors->first('phone') }}</div>
                                @endif
                            </div>

                            <!-- Address -->
                            <div class="col-md-12">
                                <label for="address" class="form-label">Address <span
                                        class="text-danger">*</span></label>
                                <textarea name="address" id="address" class="form-control" rows="1" placeholder="Enter address">{{ old('address') }}</textarea>
                                @if ($errors->has('address'))
                                    <div class="text-danger">{{ $errors->first('address') }}</div>
                                @endif
                            </div>



                            <!-- Submit Buttons -->
                            <div class="col-12">
                                <div class="row row-cols-auto g-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success px-5 radius-30">Create</button>
                                    </div>
                                    <div class="col">
                                        <a href="{{ route('admin.user.index') }}"
                                            class="btn btn-outline-success px-5 radius-30">Back</a>
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

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @foreach (langueses() as $langCode => $language)
                ClassicEditor
                    .create(document.querySelector('#trans_{{ $langCode }}_content'))
                    .then(editor => {
                        editor.ui.view.editable.element.style.minHeight = '200px';
                    })
                    .catch(error => console.error(error));
            @endforeach
        });
    </script>
@endpush
