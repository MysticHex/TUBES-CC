@csrf

<div class="row g-3">
    <div class="col-12">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" name="name" id="name"
               value="{{ old('name', $member->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required autofocus>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="nim" class="form-label">NIM</label>
        <input type="text" name="nim" id="nim"
               value="{{ old('nim', $member->nim ?? '') }}"
               class="form-control @error('nim') is-invalid @enderror" required>
        @error('nim') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="class" class="form-label">Class</label>
        <input type="text" name="class" id="class"
               value="{{ old('class', $member->class ?? '') }}"
               class="form-control @error('class') is-invalid @enderror" required placeholder="e.g. TI-3A">
        @error('class') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label for="role" class="form-label">Role</label>
        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
            @php($current = old('role', $member->role ?? ''))
            <option value="" disabled @selected($current === '')>Select role…</option>
            @foreach (['Ketua', 'Anggota'] as $role)
                <option value="{{ $role }}" @selected($current === $role)>{{ $role }}</option>
            @endforeach
        </select>
        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i> {{ $submitLabel }}
    </button>
    <a href="{{ route('members.index') }}" class="btn btn-light border">Cancel</a>
</div>
