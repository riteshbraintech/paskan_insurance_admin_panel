@extends('admin.layouts.app')

@section('content')
    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Edit User Page</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active"><a href="{{ route('admin.user.index') }}">User Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit User Page</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">

                        <form action="{{ route('admin.user.update', $record->id) }}" method="POST"
                            class="row g-3 needs-validation" enctype="multipart/form-data">
                            @csrf
                            {{-- @method('PUT') --}}

                            <!-- Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $record->name) }}"
                                    class="form-control" id="name" placeholder="Enter full name">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $record->email) }}"
                                    class="form-control" id="email" placeholder="Enter email">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div class="col-md-6">
                                <label for="dob" class="form-label">Date of Birth <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="dob" value="{{ old('dob', $record->dob) }}"
                                    class="form-control" id="dob">
                                @error('dob')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Gender -->
                            <div class="col-md-6">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select" name="gender" id="gender">
                                    <option disabled value="">Choose...</option>
                                    <option value="male" {{ old('gender', $record->gender) == 'male' ? 'selected' : '' }}>
                                        Male</option>
                                    <option value="female"
                                        {{ old('gender', $record->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other"
                                        {{ old('gender', $record->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nationality -->
                            <div class="col-md-6">
                                <label for="nationality" class="form-label">Nationality <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nationality"
                                    value="{{ old('nationality', $record->nationality) }}" class="form-control"
                                    id="nationality" placeholder="Enter nationality">
                                @error('nationality')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Marital Status -->
                            <div class="col-md-6">
                                <label for="marital_status" class="form-label">Marital Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="marital_status" id="marital_status">
                                    <option disabled value="">Choose...</option>
                                    <option value="single"
                                        {{ old('marital_status', $record->marital_status) == 'single' ? 'selected' : '' }}>
                                        Single</option>
                                    <option value="married"
                                        {{ old('marital_status', $record->marital_status) == 'married' ? 'selected' : '' }}>
                                        Married</option>
                                    <option value="divorced"
                                        {{ old('marital_status', $record->marital_status) == 'divorced' ? 'selected' : '' }}>
                                        Divorced</option>
                                    <option value="widowed"
                                        {{ old('marital_status', $record->marital_status) == 'widowed' ? 'selected' : '' }}>
                                        Widowed</option>
                                </select>
                                @error('marital_status')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mobile -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Mobile Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $record->phone) }}"
                                    class="form-control" id="phone" placeholder="Enter mobile number">
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ID Number -->
                            <div class="col-md-6">
                                <label for="id_number" class="form-label">ID Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="id_number"
                                    value="{{ old('id_number', $record->id_number) }}" class="form-control"
                                    id="id_number" placeholder="Enter ID number">
                                @error('id_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>


                            <!-- Address -->
                            <div class="col-md-12">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea name="address" id="address" class="form-control" rows="2" placeholder="Enter address">{{ old('address', $record->address) }}</textarea>
                                @error('address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            

                            <!-- Password -->
                            {{-- <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" id="password"
                                    placeholder="Leave blank to keep current password">
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div> --}}

                            
                            <!-- Submit / Back Buttons -->
                            <div class="col-12">
                                <div class="row row-cols-auto g-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success px-5 radius-30">Update</button>
                                    </div>
                                    <div class="col">
                                        <a href="{{ route('admin.user.index') }}"
                                            class="btn btn-outline-secondary px-5 radius-30">Back</a>
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


{{-- @push('scripts')
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
@endpush --}}
