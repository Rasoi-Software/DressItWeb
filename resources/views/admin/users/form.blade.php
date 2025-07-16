<div class="row">
    <div class="col-md-6 mb-3">
        <label>Name *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', optional($user)->name) }}">
        @error('name')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label>Nickname</label>
        <input type="text" name="nickname" class="form-control" value="{{ old('nickname', optional($user)->nickname) }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Email *</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', optional($user)->email) }}">
        @error('email')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', optional($user)->phone) }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Location</label>
        <input type="text" name="location" class="form-control" value="{{ old('location', optional($user)->location) }}">
    </div>

    <div class="col-md-3 mb-3">
        <label>Age</label>
        <input type="number" name="age" class="form-control" value="{{ old('age', optional($user)->age) }}">
    </div>

    <div class="col-md-3 mb-3">
        <label>Date of Birth</label>
        <input type="date" name="dob" class="form-control" value="{{ old('dob', optional($user)->dob) }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Gender</label>
        <select name="gender" class="form-control">
            <option value="">-- Select --</option>
            <option value="male" {{ old('gender', optional($user)->gender) == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', optional($user)->gender) == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('gender', optional($user)->gender) == 'other' ? 'selected' : '' }}>Other</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label>Interested In</label>
        <select name="interested_in" class="form-control">
            <option value="">-- Select --</option>
            <option value="male" {{ old('interested_in', optional($user)->interested_in) == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('interested_in', optional($user)->interested_in) == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('interested_in', optional($user)->interested_in) == 'other' ? 'selected' : '' }}>Other</option>
        </select>
    </div>

    <div class="col-md-12 mb-3">
        <label>Bio</label>
        <textarea name="bio" class="form-control">{{ old('bio', optional($user)->bio) }}</textarea>
    </div>

    <div class="col-md-6 mb-3">
        <label>Profile Image</label>
        <input type="file" name="profile_image" class="form-control">
        @if (!empty($user->profile_image))
        <img src="{{ asset('storage/' . $user->profile_image) }}" class="img-thumbnail mt-2" width="100">
        @endif
    </div>

    <div class="col-md-6 mb-3">
        <label>Password @if(!isset($user)) * @endif</label>
        <input type="password" name="password" class="form-control" >
        @error('password')
        <small class="text-danger">{{ $message }}</small>
        @enderror
        <input type="password" name="password_confirmation" class="form-control mt-2" placeholder="Confirm Password">
    </div>
</div>