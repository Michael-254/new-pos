<?php

namespace Database\Seeders;

use App\Models\MpesaCredential;
use Illuminate\Database\Seeder;

class MpesaCredentialTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $credential = MpesaCredential::create([
            'admin_id' => 1,
            'consumer_key' => 'MPESA_CONSUMER_KEY',
            'consumer_secret' => 'MPESA_CONSUMER_SECRET',
            'test_consumer_key' => 'YgreYNuYW5xIVooFrZgIhQMvvBGr2Pe2',
            'test_consumer_secret' => '699oGg0C1XdMoWOA',
            'environment' => 'sandbox',
            'shortcode' => '174379',
            'security_credential' => 'RHHsFx9loY3TtMm7RYJA91YIT9C1wFIULPyD2m+chN27Pnj+NyA2nQHguT4C3ESySnBiuvs3iyQW96eH9x0sggTvDnx/gBu9ro3YKFFBMoZ7YyVVFGJo2QbP3pn5FNz9lGyEhfdS+GzXTT/xprYUcscnxuRKbtlEhh6Ebq/kzGOoCwZK3pguJaCOgGAR9O/QqlNqPmUtG382Mm/DUC8wR1qF/CzuqRCrXKlH1GaZIi7kjBVVTFBsAAs4FyGEuMMMiE3FZQGSxjoTGAS53T/aEKCWcHFZvon0SWJpKyy1kWcfEpdIrdquSwh937dYBrWoFF//IG8uUb7Hoi6eZ9mLww==',
            'lipa_na_mpesa_passkey' => 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'
        ]);
    }
}
