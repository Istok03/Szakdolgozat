<section class="profile-card" aria-labelledby="profile-information-title">
    <h2 id="profile-information-title">Személyes adatok</h2>
    <p class="profile-description">Tartsd naprakészen a nevedet és az e-mail-címedet.</p>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    <form method="post" action="{{ route('profile.update') }}" class="profile-form">
        @csrf
        @method('patch')
        <label for="name">Név</label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" maxlength="255" required autocomplete="name" aria-describedby="name-error" @if($errors->has('name')) aria-invalid="true" @endif>
        <div id="name-error" class="profile-error">@foreach($errors->get('name') as $error)<p>{{ $error }}</p>@endforeach</div>

        <label for="email">E-mail-cím</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" maxlength="255" required autocomplete="email" aria-describedby="email-error" @if($errors->has('email')) aria-invalid="true" @endif>
        <div id="email-error" class="profile-error">@foreach($errors->get('email') as $error)<p>{{ $error }}</p>@endforeach</div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <p>Az e-mail-címed még nincs megerősítve.</p>
            <button type="submit" form="send-verification" class="profile-button profile-button-secondary">Megerősítő e-mail újraküldése</button>
            @if(session('status') === 'verification-link-sent')
                <p class="profile-success" role="status">Elküldtük az új megerősítő e-mailt.</p>
            @endif
        @endif
        <div class="profile-actions">
            <button type="submit" class="profile-button">Adatok mentése</button>
            @if(session('status') === 'profile-updated')
                <p class="profile-success" role="status">Az adataidat sikeresen mentettük.</p>
            @endif
        </div>
    </form>
</section>