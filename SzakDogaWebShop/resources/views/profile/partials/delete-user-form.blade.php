<section class="profile-card profile-delete" aria-labelledby="profile-delete-title">
    <h2 id="profile-delete-title">Fiók törlése</h2>
    <p class="profile-description">A fiókod törlése végleges. A hozzá tartozó kosár, vélemények és rendelési adatok is törlődnek.</p>
    <details class="profile-delete-confirmation" @if($errors->userDeletion->isNotEmpty()) open @endif>
        <summary>Fióktörlés megerősítése</summary>
        <form method="post" action="{{ route('profile.destroy') }}" class="profile-form">
            @csrf
            @method('delete')
            <p>Biztosan törölni szeretnéd a fiókodat? A megerősítéshez add meg a jelenlegi jelszavadat.</p>
            <label for="delete_account_password">Jelenlegi jelszó</label>
            <input id="delete_account_password" name="password" type="password" required autocomplete="current-password" aria-describedby="delete-password-error" @if($errors->userDeletion->has('password')) aria-invalid="true" @endif>
            <div id="delete-password-error" class="profile-error">@foreach($errors->userDeletion->get('password') as $error)<p>{{ $error }}</p>@endforeach</div>
            <button type="submit" class="profile-button profile-button-danger">Fiók végleges törlése</button>
        </form>
    </details>
</section>