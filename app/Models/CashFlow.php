<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'loan_id',
        'installment_id',
        'type',
        'category',
        'amount',
        'description',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'occurred_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CashFlow $cashFlow) {
            if ($cashFlow->type === 'out' && DB::transactionLevel() < 1) {
                throw ValidationException::withMessages([
                    'cash_flow' => 'Transaksi pengeluaran harus diproses dalam transaksi database.',
                ]);
            }

            $query = Account::query()
                ->whereKey($cashFlow->account_id);

            if ($cashFlow->type === 'out') {
                $query->lockForUpdate();
            }

            $account = $query->first();

            if (!$account) {
                throw ValidationException::withMessages([
                    'account_id' => 'Rekening tidak ditemukan.',
                ]);
            }

            if (!$account->is_active) {
                throw ValidationException::withMessages([
                    'account_id' => 'Rekening tidak aktif.',
                ]);
            }

            if ($account->opening_balance_initialized_at === null) {
                throw ValidationException::withMessages([
                    'account_id' => sprintf(
                        'Saldo awal rekening "%s" belum diinisialisasi. Transaksi belum dapat dilakukan.',
                        $account->name
                    ),
                ]);
            }

            if ($cashFlow->type !== 'out') {
                return;
            }

            $amountInCents = self::toCents($cashFlow->amount);

            if ($amountInCents <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Nominal pengeluaran harus lebih dari 0.',
                ]);
            }

            $availableBalance = $account->calculateBalance();

            $availableBalanceInCents = self::toCents(
                $availableBalance
            );

            if ($amountInCents > $availableBalanceInCents) {
                throw ValidationException::withMessages([
                    'account_id' => sprintf(
                        'Saldo rekening "%s" tidak mencukupi. Saldo tersedia Rp %s.',
                        $account->name,
                        number_format(
                            $availableBalance,
                            0,
                            ',',
                            '.'
                        )
                    ),
                ]);
            }
        });

        static::updating(function () {
            throw ValidationException::withMessages([
                'cash_flow' => 'Transaksi arus kas yang sudah tercatat tidak dapat diubah.',
            ]);
        });

        static::deleting(function () {
            throw ValidationException::withMessages([
                'cash_flow' => 'Transaksi arus kas yang sudah tercatat tidak dapat dihapus.',
            ]);
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    private static function toCents(mixed $value): int
    {
        $normalized = trim((string) $value);

        $negative = str_starts_with($normalized, '-');

        $normalized = ltrim(
            $normalized,
            '+-'
        );

        $parts = explode(
            '.',
            $normalized,
            2
        );

        $whole = preg_replace(
            '/\D/',
            '',
            $parts[0] ?? '0'
        );

        $decimal = preg_replace(
            '/\D/',
            '',
            $parts[1] ?? '0'
        );

        $whole = $whole !== ''
            ? $whole
            : '0';

        $decimal = substr(
            str_pad(
                $decimal,
                2,
                '0'
            ),
            0,
            2
        );

        $cents =
            ((int) $whole * 100)
            + (int) $decimal;

        return $negative
            ? -$cents
            : $cents;
    }
}
