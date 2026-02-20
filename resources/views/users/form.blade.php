@csrf

<div class="mb-3">
    <label class="form-label">First name</label>
    <input name="first_name" value="{{ old('first_name', $user->first_name ?? '') }}" class="form-control" />
</div>

<div class="mb-3">
    <label class="form-label">Last name</label>
    <input name="last_name" value="{{ old('last_name', $user->last_name ?? '') }}" class="form-control" />
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input name="email" value="{{ old('email', $user->email ?? '') }}" required class="form-control" type="email" />
</div>

<div class="mb-3">
    <label class="form-label">Password @if(isset($user)) (leave blank to keep) @endif</label>
    <input name="password" class="form-control" type="password" />
</div>

<div class="mb-3">
    <label class="form-label">Confirm password</label>
    <input name="password_confirmation" class="form-control" type="password" />
</div>

<div class="mb-3">
    <label class="form-label">Roles</label>
    <select name="roles[]" class="form-select" multiple>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" 
              @if(isset($user) && $user->roles->pluck('id')->contains($role->id)) selected @endif>
              {{ ucfirst($role->name) }}
            </option>
        @endforeach
    </select>
    <small class="text-muted">Hold ctrl/cmd to multi-select</small>
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
        {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<button class="btn btn-primary">Save</button>
