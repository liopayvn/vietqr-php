<?php

declare(strict_types=1);

namespace Liopay\VietQR\Constant;

/**
 * Merchant Category Code (MCC) constants
 *
 * List of allowed MCCs according to NAPAS specification
 *
 * @package Liopay\VietQR\Constant
 */
final class MCCCodes
{
    // Allowed MCC codes from specification
    public const COMMERCIAL_FOOTWEAR = '5139';
    public const BOOKS_PERIODICALS_NEWSPAPERS = '5192';
    public const GLASS_PAINT_WALLPAPER = '5231';
    public const SUPERMARKETS_GROCERY = '5411';
    public const MENS_BOYS_CLOTHING = '5611';
    public const WOMENS_READY_TO_WEAR = '5621';
    public const WOMENS_ACCESSORY = '5631';
    public const CHILDRENS_INFANTS_WEAR = '5641';
    public const FAMILY_CLOTHING = '5651';
    public const SHOE_STORES = '5661';
    public const MENS_WOMENS_CLOTHING = '5691';
    public const COMPUTER_SOFTWARE = '5734';
    public const EATING_PLACES_RESTAURANTS = '5812';
    public const DRINKING_PLACES_BARS = '5813';
    public const FAST_FOOD_RESTAURANTS = '5814';
    public const DRUG_STORES_PHARMACIES = '5912';
    public const PACKAGE_STORES_LIQUOR = '5921';
    public const ANTIQUE_SHOPS = '5832';
    public const BICYCLE_SHOPS = '5940';
    public const SPORTING_GOODS = '5941';
    public const BOOK_STORES = '5942';
    public const STATIONERY_OFFICE_SUPPLIES = '5943';
    public const JEWELRY_STORES = '5944';
    public const HOBBY_TOY_GAME_SHOPS = '5945';
    public const CAMERA_PHOTO_SUPPLY = '5946';
    public const GIFT_CARD_NOVELTY_SHOPS = '5947';
    public const COSMETIC_STORES = '5977';
    public const FLORISTS = '5992';
    public const PET_SHOPS = '5995';
    public const FINANCIAL_INSTITUTIONS = '6011';
    public const LODGING_HOTELS_MOTELS = '7011';
    public const LAUNDRY_SERVICES = '7211';
    public const DRY_CLEANERS = '7216';
    public const HEALTH_BEAUTY_SHOPS = '7298';
    public const HOSPITALS = '8062';

    /**
     * Get all allowed MCC codes
     *
     * @return array<string>
     */
    public static function getAll(): array
    {
        return [
            self::COMMERCIAL_FOOTWEAR,
            self::BOOKS_PERIODICALS_NEWSPAPERS,
            self::GLASS_PAINT_WALLPAPER,
            self::SUPERMARKETS_GROCERY,
            self::MENS_BOYS_CLOTHING,
            self::WOMENS_READY_TO_WEAR,
            self::WOMENS_ACCESSORY,
            self::CHILDRENS_INFANTS_WEAR,
            self::FAMILY_CLOTHING,
            self::SHOE_STORES,
            self::MENS_WOMENS_CLOTHING,
            self::COMPUTER_SOFTWARE,
            self::EATING_PLACES_RESTAURANTS,
            self::DRINKING_PLACES_BARS,
            self::FAST_FOOD_RESTAURANTS,
            self::DRUG_STORES_PHARMACIES,
            self::PACKAGE_STORES_LIQUOR,
            self::ANTIQUE_SHOPS,
            self::BICYCLE_SHOPS,
            self::SPORTING_GOODS,
            self::BOOK_STORES,
            self::STATIONERY_OFFICE_SUPPLIES,
            self::JEWELRY_STORES,
            self::HOBBY_TOY_GAME_SHOPS,
            self::CAMERA_PHOTO_SUPPLY,
            self::GIFT_CARD_NOVELTY_SHOPS,
            self::COSMETIC_STORES,
            self::FLORISTS,
            self::PET_SHOPS,
            self::FINANCIAL_INSTITUTIONS,
            self::LODGING_HOTELS_MOTELS,
            self::LAUNDRY_SERVICES,
            self::DRY_CLEANERS,
            self::HEALTH_BEAUTY_SHOPS,
            self::HOSPITALS,
        ];
    }

    /**
     * Check if MCC code is valid
     *
     * @param string $mcc MCC code to check
     * @return bool
     */
    public static function isValid(string $mcc): bool
    {
        return in_array($mcc, self::getAll(), true);
    }
}
