<?php

namespace MvcLTE\Contracts\Captcha;

interface ManagerInterface
{
    /**
     * Get a captcha instance.
     *
     * @param string|null $Name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function captcha(string $Name = null);

    /**
     * Reset to the given captcha.
     *
     * @param string|null $Name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function reset(string $Name = null);

    /**
     * Remove a captcha from the captchas.
     *
     * @param string|null $Name
     *
     * @return void
     */
    public function remove(string $Name = null);                    
}