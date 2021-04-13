<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name ?? '') }}" placeholder="John Doe" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email ?? '') }}" placeholder="john@example.com" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold">Password {{ isset($user) ? '(leave blank to keep)' : '' }} <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Min 6 characters" {{ isset($user) ? '' : 'required' }}>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold">Confirm Password {{ isset($user) ? '' : '<span class="text-danger">*</span>' }}</label>
            <input type="password" name="password_confirmation" class="form-control"
                placeholder="Repeat password" {{ isset($user) ? '' : 'required' }}>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold">Phone</label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone', $user->phone ?? '') }}" placeholder="+91 9000000000">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold">Company</label>
            <input type="text" name="company" class="form-control @error('company') is-invalid @enderror"
                value="{{ old('company', $user->company ?? '') }}" placeholder="ABC Pvt Ltd">
            @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label class="font-weight-bold">Address</label>
            <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror"
                placeholder="Full billing address">{{ old('address', $user->address ?? '') }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
