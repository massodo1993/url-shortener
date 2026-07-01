<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sqids\Sqids;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Sqids::class, function () {
            return new Sqids(alphabet: $this->shuffledAlphabet(config('app.key')), minLength: 6);
        });
    }

    public function boot(): void
    {
        //
    }

    /**
     * Deterministic per-installation permutation of the Sqids alphabet, so short
     * codes can't be decoded by anyone using the library's default alphabet.
     */
    private function shuffledAlphabet(string $seed): string
    {
        $alphabet = str_split(Sqids::DEFAULT_ALPHABET);

        mt_srand(crc32($seed));

        for ($i = count($alphabet) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$alphabet[$i], $alphabet[$j]] = [$alphabet[$j], $alphabet[$i]];
        }

        mt_srand();

        return implode('', $alphabet);
    }
}
