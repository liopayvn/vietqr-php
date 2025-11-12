<?php

declare(strict_types=1);

namespace Liopay\VietQR\Exception;

/**
 * Exception thrown when CRC checksum verification fails
 *
 * @package Liopay\VietQR\Exception
 */
class InvalidCRCException extends VietQRException {}
