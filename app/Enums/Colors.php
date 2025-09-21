<?php

namespace App\Constants;

class Colors
{
    // Status Colors
    public const STATUS_PENDING = '#FFA800';

    public const STATUS_APPROVED = '#50CD89';

    public const STATUS_REJECTED = '#F1416C';

    public const STATUS_INQUIRY = '#009EF7';

    public const STATUS_REQUEST_PRICE_OFFER = '#7239EA';

    // System States
    public const SYSTEM_CREATED = '#A1A5B7';

    public const READY_TO_SALE = '#50CD89';

    public const SOLD = '#F1416C';

    public const IN_MAINTENANCE = '#FFA800';

    public const PAID_PROCESSING = '#009EF7';

    // Sales Agreement Types
    public const NEW_APARTMENT_SALES = '#28a745';

    public const RESALE_APARTMENT = '#17a2b8';

    public const LEASE_TO_OWN = '#ffc107';

    public const INSTALLMENT_SALES = '#6610f2';

    public const DOWNPAYMENT_ONLY = '#20c997';

    public const DOWNPAYMENT_INSTALLMENT = '#fd7e14';

    public const DOWNPAYMENT_INSTALLMENT_BALLOON = '#6f42c1';

    public const FULL_PAYMENT = '#198754';

    // Sales Status
    public const PROCESSING = '#FFA800';

    public const SUBMITTED_TO_ACCOUNTED = '#198754';

    // Payment Status
    public const PARTIAL = '#3498DB';

    public const PAID = '#2ECC71';

    public const OVERDUE = '#E74C3C';

    // Payment Types
    public const DOWNPAYMENT = '#9B59B6';

    public const INSTALLMENT = '#34495E';

    public const BALLOON = '#16A085';

    // Payment Plan Status
    public const ACTIVE = '#00B74A';

    public const COMPLETED = '#1266F1';

    public const DEFAULTED = '#F93154';

    public const CANCELLED = '#757575';

    public static function getStatusColor(string $status): string
    {
        $colors = [
            'pending' => self::STATUS_PENDING,
            'approved' => self::STATUS_APPROVED,
            'rejected' => self::STATUS_REJECTED,
            'inquiry' => self::STATUS_INQUIRY,
            'request_for_price_offer' => self::STATUS_REQUEST_PRICE_OFFER,
            'created_by_system' => self::SYSTEM_CREATED,
            'ready_to_sale' => self::READY_TO_SALE,
            'sold' => self::SOLD,
            'in_maintenance' => self::IN_MAINTENANCE,
            'paid_processing' => self::PAID_PROCESSING,
            // Add all other mappings...
        ];

        return $colors[$status] ?? self::STATUS_PENDING;
    }

    public static function getPaymentStatusColor(string $status): string
    {
        $colors = [
            'partial' => self::PARTIAL,
            'paid' => self::PAID,
            'overdue' => self::OVERDUE,
            'active' => self::ACTIVE,
            'completed' => self::COMPLETED,
            'defaulted' => self::DEFAULTED,
            'cancelled' => self::CANCELLED,
        ];

        return $colors[$status] ?? self::STATUS_PENDING;
    }

    public static function getSalesAgreementColor(string $type): string
    {
        $colors = [
            'new_apartment_sales_agreement' => self::NEW_APARTMENT_SALES,
            'resale_apartment_agreement' => self::RESALE_APARTMENT,
            'lease_to_own_agreement' => self::LEASE_TO_OWN,
            'installment_sales_agreement' => self::INSTALLMENT_SALES,
            'downpayment_only' => self::DOWNPAYMENT_ONLY,
            'downpayment_installment' => self::DOWNPAYMENT_INSTALLMENT,
            'downpayment_installment_balloon' => self::DOWNPAYMENT_INSTALLMENT_BALLOON,
            'full_payment' => self::FULL_PAYMENT,
        ];

        return $colors[$type] ?? self::STATUS_PENDING;
    }
}
