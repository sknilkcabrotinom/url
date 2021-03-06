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
namespace League\Url;

use InvalidArgumentException;
use League\Url\Interfaces;
use League\Url\Utilities;

/**
 * An abstract class to ease component manipulation
 *
 * @package League.url
 * @since  4.0.0
 */
class Component implements Interfaces\Component
{
    /**
     * Trait for ComponentTrait method
     */
    use Utilities\ComponentTrait;

    /**
     * The component data
     *
     * @var string
     */
    protected $data;

    /**
     * new instance
     *
     * @param string $data the component value
     */
    public function __construct($data = null)
    {
        $data = $this->validateString($data);
        if (! empty($data)) {
            $this->data = $this->validate($data);
        }
    }

    /**
     * validate the incoming data
     *
     * @param  string $data
     *
     * @return string
     */
    protected function validate($data)
    {
        $data = filter_var($data, FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_STRIP_LOW]);

        return rawurldecode(trim($data));
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->encode($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function getUriComponent()
    {
        return $this->__toString();
    }
}
