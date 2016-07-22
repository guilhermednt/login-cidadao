<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LoginCidadao\CoreBundle\Service;

use Symfony\Component\HttpFoundation\Request;

class IntentManager
{
    const SESSION_INTENT_KEY = 'final.intent';

    /**
     * @param Request $request
     */
    public function setIntent(Request $request)
    {
        if ($request->hasSession() && $request->isMethodSafe() && !$request->isXmlHttpRequest()) {
            $request->getSession()->set(self::SESSION_INTENT_KEY, $request->getUri());
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getIntent(Request $request)
    {
        if ($request->hasSession()) {
            return $request->getSession()->get(self::SESSION_INTENT_KEY);
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    public function consumeIntent(Request $request)
    {
        $intent = $this->getIntent($request);
        if ($request->hasSession()) {
            $request->getSession()->remove(self::SESSION_INTENT_KEY);
        }

        return $intent;
    }
}
