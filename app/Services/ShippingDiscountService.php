<?php

namespace App\Services;

class ShippingDiscountService
{
    public const GROUP_JABAR_JABODETABEK_BANTEN = 'jabar_jabodetabek_banten';
    public const GROUP_JATENG_DIY_JATIM = 'jateng_diy_jatim';
    public const GROUP_OTHER = 'other';

    /**
     * Detect region group from area name and optional province/city details.
     */
    public function detectRegionGroup(string $areaName, ?string $provinceName = null, ?string $cityName = null): string
    {
        $searchString = strtolower(trim("{$areaName} {$provinceName} {$cityName}"));

        if (empty($searchString)) {
            return self::GROUP_OTHER;
        }

        // 1. Group 1: Jawa Barat, Jabodetabek, dan Banten
        $group1Keywords = [
            'jawa barat', 'west java', 'dki jakarta', 'jakarta', 'banten',
            'bogor', 'depok', 'tangerang', 'bekasi', 'kepulauan seribu',
            'bandung', 'cimahi', 'cirebon', 'sukabumi', 'tasikmalaya', 'garut',
            'karawang', 'purwakarta', 'subang', 'indramayu', 'majalengka',
            'kuningan', 'ciamis', 'pangandaran', 'banjar', 'sumedang', 'cianjur',
            'serang', 'cilegon', 'lebak', 'pandeglang', 'tangsel',
        ];

        foreach ($group1Keywords as $kw) {
            if (str_contains($searchString, $kw)) {
                return self::GROUP_JABAR_JABODETABEK_BANTEN;
            }
        }

        // 2. Group 2: Jawa Tengah, DI Yogyakarta, dan Jawa Timur
        $group2Keywords = [
            'jawa tengah', 'central java', 'di yogyakarta', 'daerah istimewa yogyakarta',
            'd.i. yogyakarta', 'yogyakarta', 'jogja', 'yogya', 'jawa timur', 'east java',
            'semarang', 'surakarta', 'solo', 'magelang', 'pekalongan', 'salatiga', 'tegal',
            'banyumas', 'purwokerto', 'cilacap', 'kudus', 'jepara', 'pati', 'klaten',
            'boyolali', 'kebumen', 'kendal', 'brebes', 'pemalang', 'batang', 'blora',
            'demak', 'grobogan', 'karanganyar', 'purbalingga', 'purworejo', 'rembang',
            'sragen', 'sukoharjo', 'temanggung', 'wonogiri', 'wonosobo',
            'sleman', 'bantul', 'kulon progo', 'gunungkidul', 'gunung kidul',
            'surabaya', 'malang', 'sidoarjo', 'gresik', 'kediri', 'blitar', 'madiun',
            'mojokerto', 'pasuruan', 'probolinggo', 'banyuwangi', 'jember', 'batu',
            'bojonegoro', 'bondowoso', 'jombang', 'lamongan', 'lumajang', 'magetan',
            'nganjuk', 'ngawi', 'pacitan', 'pamekasan', 'ponorogo', 'sampang',
            'situbondo', 'sumenep', 'trenggalek', 'tuban', 'tulungagung', 'bangkalan',
        ];

        foreach ($group2Keywords as $kw) {
            if (str_contains($searchString, $kw)) {
                return self::GROUP_JATENG_DIY_JATIM;
            }
        }

        return self::GROUP_OTHER;
    }

    /**
     * Calculate shipping discount based on total pack count, shipping fee, and destination.
     *
     * @return array{
     *     discount_amount: float,
     *     final_shipping_fee: float,
     *     group: string,
     *     group_label: string,
     *     rule_label: string,
     *     is_free_shipping: bool,
     *     item_count: int,
     *     promo_message: string
     * }
     */
    public function calculateDiscount(
        int $itemCount,
        float $shippingFee,
        string $areaName = '',
        ?string $provinceName = null,
        ?string $cityName = null
    ): array {
        $shippingFee = max(0.0, $shippingFee);
        $itemCount = max(0, $itemCount);
        $group = $this->detectRegionGroup($areaName, $provinceName, $cityName);

        $discountAmount = 0.0;
        $groupLabel = 'Wilayah Lainnya';
        $ruleLabel = 'Ongkir Normal';
        $isFreeShipping = false;
        $promoMessage = '';

        if ($group === self::GROUP_JABAR_JABODETABEK_BANTEN) {
            $groupLabel = 'Jawa Barat, Jabodetabek & Banten';

            if ($itemCount === 1) {
                $discountAmount = min($shippingFee, 5000.0);
                $ruleLabel = 'Subsidi Ongkir Rp 5.000 (1 Bungkus)';
                $promoMessage = 'Hemat Rp 5.000! Tambah 1 bungkus lagi untuk diskon ongkir Rp 10.000 atau beli ≥3 bungkus untuk Gratis Ongkir.';
            } elseif ($itemCount === 2) {
                $discountAmount = min($shippingFee, 10000.0);
                $ruleLabel = 'Subsidi Ongkir Rp 10.000 (2 Bungkus)';
                $promoMessage = 'Hemat Rp 10.000! Tambah 1 bungkus lagi untuk mendapatkan GRATIS ONGKIR!';
            } elseif ($itemCount > 2) {
                $discountAmount = $shippingFee;
                $isFreeShipping = true;
                $ruleLabel = 'Gratis Ongkir Otomatis (>2 Bungkus)';
                $promoMessage = 'Selamat! Anda mendapatkan GRATIS ONGKIR otomatis untuk pembelian >2 bungkus kopi.';
            }
        } elseif ($group === self::GROUP_JATENG_DIY_JATIM) {
            $groupLabel = 'Jawa Tengah, DIY & Jawa Timur';

            if ($itemCount === 1) {
                $discountAmount = 0.0;
                $ruleLabel = 'Ongkir Normal (1 Bungkus)';
                $promoMessage = 'Beli 2 bungkus untuk diskon Rp 5.000, 3 bungkus diskon Rp 10.000, atau ≥4 bungkus untuk Gratis Ongkir.';
            } elseif ($itemCount === 2) {
                $discountAmount = min($shippingFee, 5000.0);
                $ruleLabel = 'Subsidi Ongkir Rp 5.000 (2 Bungkus)';
                $promoMessage = 'Hemat Rp 5.000! Tambah 1 bungkus lagi untuk diskon Rp 10.000 atau ≥4 bungkus untuk Gratis Ongkir.';
            } elseif ($itemCount === 3) {
                $discountAmount = min($shippingFee, 10000.0);
                $ruleLabel = 'Subsidi Ongkir Rp 10.000 (3 Bungkus)';
                $promoMessage = 'Hemat Rp 10.000! Tambah 1 bungkus lagi untuk mendapatkan GRATIS ONGKIR!';
            } elseif ($itemCount > 3) {
                $discountAmount = $shippingFee;
                $isFreeShipping = true;
                $ruleLabel = 'Gratis Ongkir Otomatis (>3 Bungkus)';
                $promoMessage = 'Selamat! Anda mendapatkan GRATIS ONGKIR otomatis untuk pembelian >3 bungkus kopi.';
            }
        } else {
            $groupLabel = 'Luar Pulau Jawa / Wilayah Lainnya';
            $ruleLabel = 'Ongkir Normal';
            $discountAmount = 0.0;
            $promoMessage = 'Tarif ongkos kirim standar kurir.';
        }

        $finalShippingFee = max(0.0, $shippingFee - $discountAmount);

        return [
            'discount_amount' => $discountAmount,
            'final_shipping_fee' => $finalShippingFee,
            'group' => $group,
            'group_label' => $groupLabel,
            'rule_label' => $ruleLabel,
            'is_free_shipping' => $isFreeShipping,
            'item_count' => $itemCount,
            'promo_message' => $promoMessage,
        ];
    }
}
