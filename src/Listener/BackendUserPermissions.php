<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Listener;

use Alpdesk\AlpdeskFrontendediting\Utils\Utils;
use Contao\BackendUser;
use Contao\Environment;
use Contao\PageModel;
use Contao\StringUtil;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BackendUserPermissions
{
    private HttpClientInterface $httpClient;
    private ?string $url = null;

    private ?BackendUser $backendUser = null;

    private bool $isAdmin = false;
    private bool $isAdminDisabled = false;

    public function __construct()
    {
        $this->httpClient = HttpClient::create(
            (new HttpOptions())
                ->verifyHost(false)
                ->verifyPeer(false)
                ->toArray()
        );

    }

    /**
     * @param string $method
     * @param float $timeout
     * @param bool $decodeEntities
     * @param array $content
     * @param array $validStatusCodes
     * @return mixed
     * @throws \Exception
     */
    private function callHttp(string $method, float $timeout, bool $decodeEntities = false, array $content = [], array $validStatusCodes = [200]): mixed
    {
        try {

            if ($this->url === null) {
                throw new \Exception('invalid url');
            }

            $content['timeout'] = $timeout;

            $url = $this->url;
            if ($decodeEntities === true) {
                $url = StringUtil::decodeEntities($this->url);
            }

            $response = $this->httpClient->request($method, $url, $content);

            if (!\in_array($response->getStatusCode(), $validStatusCodes, true)) {
                throw new \Exception('invalid Statuscode ' . $response->getStatusCode());
            }

            if ($response->getContent() === null) {
                throw new \Exception('error sending data. ResponseCode: null');
            }

            $response = \json_decode($response->getContent(), true);

            if ($response === null) {
                throw new \Exception('invalid responseData');
            }

            return $response;

        } catch (\Throwable $ex) {
            throw new \Exception($ex->getMessage());
        }

    }

    /**
     * @return void
     * @throws \Exception
     */
    public function init(): void
    {
        try {

            $backendUser = BackendUser::getInstance();
            if (!$backendUser instanceof BackendUser) {
                throw new \Exception('invalid Usertype');
            }

            $this->backendUser = $backendUser;

            Utils::mergeUserGroupPersmissions($this->backendUser);

            $this->setIsAdmin($this->backendUser->isAdmin);

            if (
                $this->backendUser->alpdesk_fee_admin_disabled !== null &&
                (int)$this->backendUser->alpdesk_fee_admin_disabled === 1
            ) {
                $this->setIsAdminDisabled(true);
            }

            /*$this->url = Environment::get('base') . '/contao/alpdeskfeepermissions';

            $permissionsResponse = $this->callHttp('POST', 5.5, false, [
                'type' => 'global'
            ]);

            if (\is_array($permissionsResponse) && \count($permissionsResponse) > 0) {

                if (\array_key_exists('isAdmin', $permissionsResponse) && \is_bool($permissionsResponse['isAdmin']) && $permissionsResponse['isAdmin'] === true) {
                    $this->setIsAdmin(true);
                }

                if (\array_key_exists('isAdminDisabled ', $permissionsResponse) && \is_bool($permissionsResponse['isAdminDisabled ']) && $permissionsResponse['isAdminDisabled '] === true) {
                    $this->setIsAdminDisabled(true);
                }

            }*/

        } catch (\Throwable $tr) {
            throw new \Exception($tr->getMessage());
        }

    }

    /**
     * @param string|null $field
     * @param string|null $method
     * @return bool
     */
    public function hasAccess(?string $field, ?string $method): bool
    {

        if ($field === null || $method === null) {
            return false;
        }

        if ($this->backendUser !== null) {
            return $this->backendUser->hasAccess($field, $method);
        }

        try {

            return $this->callHttp('POST', 5.5, false, [
                'type' => 'hasAccess',
                'field' => $field,
                'method' => $method
            ]);

        } catch (\Throwable $tr) {
            return false;
        }

    }

    public function hasPageMountAccess(PageModel $objPage): bool
    {
        if ($this->backendUser === null) {
            return false;
        }

        return Utils::hasPageMountAccess($objPage, $this->backendUser);
    }

    public function isAllowed(int $int, mixed $row): bool
    {
        if ($this->backendUser === null) {
            return false;
        }

        return Utils::isAllowed($int, $row, $this->backendUser);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return bool
     */
    public function isAdminDisabled(): bool
    {
        return $this->isAdminDisabled;
    }

    /**
     * @param bool $isAdminDisabled
     */
    public function setIsAdminDisabled(bool $isAdminDisabled): void
    {
        $this->isAdminDisabled = $isAdminDisabled;
    }

}