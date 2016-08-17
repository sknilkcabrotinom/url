<?php
/**
 * This file is part of the League.url library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/thephpleague/url/
 * @version 4.0.0
 * @package League.url
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace League\Url\Utilities;

use InvalidArgumentException;
use League\Url\Interfaces;

/**
 * A Trait to validate a Host component
 *
 * @package League.url
 * @since 4.0.0
 */
trait HostValidator
{
    /**
     * CollectionComponent delimiter
     *
     * @var string
     */
    protected static $delimiter = '.';

    /**
     * Is the Host an IPv4
     * @var bool
     */
    protected $host_as_ipv4 = false;

    /**
     * Is the Host an IPv6
     * @var bool
     */
    protected $host_as_ipv6 = false;

    /**
     * Trait to handle punycode
     */
    use Punycode;

    /**
     * {@inheritdoc}
     */
    public function isIp()
    {
        return $this->host_as_ipv4 || $this->host_as_ipv6;
    }

    /**
     * {@inheritdoc}
     */
    public function isIpv4()
    {
        return $this->host_as_ipv4;
    }

    /**
     * {@inheritdoc}
     */
    public function isIpv6()
    {
        return $this->host_as_ipv6;
    }

    /**
     * Validate a Host as an IP
     *
     * @param string $str
     *
     * @throws \InvalidArgumentException if the IP based host is malformed
     *
     * @return array
     */
    protected function validateIpHost($str)
    {
        $res = $this->filterIpv6Host($str);
        if (! empty($res)) {
            $this->host_as_ipv4 = false;
            $this->host_as_ipv6 = true;
            return [$res];
        }

        if (filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $this->host_as_ipv4 = true;
            $this->host_as_ipv6 = false;
            return [$str];
        }

        $this->host_as_ipv4 = false;
        $this->host_as_ipv6 = false;
        return [];
    }

    /**
     * validate and filter a Ipv6 Hostname
     *
     * @param string $str
     *
     * @return string
     *
     * @throws \InvalidArgumentException If malformed IPV6 format
     */
    protected function filterIpv6Host($str)
    {
        if (preg_match(',^\[(.*)\]$,', $str, $matches)) {
            if (! filter_var($matches[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                throw new InvalidArgumentException('Invalid IPV6 format');
            }
            return $matches[1];
        }

        return filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * Validate a string only host
     *
     * @param  string $str
     *
     * @throws \InvalidArgumentException If the string failed to be a valid hostname
     *
     * @return array
     */
    protected function validateStringHost($str)
    {
        $str = $this->setIsAbsolute($str);
        $labels = array_map(function ($value) {
            $value = filter_var($value, FILTER_UNSAFE_RAW, ["flags" => FILTER_FLAG_STRIP_LOW]);

            return $this->encodeLabel(trim($value));
        }, explode(static::$delimiter, $this->lower($str)));

        $this->assertValidHost($labels);

        return array_map(function ($label) {
            return $this->decodeLabel($label);
        }, $labels);
    }

    /**
     * set the FQDN property
     *
     * @param string $str
     *
     * @return string
     */
    abstract protected function setIsAbsolute($str);

    /**
     * Convert to lowercase a string without modifying unicode characters
     *
     * @param  string $str
     *
     * @return string
     */
    protected function lower($str)
    {
        $res = [];
        for ($i = 0, $length = mb_strlen($str, 'UTF-8'); $i < $length; $i++) {
            $char = mb_substr($str, $i, 1, 'UTF-8');
            if (ord($char) < 128) {
                $char = strtolower($char);
            }
            $res[] = $char;
        }

        return implode('', $res);
    }

    /**
     * Validate a String Label
     *
     * @param array $labels found host labels
     *
     * @throws \InvalidArgumentException If the validation fails
     */
    protected function assertValidHost(array $labels)
    {
        $verifs = array_filter($labels, function ($value) {
            return !empty($value) || $value == '0';
        });

        if ($verifs != $labels) {
            throw new InvalidArgumentException('Invalid Hostname, verify labels');
        }

        $this->isValidLength($labels);
        $this->isValidLabelsCount($labels);
        $this->isValidContent($labels);
    }

    /**
     * Validate Host label length
     *
     * @param  array $data Host labels
     *
     * @throws \InvalidArgumentException If the validation fails
     */
    protected function isValidLength(array $data)
    {
        $res = array_filter($data, function ($label) {
            return strlen($label) > 63;
        });

        if (! empty($res)) {
            throw new InvalidArgumentException('Invalid Hostname, verify its length');
        }
    }

    /**
     * Validated the Host Label Pattern
     *
     * @param  array $data Host CollectionComponent
     *
     * @throws \InvalidArgumentException If the validation fails
     */
    protected function isValidContent(array $data)
    {
        if ($this->isIp()) {
            $pattern = '/^[0-9a-z]([0-9a-z-]{0,61}[0-9a-z])?$/i';
        } else {
            if (count($data) == 1) {
                throw new InvalidArgumentException('Invalid Domain, verify its content');
            }
            $pattern = '/^[0-9a-z]([0-9a-z-_]{0,61}[0-9a-z])?$/i';
        }
        $res = preg_grep($pattern, $data, PREG_GREP_INVERT);
        
        if (! empty($res)) {
            throw new InvalidArgumentException('Invalid Hostname, verify its content');
        }
    }

    /**
     * Validated the Host Label Count
     *
     * @param  array $data Host CollectionComponent
     *
     * @throws \InvalidArgumentException If the validation fails
     */
    abstract protected function isValidLabelsCount(array $data = []);
}
