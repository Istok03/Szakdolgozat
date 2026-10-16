<section class="profile-card" aria-labelledby="profile-password-title">
    <h2 id="profile-password-title">Jelszó módosítása</h2>
    <p class="profile-description">Válassz legalább 8 karakteres, egyedi jelszót.</p>
    <form method="post" action="{{ route('password.update') }}" class="profile-form">
        @csrf
        @method('put')
        @foreach(['current_password' => 'Jelenlegi jelszó', 'password' => 'Új jelszó', 'password_confirmation' => 'Új jelszó megerősítése'] as $field => $label)
            <label for="update_{{ $field }}">{{ $label }}</label>
            <input id="update_{{ $field }}" name="{{ $field }}" type="password" required autocomplete="{{ $field === 'current_password' ? 'current-password' : 'new-password' }}" @if($field !== 'current_password') minlength="8" @endif aria-describedby="{{ $field }}-error" @if($errors->updatePassword->has($field)) aria-invalid="true" @endif>
            <div id="{{ $field }}-error" class="profile-error">@foreach($errors->updatePassword->get($field) as $error)<p>{{ $error }}</p>@endforeach</div>
        @endforeach
        <div class="profile-actions">
            <button type="submit" class="profile-button">Jelszó mentése</button>
            @if(session('status') === 'password-updated')
                <p class="profile-success" role="status">A jelszavadat sikeresen módosítottuk.</p>
            @endif
        </div>
    </form>
</section>