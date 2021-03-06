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

use League\Url\Interfaces;

/**
 * Value object representing the UserInfo part of an URL.
 *
 * @package League.url
 * @since 4.0.0
 *
 * @property-read Interfaces\Component $user
 * @property-read Interfaces\Component $pass
 */
class UserInfo implements Interfaces\UserInfo
{
    /**
     * User Component
     *
     * @var User
     */
    protected $user;

    /**
     * Pass Component
     *
     * @var Pass
     */
    protected $pass;

    /**
     * Trait To get/set immutable value property
     */
    use Utilities\ImmutableProperty;

    /**
     * Create a new instance of UserInfo
     *
     * @param string $user
     * @param string $pass
     */
    public function __construct($user = null, $pass = null)
    {
        $this->user = new Component($user);
        $this->pass = new Component($pass);
        $this->cleanUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function cleanUp()
    {
        if (! $this->pass->isEmpty() && $this->user->isEmpty()) {
            $this->pass = $this->pass->modify(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->user->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getPass()
    {
        return $this->pass->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'user' => $this->user->isEmpty() ? null : $this->user->__toString(),
            'pass' => $this->pass->isEmpty() ? null : $this->pass->__toString(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if ($this->user->isEmpty()) {
            return '';
        }

        return $this->user->__toString().':'.$this->pass->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getUriComponent()
    {
        $info = $this->__toString();
        if (! empty($info)) {
            $info .= '@';
        }

        return $info;
    }

    /**
     * {@inheritdoc}
     */
    public function sameValueAs(Interfaces\UrlPart $component)
    {
        return $this->getUriComponent() === $component->getUriComponent();
    }

    /**
     * {@inheritdoc}
     */
    public function withUser($user)
    {
        return $this->withProperty('user', $user);
    }

    /**
     * {@inheritdoc}
     */
    public function withPass($pass)
    {
        return $this->withProperty('pass', $pass);
    }
}
