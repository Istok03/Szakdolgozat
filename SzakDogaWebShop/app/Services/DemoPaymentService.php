<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DemoPaymentService
{
    public const FIELDS = ['card_number', 'card_expiry', 'card_cvc'];

    public const CARDS = [
        '4242424242424242' => ['brand' => 'Visa', 'approved' => true],
        '5555555555554444' => ['brand' => 'Mastercard', 'approved' => true],
        '4000000000000002' => ['brand' => 'Visa', 'approved' => false],
        '5105105105105100' => ['brand' => 'Mastercard', 'approved' => false],
    ];

    public function simulate(array $input): array
    {
        if (isset($input['card_number']) && is_string($input['card_number'])) {
            $input['card_number'] = str_replace([' ', '-'], '', $input['card_number']);
        }
        $data = Validator::make($input, [
            'card_number' => ['required', 'string', 'regex:/\A[0-9]{16}\z/'],
            'card_expiry' => ['required', 'string', 'regex:/\A(0[1-9]|1[0-2])\/[0-9]{2}\z/'],
            'card_cvc' => ['required', 'string', 'regex:/\A[0-9]{3}\z/'],
        ], [
            'card_number.*' => 'Adj meg egy 16 számjegyű tesztkártyaszámot.',
            'card_expiry.*' => 'A lejárat formátuma HH/ÉÉ legyen (például 12/30).',
            'card_cvc.*' => 'Adj meg egy háromjegyű teszt CVC-kódot.',
        ])->validate();

        $number = $data['card_number'];
        $sum = 0;
        foreach (str_split(strrev($number)) as $index => $digit) {
            $value = (int) $digit * ($index % 2 === 1 ? 2 : 1);
            $sum += $value > 9 ? $value - 9 : $value;
        }
        if ($sum % 10 !== 0) {
            throw ValidationException::withMessages(['card_number' => 'A kártyaszám ellenőrző összege hibás.']);
        }
        $card = self::CARDS[$number] ?? null;
        if (!$card) {
            throw ValidationException::withMessages(['card_number' => 'Kizárólag az oldalon felsorolt Visa és Mastercard tesztkártyák használhatók.']);
        }
        [$month, $year] = explode('/', $data['card_expiry']);
        if ((2000 + (int) $year) * 100 + (int) $month < (int) now()->format('Ym')) {
            throw ValidationException::withMessages(['card_expiry' => 'A tesztkártya lejárt. Adj meg érvényes lejárati dátumot.']);
        }
        if (!$card['approved']) {
            throw ValidationException::withMessages(['payment_method' => 'A demófizetés elutasítva. Próbáld a sikeres tesztkártyák egyikét. Pénzlevonás nem történt.']);
        }

        return [
            'payment_status' => 'demo_approved',
            'card_brand' => $card['brand'],
            'card_last_four' => substr($number, -4),
            'payment_reference' => 'DEMO-'.Str::uuid(),
        ];
    }
}
