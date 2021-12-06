<?php

namespace Botble\SocialLogin\Http\Controllers;

use Assets;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Supports\SettingStore;
use Botble\SocialLogin\Http\Requests\SocialLoginRequest;
use Exception;
use Illuminate\Support\Str;
use RvMedia;
use Socialite;

class SocialLoginController extends BaseController
{

    /**
     * Redirect the user to the {provider} authentication page.
     *
     * @param string $provider
     * @return mixed
     */
    public function redirectToProvider($provider)
    {
        $this->setProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * @param string $provider
     */
    protected function setProvider(string $provider)
    {
        config()->set([
            'services.' . $provider => [
                'client_id'     => setting('social_login_' . $provider . '_app_id'),
                'client_secret' => setting('social_login_' . $provider . '_app_secret'),
                'redirect'      => route('auth.social.callback', $provider),
            ],
        ]);

        return true;
    }

    /**
     * Obtain the user information from {provider}.
     * @param string $provider
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function handleProviderCallback($provider, BaseHttpResponse $response)
    {
        $this->setProvider($provider);

        try {
            /**
             * @var \Laravel\Socialite\AbstractUser $oAuth
             */
            $oAuth = Socialite::driver($provider)->user();
        } catch (Exception $ex) {
            return $response
                ->setError()
                ->setNextUrl(route('public.account.login'))
                ->setMessage($ex->getMessage());
        }

        if (!$oAuth->getEmail()) {
            return $response
                ->setError()
                ->setNextUrl(route('public.account.login'))
                ->setMessage(__('Cannot login, no email provided!'));
        }

        $user = app(AccountInterface::class)->getFirstBy(['email' => $oAuth->getEmail()]);

        if (!$user) {
            $firstName = implode(' ', explode(' ', $oAuth->getName(), -1));

            $avatarId = null;
            try {
                $url = $oAuth->getAvatar();
                if ($url) {
                    $info = pathinfo($url);
                    $contents = file_get_contents($url);
                    $file = '/tmp/' . $info['basename'];
                    file_put_contents($file, $contents);
                    $fileUpload = new UploadedFile($file, Str::slug($oAuth->getName()) . '.png', 'image/png', null,
                        true);
                    $result = RvMedia::handleUpload($fileUpload, 0, 'accounts');
                    if (!$result['error']) {
                        $avatarId = $result['data']->id;
                    }
                }
            } catch (Exception $exception) {
                info($exception->getMessage());
            }

            $user = app(AccountInterface::class)->createOrUpdate([
                'first_name' => $firstName,
                'last_name'  => trim(str_replace($firstName, '', $oAuth->getName())),
                'email'      => $oAuth->getEmail(),
                'password'   => bcrypt(Str::random(36)),
                'avatar_id'  => $avatarId,
            ]);

            $user->confirmed_at = now();
            $user->save();
        }

        Auth::guard('account')->login($user, true);

        return $response
            ->setNextUrl(route('public.account.dashboard'))
            ->setMessage(trans('core/acl::auth.login.success'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSettings()
    {
        page_title()->setTitle(trans('plugins/social-login::social-login.settings.title'));

        Assets::addScriptsDirectly('vendor/core/plugins/social-login/js/social-login.js');

        return view('plugins/social-login::settings');
    }

    /**
     * @param SocialLoginRequest $request
     * @param BaseHttpResponse $response
     * @param SettingStore $setting
     * @return BaseHttpResponse
     */
    public function postSettings(SocialLoginRequest $request, BaseHttpResponse $response, SettingStore $setting)
    {
        foreach ($request->except(['_token']) as $settingKey => $settingValue) {
            $setting->set($settingKey, $settingValue);
        }

        $setting->save();

        return $response
            ->setPreviousUrl(route('social-login.settings'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
}
