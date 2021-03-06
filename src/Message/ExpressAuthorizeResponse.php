<?php

namespace Omnipay\Euplatesc\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Euplatesc.ro Express Authorize Response
 */
class ExpressAuthorizeResponse extends Response implements RedirectResponseInterface
{
    protected $liveCheckoutEndpoint = 'https://www.paypal.com/webscr';
    protected $testCheckoutEndpoint = 'https://www.sandbox.paypal.com/webscr';

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return isset($this->data['ACK']) && in_array($this->data['ACK'], array('Success', 'SuccessWithWarning'));
    }

    public function getRedirectUrl()
    {
        $query = array(
            'cmd' => '_express-checkout',
            'useraction' => 'commit',
            'token' => $this->getTransactionReference(),
        );

        return $this->getCheckoutEndpoint().'?'.http_build_query($query, '', '&');
    }

    public function getTransactionReference()
    {
        return isset($this->data['TOKEN']) ? $this->data['TOKEN'] : null;
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return null;
    }

    protected function getCheckoutEndpoint()
    {
        return $this->getRequest()->getTestMode() ? $this->testCheckoutEndpoint : $this->liveCheckoutEndpoint;
    }
}
